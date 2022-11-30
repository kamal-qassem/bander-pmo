<?php

namespace App\Http\Controllers\Api\V1;

use App\MileStone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMileStonesRequest;
use App\Http\Requests\Admin\UpdateMileStonesRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class MileStonesController extends Controller
{
    public function index()
    {
        return MileStone::all();
    }

    public function show($id)
    {
        return MileStone::findOrFail($id);
    }

    public function update(UpdateMileStonesRequest $request, $id)
    {
        $mile_stone = MileStone::findOrFail($id);
        $mile_stone->update($request->all());
        

        return $mile_stone;
    }

    public function store(StoreMileStonesRequest $request)
    {
        $mile_stone = MileStone::create($request->all());
        

        return $mile_stone;
    }

    public function destroy($id)
    {
        $mile_stone = MileStone::findOrFail($id);
        $mile_stone->delete();
        return '';
    }
}
