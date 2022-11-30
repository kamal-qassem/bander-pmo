<?php

namespace Modules\SiteThemes\Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class SiteThemeTest extends DuskTestCase
{

    public function testCreateSiteTheme()
    {
        $admin = factory('App\User', 'admin')->create();
        $site_theme = factory('Modules\SiteThemes\Entities\SiteTheme')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $site_theme) {
            $browser->loginAs($admin)
                ->visit(route('admin.site_themes.index'))
                ->clickLink('Add new')
                ->type("title", $site_theme->title)
                ->type("theme_title_key", $site_theme->theme_title_key)
                ->type("description", $site_theme->description)
                ->select("is_active", $site_theme->is_active)
                ->type("theme_color", $site_theme->theme_color)
                ->press('Save')
                ->assertRouteIs('admin.site_themes.index')
                ->assertSeeIn("tr:last-child td[field-key='title']", $site_theme->title)
                ->assertSeeIn("tr:last-child td[field-key='theme_title_key']", $site_theme->theme_title_key)
                ->assertSeeIn("tr:last-child td[field-key='is_active']", $site_theme->is_active)
                ->assertSeeIn("tr:last-child td[field-key='theme_color']", $site_theme->theme_color)
                ->logout();
        });
    }

    public function testEditSiteTheme()
    {
        $admin = factory('App\User', 'admin')->create();
        $site_theme = factory('Modules\SiteThemes\Entities\SiteTheme')->create();
        $site_theme2 = factory('Modules\SiteThemes\Entities\SiteTheme')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $site_theme, $site_theme2) {
            $browser->loginAs($admin)
                ->visit(route('admin.site_themes.index'))
                ->click('tr[data-entry-id="' . $site_theme->id . '"] .btn-info')
                ->type("title", $site_theme2->title)
                ->type("theme_title_key", $site_theme2->theme_title_key)
                ->type("description", $site_theme2->description)
                ->select("is_active", $site_theme2->is_active)
                ->type("theme_color", $site_theme2->theme_color)
                ->press('Update')
                ->assertRouteIs('admin.site_themes.index')
                ->assertSeeIn("tr:last-child td[field-key='title']", $site_theme2->title)
                ->assertSeeIn("tr:last-child td[field-key='theme_title_key']", $site_theme2->theme_title_key)
                ->assertSeeIn("tr:last-child td[field-key='is_active']", $site_theme2->is_active)
                ->assertSeeIn("tr:last-child td[field-key='theme_color']", $site_theme2->theme_color)
                ->logout();
        });
    }

    public function testShowSiteTheme()
    {
        $admin = factory('App\User', 'admin')->create();
        $site_theme = factory('Modules\SiteThemes\Entities\SiteTheme')->create();

        


        $this->browse(function (Browser $browser) use ($admin, $site_theme) {
            $browser->loginAs($admin)
                ->visit(route('admin.site_themes.index'))
                ->click('tr[data-entry-id="' . $site_theme->id . '"] .btn-primary')
                ->assertSeeIn("td[field-key='title']", $site_theme->title)
                ->assertSeeIn("td[field-key='slug']", $site_theme->slug)
                ->assertSeeIn("td[field-key='theme_title_key']", $site_theme->theme_title_key)
                ->assertSeeIn("td[field-key='settings_data']", $site_theme->settings_data)
                ->assertSeeIn("td[field-key='description']", $site_theme->description)
                ->assertSeeIn("td[field-key='is_active']", $site_theme->is_active)
                ->assertSeeIn("td[field-key='theme_color']", $site_theme->theme_color)
                ->logout();
        });
    }

}
