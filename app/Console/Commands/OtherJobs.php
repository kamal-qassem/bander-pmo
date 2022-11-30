<?php

namespace App\Console\Commands;

use Eventy;
use App\Option;
use Carbon\Carbon;
use App\InternalNotification;
use DB;


use Illuminate\Console\Command;
use Kordy\Ticketit\Models\Setting;

defined('CRON_JOB') or define('CRON_JOB', 'Yes');

class OtherJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'other:jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This includes Tickets auto close, Delete User actions data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        /*
        $invoice_auto_operations_hour = getSetting('invoice_auto_operations_hour', 'invoice-cronjob');
        if ($invoice_auto_operations_hour == '') {
            $invoice_auto_operations_hour = 9;
        }

        $invoice_auto_operations_hour = intval($invoice_auto_operations_hour);
        $hour_now                     = date('G');
        if ($hour_now != $invoice_auto_operations_hour) {
            return;
        }
        
        $last_cron_run  = getOption('last_cron_run');

        $seconds = Eventy::filter('invoice_reminders.cron_functions_execute_seconds', 300); // Minimum 5 Minutes.


        if ($last_cron_run == '' || (time() > ($last_cron_run + $seconds))) {
            $this->autoclose_tickets();

            $this->delete_activity_log();

            updateOption('last_cron_run', time());
        }
        */

        $this->autoclose_tickets();

        $this->delete_activity_log();

        updateOption('last_cron_run', time());

        $this->info('Other jobs Code executed');
    }

    private function delete_activity_log() {
        $delete_activity_log_older_than = getSetting('delete_activity_log_older_than', 'site_settings');

        if ($delete_activity_log_older_than == 0 || empty($delete_activity_log_older_than)) {
            return;
        }

        DB::table('user_actions')->whereRaw('created_at < DATE_SUB(NOW(), INTERVAL ' . $delete_activity_log_older_than . ' DAY)')->delete();
    }

    private function autoclose_tickets() {
        $auto_close_after = getSetting('autoclose_tickets_after', 'site_settings');

        if ($auto_close_after == 0) {
            return;
        }

        $tickets = DB::table('ticketit')
        ->join('contacts', 'contacts.id', '=', 'ticketit.user_id')
        ->join('ticketit_statuses', 'ticketit_statuses.id', '=', 'ticketit.status_id')
        ->join('ticketit_priorities', 'ticketit_priorities.id', '=', 'ticketit.priority_id')
        ->join('ticketit_categories', 'ticketit_categories.id', '=', 'ticketit.category_id')
        //->leftJoin('ticketit_comments', 'ticketit_comments.ticket_id', '=', 'ticketit.id')
        ->select([
                'ticketit.id',
                'ticketit.created_at AS ticket_created_at',
                'ticketit.subject AS subject',
                'ticketit_statuses.name AS status',
                'ticketit_statuses.color AS color_status',
                'ticketit_priorities.color AS color_priority',
                'ticketit_categories.color AS color_category',
                'ticketit.id AS agent',
                'ticketit.updated_at AS updated_at',
                'ticketit_priorities.name AS priority',
                'contacts.name AS owner',
                'ticketit.agent_id',
                'ticketit.user_id',
                'ticketit_categories.name AS category',
                //'ticketit_comments.created_at AS lastreply',
            ])->where('ticketit.status_id', '!=', SUPPORT_STATUS_COMPLETED)->get();
        
        if ( $tickets->count() > 0 ) {
            
            foreach ($tickets as $ticket ) {
                $close_ticket = false;
                $details =  DB::table('ticketit_comments')->where('ticket_id', '=', $ticket->id)->first();
                //dd( $details );
                if ( $details ) {                    
                    $last_reply = strtotime($details->created_at);
                    if ($last_reply <= strtotime('-' . $auto_close_after . ' hours')) {
                        $close_ticket = true;
                    }

                    $created = strtotime($ticket->ticket_created_at);
                    if ($created <= strtotime('-' . $auto_close_after . ' hours')) {
                        $close_ticket = true;
                    }
                } else {
                    $created = strtotime($ticket->ticket_created_at);
                    if ($created <= strtotime('-' . $auto_close_after . ' hours')) {
                        $close_ticket = true;
                    }
                }

                if ($close_ticket == true) {
                    //$ticket->completed_at = Carbon::now();
                    //$ticket->save();

                    DB::table('ticketit')->where('id', '=', $ticket->id)->update(['completed_at' => Carbon::now()]);

                    $customer = \App\User::find( $ticket->user_id )->contact_reference;
                    
                    if ( $customer ) {
                        if ( $customer->ticket_emails == 1 ) {
                            $action = 'auto-close-ticket';
                            $data = array();
                            $data['client_name'] = $customer->first_name . ' ' . $customer->last_name;
                            $data['to_email'] = $customer->email;                            
                            $data['site_title'] = getSetting( 'site_title', 'site_settings');
                            $logo = getSetting( 'site_logo', 'site_settings' );
                            $data['logo'] = asset( 'uploads/settings/' . $logo );
                            $data['date'] = digiTodayDateAdd();
                            
                            $data['ticket_url'] = route(Setting::grab('main_route').'.show', $ticket->id);
                            $data['ticket_subject'] = $ticket->subject;
                            $data['ticket_id'] = $ticket->id;
                            $data['category'] = $ticket->category;
                            $data['priority'] = $ticket->priority;
                            $data['ticket_created_at'] = $ticket->ticket_created_at;
                            
                            $data['site_address'] = getSetting( 'site_address', 'site_settings');
                            $data['site_phone'] = getSetting( 'site_phone', 'site_settings');
                            $data['site_email'] = getSetting( 'contact_email', 'site_settings');   
                            if ( env('MAIL_HOST') != 'smtp.mailtrap.io' ) {
                                sendEmail( $action, $data ); // Email Sent.
                            }
                        }
                    }
                }
            }
        }
    }
}
