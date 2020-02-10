<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
// session_start();

class Attribute extends Controller
{
    public function index(){
    	$attr = DB::select('select * from tbl_attribute');
    	$list_attr = array();
    	foreach ($attr as $key => $value) {
    		$list_attr[$key]['id_attribute'] = $value->id_attribute;
    		$list_attr[$key]['id_attribute_group'] = $value->id_attribute_group;
    		$list_attr[$key]['name'] = $value->name;
    	}
    	// echo'<pre>';print_r($list_attr);
    	return view('admin.admin_attribute', ['admin_name' => 'Thai Thanh Tung', 'list_attr' => $list_attr]);
    }
    public function addattribute(Request $request){
    	$attribute_group = $request->attribute_group;
    	$attribute_name = $request->attribute_name;
    	$public_name = $request->attribute_public_name;
    	// echo'<pre>';print_r($category_name);
    	DB::insert('insert into tbl_attribute (id_attribute_group, name, public_name) values (?, ?, ?)', [$attribute_group, $attribute_name, $public_name]);
        return Redirect::to('admin-attribute');
    }
    public function value_attribute($id_attribute){
        $list_value = DB::select('select * from tbl_value_attribute where id_attribute = :id_attribute', ['id_attribute' => $id_attribute] );
        $is_color = DB::select('select * from tbl_attribute a inner join tbl_attribute_group b on a.id_attribute_group = b.id_attribute_group where a.id_attribute = :id_attribute', ['id_attribute' => $id_attribute] );

        // echo'<pre>';print_r($list_value);
        return view('admin.admin_addvalueattribute', ['admin_name' => 'Thai Thanh Tung', 'list_value' => $list_value, 'is_color' => $is_color[0]->is_color_group, 'id_attribute' => $id_attribute]);
    }
    public function addvalueaddtribute(Request $request){
    	$attribute_value = $request->attribute_value;
    	$attribute_color = $request->attribute_color;
    	$id_attribute = $request->id_attribute;
    	// echo'<pre>';print_r($category_name);
    	DB::insert('insert into tbl_value_attribute (id_attribute, value, color) values (?, ?, ?)', [$id_attribute, $attribute_value, $attribute_color]);
        return Redirect::to('admin-value_attribute/'.$id_attribute);
    }
    public function attvaluedelete($id_value_attr){
         DB::table('tbl_value_attribute')->where('id',$id_value_attr)->delete();
        echo'<pre>';print_r($id_value_attr);
        // echo'<pre>';print_r($id_attribute);
        // return Redirect::to('admin-value_attribute');
        return redirect()->back()->with('thongbao','Xoa thanh cong');
    }
}
