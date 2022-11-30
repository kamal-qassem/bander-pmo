<?php

namespace Modules\Sendsms\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SendsmsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
        $items = [            
            ["title": "send_sm_create"],
            ["title": "send_sm_edit"],
            ["title": "send_sm_view"],
            ["title": "send_sm_delete"],

            ["title": "sms_gateway_access"],
            ["title": "sms_gateway_create"],
            ["title": "sms_gateway_edit"],
            ["title": "sms_gateway_view"],
            ["title": "sms_gateway_delete"],
        ];

        $ids = [];
        foreach ($items as $item) {
            $permission = \App\Permission::firstOrNew($item);
            $ids[] = $permission->id;
        }

        if ( ! empty( $ids ) ) {
            $role = Auth::User()->role;
            $role->permission()->sync($ids);
        }
    }
}
