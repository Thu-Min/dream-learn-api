<?php

use App\Mail\EmailVerify;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mail', function() {
    return view('mails.auth.email_verify');
});
