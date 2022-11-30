<?php

namespace Modules\Contracts\Http\Controllers\Admin;

use Modules\Contracts\Entities\ContractType;
use Modules\Contracts\Entities\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Modules\Contracts\Http\Requests\Admin\StoreContractTypesRequest;
use Modules\Contracts\Http\Requests\Admin\UpdateContractTypesRequest;
use App\Http\Controllers\Traits\FileUploadTrait;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ContractTypesController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of ContractTypes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('contract_type_access')) {
            return prepareBlockUserMessage();
        }


                $contract_types = ContractType::all()->sortByDesc('id');

        return view('contracts::admin.contracts.contract_types.index', compact('contract_types'));
    }

    /**
     * Show the form for creating new ContractTypes.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('contract_type_create')) {
            return prepareBlockUserMessage();
        }
        return view('contracts::admin.contracts.contract_types.create');
    }

    /**
     * Store a newly created ContractTypes in storage.
     *
     * @param  \App\Http\Requests\StoreContractTypesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContractTypesRequest $request)
    {
        if (! Gate::allows('contract_type_create')) {
            return prepareBlockUserMessage();
        }
         if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
         }

        if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contract_type = ContractType::create($request->all());


        flashMessage( 'success', 'create' );
        return redirect()->route('admin.contract_types.index')->with(['message' => trans( 'custom.messages.record_saved'), 'status' => 'success']);
    }


    /**
     * Show the form for editing ContractType.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('contract_type_edit')) {
            return prepareBlockUserMessage();
        }
        $contract_type = ContractType::findOrFail($id);
        //dd($contract_type);

        return view('contracts::admin.contracts.contract_types.edit', compact('contract_type'));
    }

    /**
     * Update ContractType in storage.
     *
     * @param  \App\Http\Requests\UpdateContractTypesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContractTypesRequest $request, $id)
    {
        if (! Gate::allows('contract_type_edit')) {
            return prepareBlockUserMessage();
        }
        if ( ! isDemo() ) {
        $request = $this->saveFiles($request);
         }
        $contract_type = ContractType::findOrFail($id);

         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contract_type->update($request->all());


        flashMessage( 'success', 'update' );
        return redirect()->route('admin.contract_types.index')->with(['message' => trans( 'custom.messages.record_updated'), 'status' => 'success']);
    }


    /**
     * Display ContractType.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  
    public function show($id)
    {
        if (! Gate::allows('contract_type_view')) {
            return prepareBlockUserMessage();
        }
        $contracts = \Modules\Contracts\Entities\Contract::where('id', $id)->get();
        

        $contract_type = ContractType::findOrFail($id);

        return view('contracts::admin.contracts.contract_types.show', compact('contract_type', 'contracts'));
    }


    /**
     * Remove ContractType from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('contract_type_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        $contract_type = ContractType::findOrFail($id);
        $contract_type->delete();

        flashMessage( 'success', 'delete' );
        return redirect()->route('admin.contract_types.index')->with(['message' => trans( 'custom.messages.record_deleted'), 'status' => 'success']);
    }

    /**
     * Delete all selected ContractType at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('contract_type_delete')) {
            return prepareBlockUserMessage();
        }
         if ( isDemo() ) {
         return prepareBlockUserMessage( 'info', 'crud_disabled' );
        }
        if ($request->input('ids')) {
            $entries = ContractType::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
            session()->flash('status', 'success' );
            session()->flash('message', trans( 'custom.messages.records_deleted' ) );
        }
    }

}
