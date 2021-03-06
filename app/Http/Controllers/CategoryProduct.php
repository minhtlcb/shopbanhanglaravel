<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CategoryProduct extends Controller
{
    public function index(){
    	$cate = DB::select('select * from tbl_category');
    	$list_cate = array();
    	foreach ($cate as $key => $value) {
    		$list_cate[$key]['id'] = $value->id;
    		$list_cate[$key]['cate_name'] = $value->category_name;
    		$list_cate[$key]['cate_desc'] = $value->category_desc;
    	}
        $product = DB::select('select * from tbl_product');
        $list_pro = array();
        foreach ($product as $key => $value) {
            $list_pro[$key]['id'] = $value->id;
            $list_pro[$key]['pro_name'] = $value->name;
            $list_pro[$key]['pro_qty'] = $value->quantity;
            $list_att = $value->attribute;
            $list_pro[$key]['pro_attr'] = explode(",",$list_att);
            $list_pro[$key]['pro_cate'] = $value->category;
            $list_pro[$key]['pro_desc'] = $value->description;
            $list_pro[$key]['pro_price'] = $value->price;
            $pro_img = $value->image;
            $list_pro[$key]['pro_image'] = explode(",",$pro_img);
            foreach ($list_cate as $key1 => $value1) {
                if ($list_pro[$key]['pro_cate'] == $value1['id']) {
                    $list_pro[$key]['pro_cate'] = $value1['cate_name'];
                }
            }
        }
        // echo'<pre>';print_r($list_pro);
    	return view('admin.admin_product', ['admin_name' => 'Thai Thanh Tung', 'list_cate' => $list_cate, 'list_pro' => $list_pro]);
    }
    public function addproduct(Request $request){
        $arr_img = array();
        $data = array();
        $data['name'] = $request->product_name;
        $data['quantity'] = $request->product_qty;
        $data['category'] = $request->product_cate;
        $data['attribute'] = rtrim($request->list_attribute,",");
        $data['description'] = $request->product_desc;
        $data['price'] = $request->product_price;
        $data['image'] = '';
         // echo'<pre>';print_r($data);
        DB::table('tbl_product')->insert($data);
        $id_pro_new = DB::getPdo()->lastInsertId();
        $get_image = $request->file('product_image');
        if ($get_image) {
            foreach($get_image as $key => $get_image){
                $name_image = $id_pro_new.'_'.$key.'.'.$get_image->getClientOriginalExtension();
                $get_image->move('public/uploads/product',$name_image);
                $arr_img[] = $name_image;
           }
       }
        $data['image'] = implode(",",$arr_img);
        DB::table('tbl_product')->where('id',$id_pro_new)->update($data);
        
        // Session::put('message', 'Add Category success!');
        return Redirect::to('admin-product');
    }
    public function editproduct($id_product_select){
        $cate = DB::select('select * from tbl_category');
        $list_cate = array();
        foreach ($cate as $key => $value) {
            $list_cate[$key]['id'] = $value->id;
            $list_cate[$key]['cate_name'] = $value->category_name;
            $list_cate[$key]['cate_desc'] = $value->category_desc;
        }
        $pro_edit = DB::select('select * from tbl_product where id = :id', ['id' => $id_product_select] );
        $pro_edit[0]->attribute = explode(",",$pro_edit[0]->attribute);
        $pro_edit[0]->image = explode(",",$pro_edit[0]->image);
        // echo'<pre>';print_r($pro_edit);
        return view('admin.admin_editproduct', ['admin_name' => 'Thai Thanh Tung', 'pro_edit' => $pro_edit[0], 'list_cate' => $list_cate]);
    }
    public function updateproduct(Request $request,$product_update_id){
        $data = array();
        $data['name'] = $request->product_name;
        $data['quantity'] = $request->product_qty;
        $data['category'] = $request->product_cate;
        $data['description'] = $request->product_desc;
        $data['price'] = $request->product_price;
        $get_image = $request->file('product_image');
        if ($get_image) {
            $new_image = rand(0,99).'.'.$get_image->getClientOriginalExtension();
            $get_image->move('public/uploads/product',$new_image);
            $data['image'] = $new_image;
        }
        // echo'<pre>';print_r($product_update_id);
        DB::table('tbl_product')->where('id',$product_update_id)->update($data);
        
        return Redirect::to('admin-product');
    }
    public function deleteproduct($id_product_delete){
        DB::table('tbl_product')->where('id',$id_product_delete)->delete();
        // echo'<pre>';print_r($cate_edit);
        return Redirect::to('admin-product');
    }
    public function getSearchAjax(Request $request){
        if($request->get('key_word'))
        {
            $key_word = $request->get('key_word');
            $data = DB::table('tbl_product')->where('name', 'LIKE', "%{$key_word}%")->get();
            $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
            foreach($data as $value)
            {
               $output .= '<li><a href="admin-editproduct/'. $value->id .'">'.$value->name.'</a></li>';
            }
            $output .= '</ul>';
            echo $output;
       }
    }
}
