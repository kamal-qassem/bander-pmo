<?php

namespace App\Http\Controllers\Api\V1;

use App\Template;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTemplatesRequest;
use App\Http\Requests\Admin\UpdateTemplatesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class TemplatesController extends Controller
{
    public function index()
    {
        return Template::all();
    }

    public function show($id)
    {
        return Template::findOrFail($id);
    }

    public function update(UpdateTemplatesRequest $request, $id)
    {
        $template = Template::findOrFail($id);
        $template->update($request->all());
        

        return $template;
    }

    public function store(StoreTemplatesRequest $request)
    {
        $template = Template::create($request->all());
        

        return $template;
    }

    public function destroy($id)
    {
        $template = Template::findOrFail($id);
        $template->delete();
        return '';
    }
}
