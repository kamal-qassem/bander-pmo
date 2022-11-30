<?php

namespace Modules\Smstemplates\Http\Controllers\Api\V1;

use Modules\Smstemplates\Entities\Smstemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Smstemplates\Http\Requests\Admin\StoreSmstemplatesRequest;
use Modules\Smstemplates\Http\Requests\Admin\UpdateSmstemplatesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class SmstemplatesController extends Controller
{
    public function index()
    {
        return Smstemplate::all();
    }

    public function show($id)
    {
        return Smstemplate::findOrFail($id);
    }

    public function update(UpdateSmstemplatesRequest $request, $id)
    {
        $smstemplate = Smstemplate::findOrFail($id);
        $smstemplate->update($request->all());
        

        return $smstemplate;
    }

    public function store(StoreSmstemplatesRequest $request)
    {
        $smstemplate = Smstemplate::create($request->all());
        

        return $smstemplate;
    }

    public function destroy($id)
    {
        $smstemplate = Smstemplate::findOrFail($id);
        $smstemplate->delete();
        return '';
    }
}
