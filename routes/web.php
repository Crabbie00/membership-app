<?php

use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => to_route('members.index'));

Route::resource('members', MemberController::class);
Route::get('members-export', [MemberController::class, 'export'])->name('members.export');
