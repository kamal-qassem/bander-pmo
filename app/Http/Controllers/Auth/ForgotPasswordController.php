<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    /*
    public function sendResetLinkEmail( Request $request, MessageBag $message_bag )
    {
        $rules = [
            'email'  => 'required|email|exists:contacts,email',
        ];
        
        $this->validate($request, $rules);

        $user = \App\User::select('password_resets.token', 'contacts.email')->join('password_resets', 'contacts.email', '=', 'password_resets.email')->where('contacts.email', $request->email)->first();
        //dd( $user );
        if( ! $user ) {
            $message = trans("auth.failed");
            $message_bag->add('email', $message);
            return redirect()->back()->withErrors($message_bag);
        }
        $data = [];
        $data['to_email'] = $user->email;
        $data['site_title'] = getSetting( 'site_title', 'site_settings');
        $logo = getSetting( 'site_logo', 'site_settings' );
        $data['logo'] = asset( 'uploads/settings/' . $logo );
        $data['date'] = digiTodayDateAdd();
        $data['link'] = url('/password/reset/' . $user->token);
        $data['site_address'] = getSetting( 'site_address', 'site_settings');
            $data['site_phone'] = getSetting( 'site_phone', 'site_settings');
            $data['site_email'] = getSetting( 'contact_email', 'site_settings');

        $res = sendEmail( 'forgot-password', $data );
        // We have e-mailed your password reset link!
        return redirect('login')->with('message', trans('auth.reset-confirmation-success'));
    }
    */
    
}

