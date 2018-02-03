<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Mail;


class UsersController extends Controller
{
    /**
     * UsersController constructor.
     */
    public function __construct()
    {
        //未登录用户访问权限
        $this->middleware('auth',[
            'except' => ['show','create','store','index','confirmEmail'],
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
        //取出用户微博
        $statuses = $user->statuses()
                            ->orderBy('created_at','desc')
                            ->paginate(10);
        return view('users.show',compact('user','statuses'));
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

        $this->sendEmailConfirmationTo($user);
        session()->flash('success','验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
//        Auth::login($user);//注册用户自动登录
//        session()->flash('success','欢迎关注并加入我们');
//
//        return redirect()->route('users.show',[$user]);
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

    public function confirmEmail($token)
    {
        $user = User::where('activation_token',$token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success','恭喜你，激活成功');

        return redirect()->route('users.show',[$user]);
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 关注人列表
     */
    public function followings(User $user)
    {
        $users = $users->followings()->paginate(30);
        $title = '我的关注';

        return view('user.show_follow',compact('users','title'));
    }

    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = '我的粉丝';

        return view('user.show_follow',compact('users','title'));
    }

    /**
     * @param $user
     * 邮件发送方法
     */
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = '906314530@qq.com';
        $name = 'ZhangCheng';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        //调用接口
        Mail::send($view,$data,function ($message) use ($from,$name,$to,$subject){
                $message->from($from,$name)->to($to)->subject($subject);
        });

    }
}
