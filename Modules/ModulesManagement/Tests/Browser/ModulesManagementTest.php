<?php

namespace Modules\ModulesManagement\Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ModulesManagementTest extends DuskTestCase
{

    public function testCreateModulesManagement()
    {
        $admin = factory('App\User', 'admin')->create();
        $modules_management = factory('Modules\ModulesManagement\Entities\ModulesManagement')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $modules_management) {
            $browser->loginAs($admin)
                ->visit(route('admin.modules_managements.index'))
                ->clickLink('Add new')
                ->type("name", $modules_management->name)
                ->type("slug", $modules_management->slug)
                ->select("type", $modules_management->type)
                ->select("enabled", $modules_management->enabled)
                ->type("description", $modules_management->description)
                ->press('Save')
                ->assertRouteIs('admin.modules_managements.index')
                ->assertSeeIn("tr:last-child td[field-key='name']", $modules_management->name)
                ->assertSeeIn("tr:last-child td[field-key='slug']", $modules_management->slug)
                ->assertSeeIn("tr:last-child td[field-key='type']", $modules_management->type)
                ->assertSeeIn("tr:last-child td[field-key='enabled']", $modules_management->enabled)
                ->logout();
        });
    }

    public function testEditModulesManagement()
    {
        $admin = factory('App\User', 'admin')->create();
        $modules_management = factory('Modules\ModulesManagement\Entities\ModulesManagement')->create();
        $modules_management2 = factory('Modules\ModulesManagement\Entities\ModulesManagement')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $modules_management, $modules_management2) {
            $browser->loginAs($admin)
                ->visit(route('admin.modules_managements.index'))
                ->click('tr[data-entry-id="' . $modules_management->id . '"] .btn-info')
                ->type("name", $modules_management2->name)
                ->type("slug", $modules_management2->slug)
                ->select("type", $modules_management2->type)
                ->select("enabled", $modules_management2->enabled)
                ->type("description", $modules_management2->description)
                ->press('Update')
                ->assertRouteIs('admin.modules_managements.index')
                ->assertSeeIn("tr:last-child td[field-key='name']", $modules_management2->name)
                ->assertSeeIn("tr:last-child td[field-key='slug']", $modules_management2->slug)
                ->assertSeeIn("tr:last-child td[field-key='type']", $modules_management2->type)
                ->assertSeeIn("tr:last-child td[field-key='enabled']", $modules_management2->enabled)
                ->logout();
        });
    }

    public function testShowModulesManagement()
    {
        $admin = factory('App\User', 'admin')->create();
        $modules_management = factory('Modules\ModulesManagement\Entities\ModulesManagement')->create();

        


        $this->browse(function (Browser $browser) use ($admin, $modules_management) {
            $browser->loginAs($admin)
                ->visit(route('admin.modules_managements.index'))
                ->click('tr[data-entry-id="' . $modules_management->id . '"] .btn-primary')
                ->assertSeeIn("td[field-key='name']", $modules_management->name)
                ->assertSeeIn("td[field-key='slug']", $modules_management->slug)
                ->assertSeeIn("td[field-key='type']", $modules_management->type)
                ->assertSeeIn("td[field-key='enabled']", $modules_management->enabled)
                ->assertSeeIn("td[field-key='description']", $modules_management->description)
                ->logout();
        });
    }

}
