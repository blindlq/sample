<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Auth;

class FollowersController extends Controller
{
    //
    /**
     * FollowersController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * 关注动作
     */
    public function store(User $user)
    {
        //当前用户是自己则跳到首页
        if(Auth::user() === $user->id ){
            redirect('/');
        }

        if(!Auth::user()->isFollowing($user->id)){
            Auth::user()->follow($user->id);
        }

        return redirect()->route('users.show', $user->id);

    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * 取消关注动作
     */
    public function destroy(User $user)
    {

        //当前用户是自己则跳到首页
        if(Auth::user() === $user->id ){
            redirect('/');
        }

        if (Auth::user()->isFollowing($user->id)) {
            Auth::user()->unfollow($user->id);
        }

        return redirect()->route('users.show', $user->id);
    }
}