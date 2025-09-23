<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Address;
use App\Models\AddressType;
use App\Models\Document;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
   public function index(Request $request)
    {
        $q   = $request->input('q');
        $ref = $request->input('ref');

        $members = Member::filter($q, $ref)   
                     ->paginate(10)      
                     ->withQueryString(); 

        return view('members.index', compact('members','q','ref'));
    }

    public function create()
    {
        $addressTypes = AddressType::where('status', true)->orderBy('name')->get();
        return view('members.create', compact('addressTypes'));
    }


    public function show(Member $member)
    {
        $member->load(['addresses.type', 'documents', 'referrer']);
        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $member->load(['addresses.type', 'documents']);
        $addressTypes = AddressType::where('status', true)->orderBy('name')->get();
        return view('members.edit', compact('member','addressTypes'));
    }


    public function store(StoreMemberRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data, $request) {
            // Find referrer_id if provided
            $referrerId = null;
            if (!empty($data['referral_code_input'])) {
                $referrerId = Member::where('referral_code', $data['referral_code_input'])->value('id');
            }

            $member = Member::create([
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'referral_code' => $this->uniqueReferralCode(),
                'referrer_id'   => $referrerId,
            ]);

            // Addresses
            foreach (($data['addresses'] ?? []) as $addr) {
                $addr['member_id'] = $member->id;
                $addr['country'] = $addr['country'] ?? 'MY';
                $address = Address::create($addr);

                // Proof-of-address document attaches to the address
                if ($request->hasFile('proof_of_address')) {
                    $this->replaceDocument($address, $request->file('proof_of_address'), 'proof');
                }
            }

            // Profile image attaches to the member
            if ($request->hasFile('profile_image')) {
                $this->replaceDocument($member, $request->file('profile_image'), 'profile');
            }

            return to_route('members.show', $member)->with('ok','Member created.');
        });
    }


    public function update(UpdateMemberRequest $request, Member $member)
    {
        $data = $request->validated();
        
        
        return DB::transaction(function () use ($member, $data, $request) {
            $member->update([
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ]);

            // Sync addresses (simple replace-or-update approach)
            $keptIds = [];
            foreach (($data['addresses'] ?? []) as $addr) {
                if (!empty($addr['id'])) {
                    $address = Address::where('member_id', $member->id)->findOrFail($addr['id']);
                    $address->update($addr);
                    $keptIds[] = $address->id;
                } else {
                    $addr['member_id'] = $member->id;
                    $addr['country'] = $addr['country'] ?? 'MY';
                    $address = Address::create($addr);
                    $keptIds[] = $address->id;
                }
            }
            Address::where('member_id',$member->id)->whereNotIn('id',$keptIds)->delete();

            // Files (optional re-upload)
            if ($request->hasFile('profile_image')) {
                $this->replaceDocument($member, $request->file('profile_image'), 'profile');
            }
            if ($request->hasFile('proof_of_address')) {
                // attach proof to first address (or create a default if none)
                $address = $member->addresses()->first() ?? $member->addresses()->create([
                    'address_type_id' => AddressType::where('status',true)->value('id'),
                    'line1' => 'N/A', 'city' => 'N/A', 'country' => 'MY'
                ]);
                $this->replaceDocument($address, $request->file('proof_of_address'), 'proof');
            }

            return to_route('members.show', $member)->with('ok','Member updated.');
        });
    }

    public function destroy(Member $member)
    {
        //$member->delete();
        return to_route('members.index')->with('ok','Member deleted.');
    }

    public function export(Request $request): StreamedResponse
    {
        // Reuse list filters
        $q = $request->string('q')->toString();
        $ref = $request->string('ref')->toString();

        $rows = Member::query()
            ->with(['referrer'])
            ->when($q, fn($query)=>$query->where(fn($w)=>$w->where('name','like',"%{$q}%")->orWhere('email','like',"%{$q}%")))
            ->when($ref, fn($query)=>$query->where('referral_code','like',"%{$ref}%")
                ->orWhereHas('referrer', fn($r)=>$r->where('name','like',"%{$ref}%")
                    ->orWhere('email','like',"%{$ref}%")->orWhere('referral_code','like',"%{$ref}%")))
            ->orderBy('id')
            ->get();

        $headers = ['ID','Name','Email','Phone','Referral Code','Referrer Email','Created At'];

        return response()->streamDownload(function() use ($rows, $headers) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($rows as $m) {
                fputcsv($out, [
                    $m->id, $m->name, $m->email, $m->phone,
                    $m->referral_code, optional($m->referrer)->email, $m->created_at
                ]);
            }
            fclose($out);
        }, 'members.csv', ['Content-Type' => 'text/csv']);
    }

    public function destroyProfileImage(Member $member)
    {
        $member->documents()->where('type','profile')->get()->each->delete();
        return back()->with('ok','Profile image removed.');
    }

    public function destroyProof(Member $member)
    {
        $member->documents()->where('type','proof')->get()->each->delete();
        return back()->with('ok', 'Proof of address removed.');
    }

    private function uniqueReferralCode(): string
    {
        do 
        {
            $code = Str::upper(Str::random(8));
        } 
        while (Member::where('referral_code', $code)->exists());

        return $code;
    }

    private function storeDocument($model, \Illuminate\Http\UploadedFile $file, string $type): Document
    {
        $path = $file->store('uploads', 'public');
        return $model->documents()->create([
            'type'          => $type,
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_size'     => $file->getSize(),
            'mime_type'     => $file->getMimeType(),
        ]);
    }

    private function replaceDocument($model, UploadedFile $file, string $type): Document
    {
        $model->documents()->where('type', $type)->get()->each->delete();

        return $this->storeDocument($model, $file, $type);
    }

}
