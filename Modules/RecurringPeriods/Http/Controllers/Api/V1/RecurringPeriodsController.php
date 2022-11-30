<?php

namespace Modules\RecurringPeriods\Http\Controllers\Api\V1;

use Modules\RecurringPeriods\Entities\RecurringPeriod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\RecurringPeriods\Http\Requests\Admin\StoreRecurringPeriodsRequest;
use Modules\RecurringPeriods\Http\Requests\Admin\UpdateRecurringPeriodsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class RecurringPeriodsController extends Controller
{
    public function index()
    {
        return RecurringPeriod::all();
    }

    public function show($id)
    {
        return RecurringPeriod::findOrFail($id);
    }

    public function update(UpdateRecurringPeriodsRequest $request, $id)
    {
        $recurring_period = RecurringPeriod::findOrFail($id);
        $recurring_period->update($request->all());
        

        return $recurring_period;
    }

    public function store(StoreRecurringPeriodsRequest $request)
    {
        $recurring_period = RecurringPeriod::create($request->all());
        

        return $recurring_period;
    }

    public function destroy($id)
    {
        $recurring_period = RecurringPeriod::findOrFail($id);
        $recurring_period->delete();
        return '';
    }
}
