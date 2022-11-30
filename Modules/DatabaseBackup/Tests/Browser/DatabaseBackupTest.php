<?php

namespace Modules\DatabaseBackup\Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Modules\DatabaseBackup\Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class DatabaseBackupTest extends DuskTestCase
{

    public function testCreateDatabaseBackup()
    {
        $admin = factory('App\User', 'admin')->create();
        $database_backup = factory('Modules\DatabaseBackup\Entities\DatabaseBackup')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $database_backup) {
            $browser->loginAs($admin)
                ->visit(route('admin.database_backups.index'))
                ->clickLink('Add new')
                ->type("name", $database_backup->name)
                ->type("storage_location", $database_backup->storage_location)
                ->press('Save')
                ->assertRouteIs('admin.database_backups.index')
                ->assertSeeIn("tr:last-child td[field-key='name']", $database_backup->name)
                ->assertSeeIn("tr:last-child td[field-key='storage_location']", $database_backup->storage_location)
                ->logout();
        });
    }

    public function testEditDatabaseBackup()
    {
        $admin = factory('App\User', 'admin')->create();
        $database_backup = factory('Modules\DatabaseBackup\Entities\DatabaseBackup')->create();
        $database_backup2 = factory('Modules\DatabaseBackup\Entities\DatabaseBackup')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $database_backup, $database_backup2) {
            $browser->loginAs($admin)
                ->visit(route('admin.database_backups.index'))
                ->click('tr[data-entry-id="' . $database_backup->id . '"] .btn-info')
                ->type("name", $database_backup2->name)
                ->type("storage_location", $database_backup2->storage_location)
                ->press('Update')
                ->assertRouteIs('admin.database_backups.index')
                ->assertSeeIn("tr:last-child td[field-key='name']", $database_backup2->name)
                ->assertSeeIn("tr:last-child td[field-key='storage_location']", $database_backup2->storage_location)
                ->logout();
        });
    }

    public function testShowDatabaseBackup()
    {
        $admin = factory('App\User', 'admin')->create();
        $database_backup = factory('Modules\DatabaseBackup\Entities\DatabaseBackup')->create();

        


        $this->browse(function (Browser $browser) use ($admin, $database_backup) {
            $browser->loginAs($admin)
                ->visit(route('admin.database_backups.index'))
                ->click('tr[data-entry-id="' . $database_backup->id . '"] .btn-primary')
                ->assertSeeIn("td[field-key='name']", $database_backup->name)
                ->assertSeeIn("td[field-key='storage_location']", $database_backup->storage_location)
                ->logout();
        });
    }

}
