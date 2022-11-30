<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Mail\Mailer;

class SendBulkEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $data )
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        
        $emails = $this->data['emails'];

        // $emails = ['adiyya@gmail.com', 'adiyya@conquerorstech.net'];
        /*
        $mailer->queue('email.welcome', ['data'=>'data'], function ($message) use( $emails ) {
            $message->from('nwambachristian@gmail.com', 'Christian Nwmaba');
            $message->to('nwambachristian@gmail.com');
        });
        */
        if ( ! empty( $emails ) ) {
            foreach ($emails as $email) {
                $this->data['to_email'] = $email;
                sendEmail( 'bulk-contact-email', $this->data );
            }
            
        }
        
    }
}
