<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Status;
use Auth;

class StatusesController extends Controller
{
    //
    /**
     * StatusesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        //内容字段验证
        $this->validate($request,[
            'content' => 'required|max:140'
        ]);

        Auth::user()->statuses()->create([
            'content' => $request->content
        ]);
        //用户完成微博的创建之后，需要将其导向至上一次发出请求的页面，即网站主页
        return redirect()->back();
    }

    public function destroy(Status $status)
    {

        $this->authorize('destroy',$status);

        $status->delete();
        session()->flash('success', '微博已被成功删除！');
        return redirect()->back();
    }

}
