<?php

use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => to_route('members.index'));

Route::resource('members', MemberController::class);
//Route::post('members/{member}/members-update', [MemberController::class, 'update'])->name('members.update');
Route::get('members-export', [MemberController::class, 'export'])->name('members.export');

Route::delete('members/{member}/profile-image', [MemberController::class, 'destroyProfileImage'])
    ->name('members.profile-image.destroy');


Route::delete('members/{member}/proof', [MemberController::class, 'destroyProof'])
    ->name('members.proof.destroy');