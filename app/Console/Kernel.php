<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\RecurringInvoice::class,
        Commands\QuoteReminders::class,
        Commands\InvoiceReminders::class,
        Commands\OtherJobs::class,
        Commands\OrderReminders::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        if( config('app.db_database') != '' ) {
            updateOption('last_cron_checked', time()); 

            // Recurring invoice will generate automatically based on recurring invoice settings.
            $invoice_hour_auto_operations = getSetting('invoice_auto_operations_hour', 'invoice-cronjob'); // When the cron job will run

            if ($invoice_hour_auto_operations == '') {
                $invoice_hour_auto_operations = 9;
            }
            $schedule->command('recurringinvoice:generate')
                ->dailyAt( $invoice_hour_auto_operations )->withoutOverlapping();

            // This includes Task Reminders, General Reminders, Recurring tasks related to quote, Quote Expiration Reminders
            $invoice_auto_operations_hour = getSetting('invoice_auto_operations_hour', 'invoice-cronjob');
            if ($invoice_auto_operations_hour == '') {
                $invoice_auto_operations_hour = 9;
            }
            $schedule->command('quote:reminders')
                ->dailyAt( $invoice_auto_operations_hour )->withoutOverlapping();

            // This includes Task Reminders, General Reminders, Recurring tasks related to invoice, Invoice Overdue Reminders
            $invoice_auto_operations_hour = getSetting('invoice_auto_operations_hour', 'invoice-cronjob');
            if ($invoice_auto_operations_hour == '') {
                $invoice_auto_operations_hour = 9;
            }
            $schedule->command('invoice:reminders')
                ->dailyAt( $invoice_auto_operations_hour )->withoutOverlapping();

            // This includes Tickets auto close, Delete User actions data
            $invoice_auto_operations_hour = getSetting('invoice_auto_operations_hour', 'invoice-cronjob');
            if ($invoice_auto_operations_hour == '') {
                $invoice_auto_operations_hour = 9;
            }
            $schedule->command('other:jobs')
                ->dailyAt( $invoice_auto_operations_hour )->withoutOverlapping();

            // This includes Recurring orders Reminders
            $invoice_auto_operations_hour = getSetting('invoice_auto_operations_hour', 'order-cronjob');
            if ($invoice_auto_operations_hour == '') {
                $invoice_auto_operations_hour = 9;
            }
            $schedule->command('order:reminders')
                ->dailyAt( $invoice_auto_operations_hour )->withoutOverlapping();
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
