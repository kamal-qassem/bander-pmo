<?php

namespace Modules\DatabaseBackup\Http\Controllers\Api\V1;

use Modules\DatabaseBackup\Entities\DatabaseBackup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\DatabaseBackup\Http\Requests\Admin\StoreDatabaseBackupsRequest;
use Modules\DatabaseBackup\Http\Requests\Admin\UpdateDatabaseBackupsRequest;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class DatabaseBackupsController extends Controller
{
    public function index()
    {
        return DatabaseBackup::all();
    }

    public function show($id)
    {
        return DatabaseBackup::findOrFail($id);
    }

    public function update(UpdateDatabaseBackupsRequest $request, $id)
    {
        $database_backup = DatabaseBackup::findOrFail($id);
        $database_backup->update($request->all());
        

        return $database_backup;
    }

    public function store(StoreDatabaseBackupsRequest $request)
    {
        $database_backup = DatabaseBackup::create($request->all());
        

        return $database_backup;
    }

    public function destroy($id)
    {
        $database_backup = DatabaseBackup::findOrFail($id);
        $database_backup->delete();
        return '';
    }
}
