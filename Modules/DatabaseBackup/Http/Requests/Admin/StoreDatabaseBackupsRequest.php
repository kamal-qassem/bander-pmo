<?php
namespace Modules\DatabaseBackup\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDatabaseBackupsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:database_backups,name,'.$this->route('database_backup'),
            
        ];
    }
}
