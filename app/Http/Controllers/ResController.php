<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Food;
use App\FoodCountry;
use App\PageUrl;
use App\Comment;
use Mail;

class ResController extends Controller
{
    
    public function getTrangChu(){

    	$foods_soup = Food::with('PageUrl')->where('id_type', 6)->orderBy('id', 'DESC')->limit(6)->get();
    	$foods_meat = Food::with('PageUrl')->where('id_type', 4)->orderBy('id', 'DESC')->limit(6)->get();
    	$foods_xao	= Food::with('PageUrl')->where('id_type', 14)->orderBy('id', 'DESC')->limit(6)->get();
    	$foods_rice	= Food::with('PageUrl')->where('id_type', 3)->orderBy('id', 'DESC')->limit(6)->get();
    	$foods_desert	= Food::with('PageUrl')->where('id_type', 10)->orderBy('id', 'DESC')->limit(6)->get();
    	$foods_top	= Food::with('PageUrl')->orderBy('soluotxem', 'DESC')->limit(6)->get();

    	return view('res.page.trangchu', compact('foods_soup', 'foods_meat', 'foods_xao', 'foods_rice', 'foods_desert', 'foods_top'));
    }

    public function getDanhSachTatCaMonAn(){
    	$foods = Food::with('PageUrl')->orderBy('id', 'DESC')->paginate(6);
    	return view('res.page.cac_mon_an', compact('foods'));
    }

    public function getDanhSachTatCaCacNuoc(){
    	$country = FoodCountry::all();
    	return view('res.page.cac_nuoc', compact('country'));
    }

    public function getDanhSachMonAnTheoNuoc($id){

    	$foods = Food::with('PageUrl')->where('id_country', $id)->get();
    	$country = FoodCountry::where('id', $id)->first();
    	return view('res.page.mon_an_theo_nuoc', compact('foods', 'country'));
    }

    public function getChiTietMonAn($url){

        $_url = PageUrl::where('url', '=', $url)->first();
        $id_url = $_url->id;
    	$food = Food::where('id_url', $id_url)->first();
        $id_food = $food->id;
        $comment = Comment::where('id_food', $id_food)->get();
        $count = count($comment);
        return view('res.page.chi_tiet_mon_an', compact('food', 'comment', 'count'));
    }

    public function getTimKiem(Request $request){

        $key = $request->key;
        $foods = Food::with('PageUrl')->where('name', 'LIKE', '%'.$key.'%')->paginate(6);
        $count = Food::where('name', 'LIKE', '%'.$key.'%')->count();
        return view('res.page.cac_mon_an', compact('foods', 'key', 'count'));
    }

    public function postLienHe(Request $request){

        Mail::send('res.page.send_email', ['HoTen'=>$request->HoTen, 'NoiDung'=>$request->NoiDung], function ($message) use($request)
        {
            $message->from('mail.technologystore96@gmail.com', 'WorldFOOD');
            $message->to($request->Email,$request->HoTen);
            $message->subject('Cảm ơn bạn đã đặt câu hỏi cho chúng tôi.');
        });
        echo "<script>
   
            alert('Cảm ơn bạn đã góp ý. Chúng tôi sẽ liên lạc với bạn trong thời gian sớm nhất.');
            window.location='".url('./')."'

            </script>";
    }

    public function postBinhLuan(Request $request, $idFood){

        $comment = new Comment;
        $comment->id_food = $idFood;
        $comment->name = $request->TenNguoiBinhLuan;
        $comment->comment = $request->NoiDungBinhLuan;
        $comment->date = date('Y-m-d');
        $comment->save();
        return redirect()->back(); 
    }
}
