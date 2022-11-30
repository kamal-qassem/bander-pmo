<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 *
 * @package App
 * @property string $title
*/
class Role extends Model
{
    
    protected $fillable = ['title', 'name', 'color', 'type', 'description'];
    

    public static function storeValidation($request)
    {
        return [
            'title' => 'max:191|required',
            'permission' => 'array|required',
            'permission.*' => 'integer|exists:permissions,id|max:4294967295|required',
            'color' => 'required',
        ];
    }

    public static function updateValidation($request)
    {
        return [
            'title' => 'max:191|required',
            'permission' => 'array|required',
            'permission.*' => 'integer|exists:permissions,id|max:4294967295|required',
            'color' => 'required',
        ];
    }


    
    public function permission()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function role_widgets()
    {
        return $this->belongsToMany(\App\DashBoard::class, 'dashboard_widgets_role', 'role_id', 'dash_board_id');
    }
    
    
}
