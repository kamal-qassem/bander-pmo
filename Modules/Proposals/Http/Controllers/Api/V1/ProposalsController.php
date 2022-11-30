<?php

namespace Modules\Proposals\Http\Controllers\Api\V1;

use Modules\Proposals\Entities\Proposal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProposalsRequest;
use App\Http\Requests\Admin\UpdateProposalsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ProposalsController extends Controller
{
    public function index()
    {
        return Proposal::all();
    }

    public function show($id)
    {
        return Proposal::findOrFail($id);
    }

    public function update(UpdateProposalsRequest $request, $id)
    {
        $recurring_invoice = Proposal::findOrFail($id);
        $recurring_invoice->update($request->all());
        

        return $recurring_invoice;
    }

    public function store(StoreProposalsRequest $request)
    {
        $recurring_invoice = Proposal::create($request->all());
        

        return $recurring_invoice;
    }

    public function destroy($id)
    {
        $recurring_invoice = Proposal::findOrFail($id);
        $recurring_invoice->delete();
        return '';
    }
}
