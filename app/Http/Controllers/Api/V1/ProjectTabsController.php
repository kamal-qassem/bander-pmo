<?php

namespace App\Http\Controllers\Api\V1;

use App\ProjectTab;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectTabsRequest;
use App\Http\Requests\Admin\UpdateProjectTabsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ProjectTabsController extends Controller
{
    public function index()
    {
        return ProjectTab::all();
    }

    public function show($id)
    {
        return ProjectTab::findOrFail($id);
    }

    public function update(UpdateProjectTabsRequest $request, $id)
    {
        $project_tab = ProjectTab::findOrFail($id);
        $project_tab->update($request->all());
        

        return $project_tab;
    }

    public function store(StoreProjectTabsRequest $request)
    {
        $project_tab = ProjectTab::create($request->all());
        

        return $project_tab;
    }

    public function destroy($id)
    {
        $project_tab = ProjectTab::findOrFail($id);
        $project_tab->delete();
        return '';
    }
}
