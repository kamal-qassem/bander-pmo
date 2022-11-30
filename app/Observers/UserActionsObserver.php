<?php

namespace App\Observers;

use Auth;
use App\UserAction;

class UserActionsObserver
{
    public function saved($model)
    {
        if ($model->wasRecentlyCreated == true) {
            // Data was just created
            $action = 'created';
        } else {
            // Data was updated
            $action = 'updated';
        }
        if (Auth::check()) {           
            UserAction::create([
                'user_id'      => Auth::user()->id,
                'action'       => $action,
                'action_model' => $model->getTable(),
                'action_id'    => $model->id,

                'record_original'    => json_encode( $model->getOriginal() ),
                'record_update'    => json_encode( $model->getAttributes() ),
            ]);

            if ( 'contacts' === $model->getTable() ) {
                $enable_mailchimp = getSetting('enable_mailchimp', 'mailchimp-settings', 'no');
                $api_key = trim( getSetting('mailchimp_api_key', 'mailchimp-settings', '') );
                $contact = \App\Contact::find( $model->id );
                
                if ( ! empty( $enable_mailchimp ) 
                    && 'Yes' === $enable_mailchimp 
                    && ! empty( $api_key ) 
                    && $contact 
                    && ! empty( $contact->email ) 
                    && filter_var($contact->email, FILTER_VALIDATE_EMAIL)
                ) {
                    $MailChimp = new \DrewM\MailChimp\MailChimp( $api_key );
                    $mailchimp_lists = $MailChimp->get('lists');
                    $contact_types = $contact->contact_type->pluck('slug')->toArray();
                    
                    if ( ! empty( $contact_types ) ) {
                        foreach ($contact_types as $slug) {
                            $list_id = getSetting('default-mailchimplist-'.strtolower($slug), 'mailchimp-settings', '');

                            if ( ! empty( $list_id ) ) {
                                $subscriber_hash = md5( strtolower( $contact->email ) );
                                $result = $MailChimp->put("lists/$list_id/members/$subscriber_hash", [
                                    'email_address' => $contact->email,
                                    'status'        => 'subscribed',
                                    'language' => $contact->languagecode->code,
                                    'merge_fields' => (Object)[
                                        'FNAME' => $contact->first_name,
                                        'LNAME' => $contact->last_name,
                                        /*
                                        'ADDRESS' => [
                                            'addr1' => $contact->fulladdress,
                                            //'addr2' => $contact->address2,
                                            'city' => $contact->city,
                                            'state' => $contact->state_region,
                                            'zip' => $contact->zip_postal_code,
                                            'country' => $contact->country->shortcode,
                                            'language' => $contact->languagecode->code,
                                        ],
                                        */
                                    ],
                                ]);
                            }
                        }
                    }
                }
            }
        } else {
            // If it is CRON job.
            UserAction::create([
                'user_id'      => null,
                'action'       => $action,
                'action_model' => $model->getTable(),
                'action_id'    => $model->id,

                'record_original'    => json_encode( $model->getOriginal() ),
                'record_update'    => json_encode( $model->getAttributes() ),
            ]);
        }
    }


    public function deleting($model)
    {
        if (Auth::check()) {
            UserAction::create([
                'user_id'      => Auth::user()->id,
                'action'       => 'deleted',
                'action_model' => $model->getTable(),
                'action_id'    => $model->id,

                'record_original'    => json_encode( $model->getOriginal() ),
                'record_update'    => json_encode( $model->getAttributes() ),
            ]);
        } else {
            // If it is CRON job.
            UserAction::create([
                'user_id'      => null,
                'action'       => $action,
                'action_model' => $model->getTable(),
                'action_id'    => $model->id,

                'record_original'    => json_encode( $model->getOriginal() ),
                'record_update'    => json_encode( $model->getAttributes() ),
            ]);
        }
    }
}