<?php

namespace App\Console\Commands;

use Eventy;
use App\Option;
use Carbon\Carbon;
use App\InternalNotification;
use DB;


use Illuminate\Console\Command;
use Kordy\Ticketit\Models\Setting;

use Faker\Factory as Faker;

defined('CRON_JOB') or define('CRON_JOB', 'Yes');

class AssignRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:roles';

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

        $this->generate_fakedata();

    }

    private function generate_fakedata() {
        
        $contacts = \App\Contact::get();
        foreach( $contacts as $contact ) {
            $contact_type = \App\ContactType::inRandomOrder()->take(1)->pluck('id');
            $contact->contact_type()->sync($contact_type);

            $language = \App\Language::inRandomOrder()->take(1)->pluck('id');
            $contact->language()->sync($language);
        }
        
    }
}
