<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Status;
use Auth;

class StaticpagesController extends Controller
{
    public function home()
    {
//        return '主页';
        $feed_items = [];//个人所有微博
        if(Auth::check()){
            $feed_items = Auth::user()->feed()->paginate(10);
        }
        return view('static_pages/home',compact('feed_items'));
    }

    public function help()
    {
//        return '帮助页';
        return view('static_pages/help');
    }

    public function about()
    {
//        return '关于页';
        return view('static_pages/about');
    }
}
