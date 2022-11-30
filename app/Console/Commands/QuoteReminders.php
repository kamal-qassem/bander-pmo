<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Invoice;
use Modules\Quotes\Entities\Quote;
use Modules\Quotes\Entities\QuoteTask;
use Modules\Quotes\Entities\QuotesReminder;
use Eventy;
use App\Option;
use \Datetime;
use Modules\Templates\Entities\Template;
use Location;
use Carbon\Carbon;
use App\InternalNotification;

defined('CRON_JOB') or define('CRON_JOB', 'Yes');

class QuoteReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quote:reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This includes Task Reminders, General Reminders, Recurring tasks related to quote, Quote Expiration Reminders';

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
        $last_cron_run  = getOption('last_cron_run');

        $seconds = Eventy::filter('quote_reminders.cron_functions_execute_seconds', 300); // Minimum 5 Minutes.


        if ($last_cron_run == '' || (time() > ($last_cron_run + $seconds))) {
            $this->runCron();
        }
        */

        $this->runCron();

        $this->info('quote reminders Code executed');
    }

    public function runCron() {
        updateOption('last_cron_run', time());

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
        */

        $this->recurring_tasks();

        $this->generalreminders();

        $this->taskreminders();

	    $this->expiryreminders();
    }

    private function recurring_tasks() {
    	// Recurring Tasks.
    	$recurring_tasks = QuoteTask::where('recurring_value', '!=', 0)->whereRaw('(cycles != total_cycles OR cycles=0)')->get();
    	
	    foreach ($recurring_tasks as $task) {
			$type                = $task->recurring_type;
            $recurring_value     = $task->recurring_value;
            $last_recurring_date = $task->last_recurring_date;
            $task_date           = $task->startdate;
            $duedate             = $task->duedate;

	    	// Check if is first recurring
            if (!$last_recurring_date) {
                $last_recurring_date = date('Y-m-d', strtotime($task_date));
            } else {
                $last_recurring_date = date('Y-m-d', strtotime($last_recurring_date));
            }

            $re_create_at = date('Y-m-d', strtotime('+' . $recurring_value . ' ' . strtoupper($type), strtotime($last_recurring_date)));

            if ( strtotime( date('Y-m-d') ) >= strtotime( $re_create_at ) ) {
            	$newtask = $task->replicate();

            	$newtask->startdate = $re_create_at;

            	$newtask->datefinished = null;
            	$newtask->recurring_type = null;
            	$newtask->recurring_value = null;
            	$newtask->cycles = null;
            	$newtask->total_cycles = null;
            	$newtask->last_recurring_date = null;
            	$newtask->recurring_id = null;
            	$newtask->is_recurring_from = $task->id;
            	$newtask->billed = 'no';
            	$newtask->deadline_notified = 'no';

            	if ( ! empty( $duedate ) ) {
                    $dStart                      = new DateTime($task_date);
                    $dEnd                        = new DateTime($duedate);
                    $dDiff                       = $dStart->diff($dEnd);
                    $newtask->duedate = date('Y-m-d', strtotime('+' . $dDiff->days . ' days', strtotime($re_create_at)));
                }

                $newtask->save();

                if ( $newtask ) {
	                $assignees = $task->assigned_to()->get()->pluck('id')->toArray();
	                if ( ! empty( $assignees ) ) {
	                	$newtask->assigned_to()->sync( $assignees );
	            	}

	            	// Let us update recurring details for parent task.
	            	$task->last_recurring_date = $re_create_at;
	            	$task->total_cycles = $task->total_cycles + 1;
	            	$task->save();
            	}
            }
	    }
    }

    private function generalreminders() {
    	$enable_general_reminders = getSetting('enable_general_reminders', 'quote-cronjob');

    	if ( 'yes' === $enable_general_reminders ) {
        	// General Reminders.
	        $reminders = QuotesReminder::where('isnotified', '=', 'no' )->get();	        
	        foreach ($reminders as $reminder) {
	        	$reminderdate = Carbon::parse( $reminder->date );

	        	if ( $reminderdate->isToday() ) {
	        		$notification = array(
	                    'text' => $reminder->description,
	                    'link' => route( 'admin.quote_reminders.show', [ 'quote_id' => $reminder->quote_id, 'id' => $reminder->id ] ),
	                );
	                $internal_notification = InternalNotification::create($notification);
	                $internal_notification->users()->sync(array( $reminder->reminder_to_id ));

	                $customer = \App\User::find( $reminder->reminder_to_id )->contact_reference;

	                if ( $customer && filter_var( $customer->email, FILTER_VALIDATE_EMAIL ) && $reminder->notify_by_email == 'yes' ) {
	                	$action = 'quote-reminder';
	                    $data = array();
	                    $data['client_name'] = $customer->first_name . ' ' . $customer->last_name;
	                    $data['to_email'] = $customer->email;                            
	                    $data['site_title'] = getSetting( 'site_title', 'site_settings');
	                    $logo = getSetting( 'site_logo', 'site_settings' );
	                    $data['logo'] = asset( 'uploads/settings/' . $logo );
	                    $data['date'] = digiTodayDateAdd();
	                    
	                    $data['reminder_link'] = route( 'admin.quote_reminders.show', [ 'quote_id' => $reminder->quote_id, 'id' => $reminder->id ] );
	                    $data['description'] = $reminder->description;                            
	                    
	                    $data['site_address'] = getSetting( 'site_address', 'site_settings');
	                    $data['site_phone'] = getSetting( 'site_phone', 'site_settings');
	                    $data['site_email'] = getSetting( 'contact_email', 'site_settings');   
	                    if ( env('MAIL_HOST') != 'smtp.mailtrap.io' ) {
                            sendEmail( $action, $data ); // Email Sent.
                        }
	                }
	                $reminder->isnotified = 'yes';
	                $reminder->save();
	        	}
	        }
    	}
    }

    private function taskreminders() {
    	$enable_task_reminders = getSetting('enable_task_reminders', 'quote-cronjob');

        if ( 'yes' === $enable_task_reminders ) {
	        $reminder_before = getSetting('tasks_reminder_notification_before', 'quote-cronjob');

	        // Task Reminders.
	        $tasks = QuoteTask::whereNotNull('duedate')->where('status_id', '!=', STATUS_COMPLETED)->where('deadline_notified', '=', 'no')->get();
	        $now = Carbon::today();
	        	        
	        foreach( $tasks as $task ) {
	            $duedate = Carbon::parse( $task->duedate );
	            if ( $duedate->isToday() || $duedate->isFuture() ) {
	                
	                $diff = $duedate->diffInDays( $now );
	                
	                // Check if difference between start date and duedate is the same like the reminder before
	                // In this case reminder wont be sent becuase the task it too short
	                $start_date              = Carbon::parse($task->startdate);
	                $start_and_due_date_diff = $duedate->diffInDays( $start_date );

	                if ($diff <= $reminder_before && $start_and_due_date_diff > $reminder_before ) {
	                    $assignees = $task->assigned_to()->get();
	                                        
	                    $notification = array(
	                        'text' => $task->name,
	                        'link' => route( 'admin.quote_tasks.show', [ 'invoice_id' => $task->invoice_id, 'id' => $task->id ] ),
	                    );
	                    $internal_notification = InternalNotification::create($notification);
	                    $internal_notification->users()->sync($assignees->pluck('id')->toArray());
	                    

	                    foreach ($assignees as $member) {
	                        
	                        $customer = \App\User::find( $member->id )->contact_reference;
	                        if ( $customer && filter_var( $customer->email, FILTER_VALIDATE_EMAIL ) ) {                            

	                            $action = 'task-deadline-reminder';
	                            $data = array();
	                            $data['client_name'] = $customer->first_name . ' ' . $customer->last_name;
	                            $data['to_email'] = $customer->email;                            
	                            $data['site_title'] = getSetting( 'site_title', 'site_settings');
	                            $logo = getSetting( 'site_logo', 'site_settings' );
	                            $data['logo'] = asset( 'uploads/settings/' . $logo );
	                            $data['date'] = digiTodayDateAdd();
	                            
	                            $data['task_link'] = route( 'admin.quote_tasks.show', [ 'quote_id' => $task->quote_id, 'id' => $task->id ] );
	                            $data['task_name'] = $task->name;                            
	                            $data['task_duedate'] = digiDate( $task->duedate );

	                            $data['site_address'] = getSetting( 'site_address', 'site_settings');
	                            $data['site_phone'] = getSetting( 'site_phone', 'site_settings');
	                            $data['site_email'] = getSetting( 'contact_email', 'site_settings');   
	                            if ( env('MAIL_HOST') != 'smtp.mailtrap.io' ) {
                                    sendEmail( $action, $data ); // Email Sent.
                                }

	                            $task->deadline_notified = 'yes';
	                            $task->save();
	                        }
	                    }
	                }
	            }
	        }
    	}
    }

    private function expiryreminders() {
    	$quotes = Quote::where('status', '=', 'Published')->where('paymentstatus', '=', 'delivered')->get(); //Only get delivered quotes

        $now = new DateTime(date('Y-m-d'));

        foreach ($quotes as $quote) {
            if ($quote->invoice_due_date != null) {
                if (strtotime( date('Y-m-d') ) > strtotime( $quote->invoice_due_date ) ) {
                    $status = 'dead';
                    $quote->paymentstatus = $status;
                    $quote->save();
                    $this->insertHistory( array('id' => $quote->id, 'comments' => trans('quotes::custom.quotes.status-changed-' . $status), 'operation_type' => 'cron-updated' ) );
                } else {
                    
                    if ($quote->is_expiry_notified == 0 && $this->isTemplateAvailable( 'estimate-expiry-reminder' ) ) {
                        $reminder_before        = getSetting('send_quote_expiry_reminder_before', 'quote-cronjob');
                        $expirydate             = new DateTime($quote->invoice_due_date);
                        $diff                   = $expirydate->diff($now)->format('%a');
                        $date                   = strtotime($quote->invoice_date);
                        $expirydate             = strtotime($quote->invoice_due_date);
                        $date_and_due_date_diff = $expirydate - $date;
                        $date_and_due_date_diff = floor($date_and_due_date_diff / (60 * 60 * 24));
                        
                        if ($diff <= $reminder_before && $date_and_due_date_diff > $reminder_before) {
                            $customer = $quote->customer()->first();

                            if ( $customer ) {
                                $action = 'estimate-expiry-reminder';
                                $data = array();
                                $data['client_name'] = $customer->first_name . ' ' . $customer->last_name;
                                $data['to_email'] = $customer->email;
                                // $data['content'] = '';
                                $data['site_title'] = getSetting( 'site_title', 'site_settings');
                                $logo = getSetting( 'site_logo', 'site_settings' );
                                $data['logo'] = asset( 'uploads/settings/' . $logo );
                                $data['date'] = digiTodayDateAdd();

                                $data['invoice_url'] = route( 'admin.quotes.preview', [ 'slug' => $quote->slug ] );
                                $data['invoice_no'] = $quote->invoice_no;
                                $data['invoice_amount'] = $quote->amount;
                                $data['invoice_due_date'] = digiDate( $quote->invoice_due_date );
                                
                                $data['site_address'] = getSetting( 'site_address', 'site_settings');
                                $data['site_phone'] = getSetting( 'site_phone', 'site_settings');
                                $data['site_email'] = getSetting( 'contact_email', 'site_settings');   
                                if ( env('MAIL_HOST') != 'smtp.mailtrap.io' ) {
                                    sendEmail( $action, $data );
                                }

                                $this->insertHistory( array('id' => $quote->id, 'comments' => $action, 'operation_type' => 'cron-email' ) );

                                $quote->is_expiry_notified = 1;
                                $quote->save();
                            }
                        }
                    }
                }
            }
        }
    }

    private function insertHistory( $data ) {
        $ip_address = GetIP();
        $position = Location::get( $ip_address );

        $id = ! empty( $data['id'] ) ? $data['id'] : 0;
        $comments = ! empty( $data['comments'] ) ? $data['comments'] : 0;
        $operation_type = ! empty( $data['operation_type'] ) ? $data['operation_type'] : 'general';

        $city = ! empty( $position->cityName ) ? $position->cityName : '';
        if ( ! empty( $position->regionName ) ) {
            $city .= ' ' . $position->regionName;
        }
        if ( ! empty( $position->zipCode ) ) {
            $city .= ' ' . $position->zipCode;
        }
        $log = array(
            'ip_address' => $ip_address,
            'country' => ! empty( $position->countryName ) ? $position->countryName : '',
            'city' => $city,
            'browser' => ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : 'Cron job',
            'quote_id' => $id,
            'comments' => $comments,
            'operation_type' => $operation_type,
        );
        \Modules\Quotes\Entities\QuoteHistory::create( $log );
    }

    private function isTemplateAvailable( $key ) {
        $template = Template::where('key', '=', $key)->where('status', '=', 'active')->first();
        if ( $template ) {
            return true;
        } else {
            return false;
        }
    }
}
