<?php

namespace Modules\DynamicOptions\Http\Controllers\Api\V1;

use Modules\DynamicOptions\Entities\DynamicOption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\DynamicOptions\Http\Requests\Admin\StoreDynamicOptionsRequest;
use Modules\DynamicOptions\Http\Requests\Admin\UpdateDynamicOptionsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class DynamicOptionsController extends Controller
{
    public function index()
    {
        return DynamicOption::all();
    }

    public function show($id)
    {
        return DynamicOption::findOrFail($id);
    }

    public function update(UpdateDynamicOptionsRequest $request, $id)
    {
        $recurring_period = DynamicOption::findOrFail($id);
        $recurring_period->update($request->all());
        

        return $recurring_period;
    }

    public function store(StoreDynamicOptionsRequest $request)
    {
        $recurring_period = DynamicOption::create($request->all());
        

        return $recurring_period;
    }

    public function destroy($id)
    {
        $recurring_period = DynamicOption::findOrFail($id);
        $recurring_period->delete();
        return '';
    }
}
