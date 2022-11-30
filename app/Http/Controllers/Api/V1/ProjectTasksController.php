<?php

namespace App\Http\Controllers\Api\V1;

use App\ProjectTask;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectTasksRequest;
use App\Http\Requests\Admin\UpdateProjectTasksRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ProjectTasksController extends Controller
{
    use FileUploadTrait;

    public function index()
    {
        return ProjectTask::all();
    }

    public function show($id)
    {
        return ProjectTask::findOrFail($id);
    }

    public function update(UpdateProjectTasksRequest $request, $id)
    {
        $request = $this->saveFiles($request);
        $project_task = ProjectTask::findOrFail($id);
        $project_task->update($request->all());
        

        return $project_task;
    }

    public function store(StoreProjectTasksRequest $request)
    {
        $request = $this->saveFiles($request);
        $project_task = ProjectTask::create($request->all());
        

        return $project_task;
    }

    public function destroy($id)
    {
        $project_task = ProjectTask::findOrFail($id);
        $project_task->delete();
        return '';
    }
}
