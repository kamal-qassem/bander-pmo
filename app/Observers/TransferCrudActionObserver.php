<?php

namespace App\Observers;

use App\Transfer;
use App\Notifications\QA_EmailNotification;
use Illuminate\Support\Facades\Notification;
use DB;

class TransferCrudActionObserver
{

    public function created(Transfer $model)
    {
        $email = getSetting( 'contact_email', 'site_settings');
        $templatedata = array(
            'email' => $email,
            'from_id' => $model->from_id,
            'to_id' => $model->to_id,
            'amount' => $model->amount,
            'date' => $model->date,
            'ref_no' => $model->ref_no,
            'description' => $model->description,
            'payment_method_id' => $model->payment_method_id,

            'site_address' => getSetting( 'site_address', 'site_settings'),
            'site_phone' => getSetting( 'site_phone', 'site_settings'),
            'site_email' => getSetting( 'contact_email', 'site_settings'),                
            'site_title' => getSetting( 'site_title', 'site_settings'),
            'logo' => asset( 'uploads/settings/' . getSetting( 'site_logo', 'site_settings' ) ),
            'date' => digiTodayDate(),
            'site_url' => env('APP_URL'),
        );

        if ( ! empty( $model->from->name ) ) {
            $templatedata['from_id'] = $model->from->name;
        }
        if ( ! empty( $model->to->name ) ) {
            $templatedata['to_id'] = $model->to->name;
        }
        if ( ! empty( $model->payment_method->name ) ) {
            $templatedata['payment_method_id'] = $model->payment_method->name;
        }

        $data = [
            "action" => "Created",
            "crud_name" => "Transfers",
            'template' => 'amount-transfered',
            'email' => $email,
            'data' => $templatedata,
        ];

        // All admin  users.
        $users = DB::table('contacts')
        ->join('role_user', 'role_user.user_id', '=', 'contacts.id')
        ->join('roles', 'roles.id', '=', 'role_user.role_id')->where('roles.title', 'Admin')->get();
        
        //$users = \App\User::whereHas('roles', function($q){$q->whereIn('role_name', ['Admin']);})->get();
        Notification::send($users, new QA_EmailNotification($data));

    }

    

    

}