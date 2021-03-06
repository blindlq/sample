<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;


class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest',[
            'only' =>  ['create'],
        ]);
    }

    //
    public function create()
    {
        return view('sessions.create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $credentials = $this->validate($request,[
            'email' => 'required|email|max:255',
            'password' =>'required'
        ]);
        if(Auth::attempt($credentials,$request->has('remember'))){
            //验证是否激活
            if(Auth::user()->activated){
                session()->flash('success','欢迎回来');
                return redirect()->intended(route('users.show', [Auth::user()]));
            }else{//未激活跳转回首页
                Auth::logout();
                session()->flash('warning','你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');

            }
            //友好跳转返回之前页面
//            return redirect()->intended(route('users.show',[Auth::user()]));
        }else{
            session()->flash('danger','很抱歉，您的邮箱和密码不匹配');
            return redirect()->back();
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy()
    {
        Auth::logout();
        session()->flash('success','您已成功退出');
        return redirect('login');
    }
}
