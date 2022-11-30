<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use Auth;
use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('install');
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * This is method is override from Authenticate Users class
     * This validates the user with username or email with the sent password
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function postLogin(Request $request, MessageBag $message_bag)
    {

        $rules = [
            'email'  => 'required|email|exists:contacts,email',
            'password' => 'required',
        ];

        $this->validate($request, $rules);

        $login_status = FALSE;
        $rememberme = FALSE;
        if( $request->has('rememberme') )
            $rememberme = TRUE;

        // echo Hash::make($request->password);
        //  die();
        if (Auth::attempt(['email'=> $request->email, 'password' => $request->password, 'status' => 'Active'], $rememberme)) {
            $login_status = TRUE;
        }

        if(!$login_status)
        {
               $record = User::where('email', $request->email)->first();
               $message = trans("auth.failed");
               if( $record )
               {
                   if($record->status == 'Suspended' )
                   {
                       $message = trans('auth.suspended');
                   } elseif( ! empty( $record->confirmation_code ) )
                   {
                       $message = trans('auth.not-confirmed');
                   }
               }
               $message_bag->add('email', $message);
               return redirect()->back()->withErrors($message_bag);
        }

        if($login_status)
        {
            $user = Auth()->user();
            // Let us set the user default language as per admin settings.
            $direction = 'ltr';
            $lang = 'en';
            $theme = 'default';
            $color_theme = 'default';
            $color_skin = 'skin-blue';
            if ( ! empty( $user->theme ) ) {
              $theme = $user->theme;
            }
            if ( ! empty( $user->color_theme ) ) {
              $color_theme = $user->color_theme;
            }
            if ( ! empty( $user->color_skin ) ) {
              $color_skin = $user->color_skin;
            }

            $user->last_login_from = GetIP();
            $user->save();

            updateLocalSettings();

            return redirect($this->redirectTo)
            ->withCookie(cookie()->forever('language', $lang))
            ->withCookie(cookie()->forever('direction', $direction))
            ->withCookie(cookie()->forever('color_theme', $color_theme))
            ->withCookie(cookie()->forever('color_skin', $color_skin))
            ->withCookie(cookie()->forever('theme', $theme));
        }
    }

    public function confirm(MessageBag $message_bag, $confirmation_code)
    {
        $record = User::where('confirmation_code', $confirmation_code)->first();
        if( ! $record ) {
            // flashMessage( 'danger', 'create', trans('auth.invalid-link') );
            $message_bag->add('email', trans('auth.invalid-link'));
            return redirect()->route('login')->withErrors($message_bag);
        }

        $record->email_verified_at = Carbon::now();
        $record->confirmation_code = null;
        $record->status = 'Active';
        $record->save();
        // flashMessage('success', 'create', trans('auth.confirmation-success'));
        //$message_bag->add('email', trans('auth.confirmation-success'));
        return redirect('login')->with('message', trans('auth.confirmation-success'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        if ( isDemo() ) {
            return redirect()->route('direct.login');
        } else {
            return redirect('login');
        }
    }

    public function directLogin( MessageBag $message_bag, $id = '' )
    {
        /*
        if( ! isDemo() ) {
            $message_bag->add('email', trans('auth.direct-login-notallowed'));
            return redirect()->route('login')->withErrors($message_bag);
        }
        */
      $users = [
            // Admin Role
            [
                'id' => DEFAULT_ADMIN_ID,
                'email' => 'admin@admin.com',
                'password' => 'password', // password
                'role' => ADMIN_TYPE,
            ],

            // Executive
            [
                'id' => DEFAULT_EXECUTIVE_ID,
                'email' => 'sam@gmail.com',
                'password' => 'password', // password
                'role' => EXECUTIVE_TYPE,
            ],

            // Customer
            [
                'id' => DEFAULT_CUSTOMER_ID,
                'email' => 'domenic@gmail.com',
                'password' => 'password', // password
                'role' => CUSTOMERS_TYPE,
            ],

            // Sale Agent
            [
                'id' => DEFAULT_SALEAGENT_ID,
                'email' => 'cieo@gmail.com',
                'password' => 'password', // password
                'role' => CONTACT_SALE_AGENT,
            ],

            // Supplier
            [
                'id' => DEFAULT_SUPPLIER_ID,
                'email' => 'brent@gmail.com',
                'password' => 'password', // password
                'role' => SUPPLIERS_TYPE,
            ],

            // Sales Manager
            [
                'id' => DEFAULT_SALEMANAGER_ID,
                'email' => 'lavinia@gmail.com',
                'password' => 'password', // password
                'role' => SALES_MANAGER_TYPE,
            ],

            // Employee
            [
                'id' => DEFAULT_EMPLOYEE_ID,
                'email' => 'himla@gmail.com',
                'password' => 'password', // password
                'role' => EMPLOYEES_TYPE,
            ],

            // Client
            [
                'id' => DEFAULT_CLIENT_ID,
                'email' => 'merle@gmail.com',
                'password' => 'password', // password
                'role' => CONTACT_CLIENT_TYPE,
            ],

            // Project Manager
            [
                'id' => DEFAULT_PROJECTMANAGER_ID,
                'email' => 'joanie@gmail.com',
                'password' => 'password', // password
                'role' => PROJECT_MANAGER,
            ],

            // Business Manager
            [
                'id' => DEFAULT_BUSINESSMANAGER_ID,
                'email' => 'robert@gmail.com',
                'password' => 'password', // password
                'role' => BUSINESS_MANAGER_TYPE,
            ],

            // Stock Manager
            [
                'id' => DEFAULT_STOCKMANAGER_ID,
                'email' => 'donald@example.com',
                'password' => 'password', // password
                'role' => STOCK_MANAGER,
            ],

        ];

      if ( ! empty( $id ) ) {
        $user = $email = $password = '';

        foreach ($users as $user) {
            if ( $user['role'] == $id ) {
                $email = $user['email'];
                $password = $user['password'];

            }
        }

        if ( empty( $user ) ) {
            $message_bag->add('email', 'User not exists');
            return redirect()->back()->withErrors($message_bag);
        }
        $login_status = false;

        if (Auth::attempt(['email'=> $email, 'password' => $password, 'status' => 'Active'], false)) {
            $login_status = TRUE;
        }

        if(!$login_status)
        {
               $record = User::where('email', $email)->first();
               $message = trans("auth.failed");
               if( $record )
               {
                   if($record->status == 'Suspended' )
                   {
                       $message = trans('auth.suspended');
                   } elseif( ! empty( $record->confirmation_code ) )
                   {
                       $message = trans('auth.not-confirmed');
                   }
               }
               $message_bag->add('email', $message);
               return redirect()->back()->withErrors($message_bag);
        }

        if($login_status)
        {
            $user = Auth()->user();
            // Let us set the user default language as per admin settings.
            $direction = 'ltr';
            $lang = 'en';
            $theme = 'default';
            $color_theme = 'default';
            $color_skin = 'skin-blue';
            if ( ! empty( $user->theme ) ) {
              $theme = $user->theme;
            }
            if ( ! empty( $user->color_theme ) ) {
              $color_theme = $user->color_theme;
            }
            if ( ! empty( $user->color_skin ) ) {
              $color_skin = $user->color_skin;
            }

            $user->last_login_from = GetIP();
            $user->save();

            updateLocalSettings();

            return redirect($this->redirectTo)
            ->withCookie(cookie()->forever('language', $lang))
            ->withCookie(cookie()->forever('direction', $direction))
            ->withCookie(cookie()->forever('color_theme', $color_theme))
            ->withCookie(cookie()->forever('color_skin', $color_skin))
            ->withCookie(cookie()->forever('theme', $theme));
        }
      }

      $roles = \App\ContactType::where('type', 'role')->where('status', 'active')->orderBy('priority')->get();

      foreach( $roles as $role ) {
        foreach ($users as $user) {
          if ( $user['role'] == $role->id ) {
            $role->email = $user['email'];
            $role->password = $user['password'];
          }
        }
      }
      return view('auth.direct-login', compact('roles', 'users'));
    }

}

    /*
    function dueReminders() {

        $from = date('Y-m-d' . ' 00:00:00', time()); //need a space after dates.
        $to = date('Y-m-d' . ' 24:60:60', time());
        $invoices = \App\Invoice::whereBetween('due_date', [])->get();

        $campaigns = $this->db->query($query)->result();
        if ( ! empty($campaigns) ) {
            foreach ( $campaigns as $campaign ) {
                $template = $this->db->query('SELECT * FROM email_templates WHERE status="Active" AND id = ' . $campaign->template)->result();
                If ( ! empty($template) ) {
                    $message = $template[0]->description;
                    $from = $this->config->item('site_settings')->contact_email;
                    $sub = $campaign->title;
                    $emails = json_decode($campaign->emails);
                    foreach ( $emails as $email ) {
                        if ( $email != '' ) {
                            sendEmail($from, $email, $sub, $message);
                        }
                    }
                }
            }
        }
    }
    */

