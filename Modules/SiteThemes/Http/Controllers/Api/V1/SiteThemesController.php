<?php

namespace Modules\SiteThemes\Http\Controllers\Api\V1;

use Modules\SiteThemes\Entities\SiteTheme;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\SiteThemes\Http\Requests\Admin\StoreSiteThemesRequest;
use Modules\SiteThemes\Http\Requests\Admin\UpdateSiteThemesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class SiteThemesController extends Controller
{
    public function index()
    {
        return SiteTheme::all();
    }

    public function show($id)
    {
        return SiteTheme::findOrFail($id);
    }

    public function update(UpdateSiteThemesRequest $request, $id)
    {
        $site_theme = SiteTheme::findOrFail($id);
        $site_theme->update($request->all());
        

        return $site_theme;
    }

    public function store(StoreSiteThemesRequest $request)
    {
        $site_theme = SiteTheme::create($request->all());
        

        return $site_theme;
    }

    public function destroy($id)
    {
        $site_theme = SiteTheme::findOrFail($id);
        $site_theme->delete();
        return '';
    }
}
