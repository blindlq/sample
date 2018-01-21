<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;


class UsersController extends Controller
{
    /**
     * UsersController constructor.
     */
    public function __construct()
    {
        //身份验证黑名单（以登录用户）
        $this->middleware('auth',[
            'except' => ['show','create','store','index'],
        ]);
        //guest 只允许未登录用户访问的动作
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {

        return view('users.show',compact('user'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $this->validate($request,[
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>bcrypt($request->password),
        ]);
        Auth::login($user);//注册用户自动登录
        session()->flash('success','欢迎关注并加入我们');

        return redirect()->route('users.show',[$user]);
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        //授权验证
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    /**
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(User $user,Request $request)
    {

        $this->validate($request,[
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        //授权验证
        $this->authorize('update',$user);

        $data = [];
        $data['name'] = $request->name;
        //如果修改了密码
        if($request->password){
            $data['password'] = bcrypt($request->password);
            $user->update($data);
            //提醒修改成功
            session()->flash('success','个人资料修改成功');
        }else{
            //只修改姓名
            $user->update($data);
            session()->flash('success','姓名修改成功');
        }
        //跳转回个人中心
        return redirect()->route('users.show',$user->id);

    }

    public function destroy(User $user)
    {
        //验证删除权限
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success','成功删除用户');
        return back();
    }
}
