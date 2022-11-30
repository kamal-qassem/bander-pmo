<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Harimayco\Menu\Controllers\MenuController;
use Harimayco\Menu\Models\Menus;
use Harimayco\Menu\Models\MenuItems;
use Harimayco\Menu\Facades\Menu;

class NavigationMenuesController extends MenuController
{
    public function index()
    {
        if (! Gate::allows('navigation_menue_access')) {
            return prepareBlockUserMessage();
        }
        return view('admin.navigation_menues.index');
    }

    public function additem()
    {

        $theme = \Cookie::get('theme');
        if ( empty( $theme ) ) {
            $theme = 'default';
        }
        $menuitem = new MenuItems();
        $menuitem->label = request()->input("labelmenu");
        $menuitem->link = request()->input("linkmenu");
        $menuitem->menu = request()->input("idmenu");
        $menuitem->permission = request()->input("permission");
        $menuitem->icon_html = request()->input("icon_html");
        $menuitem->sort = MenuItems::getNextSortRoot(request()->input("idmenu"));
        $menuitem->theme = $theme;
        $menuitem->save();

    }

    public function updateitem()
    {
        $theme = \Cookie::get('theme');
        if ( empty( $theme ) ) {
            $theme = 'default';
        }
        
        $arraydata = request()->input("arraydata");
        if (is_array($arraydata)) {
            foreach ($arraydata as $value) {
                $menuitem = MenuItems::find($value['id']);
                $menuitem->label = $value['label'];
                $menuitem->link = $value['link'];
                $menuitem->class = $value['class'];
                $menuitem->permission = ! empty($value['permission']) ? $value['permission'] : '';
                $menuitem->icon_html = $value['icon_html'];
                $menuitem->theme = $theme;
                $menuitem->save();
            }
        } else {
            $menuitem = MenuItems::find(request()->input("id"));
            $menuitem->label = request()->input("label");
            $menuitem->link = request()->input("url");
            $menuitem->class = request()->input("clases");
            $menuitem->permission = request()->input("permission");
            $menuitem->icon_html = request()->input("icon_html");
            $menuitem->theme = $theme;
            $menuitem->save();
        }
    }
}