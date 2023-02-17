<?php

namespace Fatura\Core\Controllers;

use Illuminate\Routing\Controller;
use DashboardTraits;

class AclController extends Controller
{
  

    public function __construct()
    {
        $this->getNavigationBar();
        $this->getWidgets();
    }


    public function getIndex()
    {
        $title = 'Manage User Role Privilege';
​
     if (\Auth::guard('admin')) {
            $result = [];
            // For Super Admin
​
                $result = \DB::table('acl_permission')
                    ->Where('role_id', \Input::get('id'))
                    ->get();
​
        }  
        else {
            return redirect('admin/dashboard')->with(['error' => 'Unauthorized action detected']);
        }
​
        return \View::make('Dashboard::user.aclform', compact('title', 'result'));
    }
​
    protected function setPermission()
    {
        $role_id = \Input::get('role_id');
​
​
​
        if ((int)$role_id == 0) {
            return redirect('admin/aclmanager')->with(['error' => 'Please select proper roles and permission']);
        }
​
​
        \DB::table('acl_permission')
            ->Where('role_id', $role_id)
            ->delete();
​
        $main_menu_text = \Input::get('main_menu_text');
        $main_menu_icon = \Input::get('main_menu_icon');
        $main_menu_order = \Input::get('main_menu_order');
        $main_menu_acl_key = \Input::get('main_menu_acl_key');
        $data = [];
        for ($i = 0; $i < sizeof($main_menu_text); $i++) {
            $main_menu_item = !empty(\Input::has('main_menu_view_' . $i)) ?
                ((\Input::get('main_menu_view_' . $i) == 'on') ? 1 : 0)
                : 0;
​
            $data[] = [
                'role_id' => $role_id,
                'link' => '',
                'icon' => $main_menu_icon[$i],
                'menu_text' => $main_menu_text[$i],
                'acl_key' => $main_menu_acl_key[$i],
                'level' => 0,
                'ordering' => !empty($main_menu_order[$i]) ? $main_menu_order[$i] : 0,
                'parent_menu' => 0,
                'view' => $main_menu_item,
                'created_at' => date("Y-m-d h:i:s"),
            ];
        }
​
        \DB::table('acl_permission')->insert($data);
​
        $sub_menu_text = \Input::get('sub_menu_text');
        $sub_menu_icon = \Input::get('sub_menu_icon');
        $sub_menu_order = \Input::get('sub_menu_order');
        $sub_menu_acl_key = \Input::get('sub_menu_acl_key');
        $sub_menu_parent_acl_key = \Input::get('sub_menu_parent_acl_key');
        $data = [];
        for ($i = 0; $i < sizeof($sub_menu_text); $i++) {
            $sub_menu_item = !empty(\Input::has('sub_menu_view_' . $i)) ?
                ((\Input::get('sub_menu_view_' . $i) == 'on') ? 1 : 0)
                : 0;
            $subdata[] = [
                'role_id' => $role_id,
                'link' => '',
                'icon' => $sub_menu_icon[$i],
                'menu_text' => $sub_menu_text[$i],
                'acl_key' => $sub_menu_acl_key[$i],
                'level' => 1,
                'ordering' => !empty($sub_menu_order[$i]) ? $sub_menu_order[$i] : 0,
                'parent_menu' => \DB::table('acl_permission')
                    ->Where('acl_key', $sub_menu_parent_acl_key[$i])
                    ->Where('role_id', $role_id)
                    ->value('id'),
                'view' => $sub_menu_item,
                'created_at' => date("Y-m-d h:i:s"),
​
            ];
        }
        \DB::table('acl_permission')->insert($subdata);
​
        $sub_sub_menu_text = \Input::get('sub_sub_menu_text');
        $sub_sub_menu_link = \Input::get('sub_sub_menu_link');
        $sub_sub_menu_order = \Input::get('sub_sub_menu_order');
        $sub_sub_menu_acl_key = \Input::get('sub_sub_menu_acl_key');
        $sub_sub_menu_parent_acl_key = \Input::get('sub_sub_menu_parent_acl_key');
​
        $sub_sub_menu_vendor = \Input::get('sub_sub_menu_vendor');
        $sub_sub_menu_namespace = \Input::get('sub_sub_menu_namespace');
        $sub_sub_menu_model = \Input::get('sub_sub_menu_model');
​
​
        $subdata = [];
        for ($i = 0; $i < sizeof($sub_sub_menu_text); $i++) {
            $sub_sub_menu_views = !empty(\Input::has('sub_sub_menu_view_' . $i)) ?
                ((\Input::get('sub_sub_menu_view_' . $i) == 'on') ? 1 : 0)
                : 0;
            $sub_sub_menu_adds = !empty(\Input::has('sub_sub_menu_add_' . $i)) ?
                ((\Input::get('sub_sub_menu_add_' . $i) == 'on') ? 1 : 0)
                : 0;
            $sub_sub_menu_edits = !empty(\Input::has('sub_sub_menu_edit_' . $i)) ?
                ((\Input::get('sub_sub_menu_edit_' . $i) == 'on') ? 1 : 0)
                : 0;
            $sub_sub_menu_deleted = !empty(\Input::has('sub_sub_menu_delete_' . $i)) ?
                ((\Input::get('sub_sub_menu_delete_' . $i) == 'on') ? 1 : 0)
                : 0;
            $subsubdata[] = ['role_id' => $role_id
                , 'icon' => ''
                , 'link' => $sub_sub_menu_link[$i]
                , 'menu_text' => $sub_sub_menu_text[$i]
                , 'acl_key' => $sub_sub_menu_acl_key[$i]
                , 'level' => 2
                , 'ordering' => !empty($sub_sub_menu_order[$i]) ? $sub_sub_menu_order[$i] : 0
                , 'parent_menu' => \DB::table('acl_permission')
                    ->Where('acl_key', $sub_sub_menu_parent_acl_key[$i])
                    ->Where('role_id', $role_id)
                    ->value('id')
                , 'view' => $sub_sub_menu_views
                , 'adding' => $sub_sub_menu_adds
                , 'edit' => $sub_sub_menu_edits
                , 'trash' => $sub_sub_menu_deleted
​
                , 'vendor' => $sub_sub_menu_vendor[$i]
                , 'namespace' => $sub_sub_menu_namespace[$i]
                , 'model' => $sub_sub_menu_model[$i]
                , 'created_at' => date("Y-m-d h:i:s"),
​
            ];
        }
        \DB::table('acl_permission')->insert($subsubdata);
​
        return redirect('admin/aclmanager?id=' . $role_id)->with(['message' => 'Permission set successfully!']);
    }

}
