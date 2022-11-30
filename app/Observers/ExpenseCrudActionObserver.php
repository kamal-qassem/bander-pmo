<?php

namespace App\Observers;

use App\Expense;
use App\Notifications\QA_EmailNotification;
use Illuminate\Support\Facades\Notification;

class ExpenseCrudActionObserver
{

    public function created(Expense $model)
    {

        $admin_email = getSetting('contact_email','site_settings');

        if ( ! empty( $admin_email ) ) {
            $emails = [ $admin_email ];

            $logo = getSetting( 'site_logo', 'site_settings' );
            // dd( $model );
            $templatedata = array(
                'to_email' => $admin_email,

                'account_id' => ! empty($model->account) ? $model->account->id : null,
                'entry_date' => $model->entry_date,
                'amount' => digiCurrency($model->amount,$model->currency_id),
                'description' => $model->description,
                'ref_no' => $model->ref_no,
                'slug' => $model->slug,
                'updated_at' => digiDate( $model->updated_at, true ),
                'created_at' => digiDate( $model->created_at),

                'payee_id' => '',
                'payment_method_id' => '',
                'expense_category_id' => '',

                'date' => digiTodayDate(),
                'logo' => asset( 'uploads/settings/' . $logo ),
                'site_title' => getSetting( 'site_title', 'site_settings'),
                'site_address' => getSetting( 'site_address', 'site_settings'),
                'site_phone' => getSetting( 'site_phone', 'site_settings'),
                'site_email' => getSetting( 'contact_email', 'site_settings'),
            );
            if ( ! empty( $model->payee_id ) ) {
                $templatedata['payee_id'] = $model->payee->name;
            }
            if ( ! empty( $model->payment_method ) ) {
                $templatedata['payment_method_id'] = $model->payment_method->name;
            }
            if ( ! empty( $model->expense_category ) ) {
                $templatedata['expense_category_id'] = $model->expense_category->name;
            }
            $data = [
                "action" => "Created",
                "crud_name" => "Expenses",
                'template' => 'expense-created',
                'model' => 'App\Expense',
                'data' => $templatedata,
            ];

            $users = \App\User::where("email", $emails)->get();

            Notification::send($users, new QA_EmailNotification($data));
        }
        // sendEmail( 'expense-created', $templatedata );

    }

    

    

}