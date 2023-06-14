<?php

namespace App\Http\Controllers;

use App\Mail\KirimEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class KirimEmailController extends Controller
{


    public function index()
    {
        $otp = mt_rand(100000, 999999);
        $user =User::find();
        $email = new KirimEmail($otp,$user);
        Mail::to("f.catus45@gmail.com")->send($email);
        return view('mail.KirimEmail')->with('otp', $otp);
    }
}
