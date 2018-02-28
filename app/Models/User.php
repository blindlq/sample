<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @param string $size
     * @return img_url
     */
    function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));

        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($user){
            $user->activation_token = str_random(30);
        });


    }

    /**
     * @param string $token
     * 调用消息通知
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * 用户与博客 1对多关系
     */
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    /**
     * @return $this
     * 取出用户所有文章
     */
    public function feed()
    {
        //取出所有关注用户的id
        $user_ids = Auth::user()->followings->pluck('id')->toArray();
        //将当前用户加入$user_ids
        array_push($user_ids,Auth::user()->id);

        return Status::whereIn('user_id',$user_ids)
                                ->with('user')
                                ->orderBy('created_at','desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * 粉丝关系多对多
     */
    public function followers()
    {
        return $this->belongsToMany(User::class,'followers','user_id','follower_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followings()
    {
        return $this->belongsToMany(User::class,'followers','follower_id','user_id');
    }

    /**
     * @param $user_ids
     * 注册关注事件
     */
    public function follow($user_ids)
    {
        if(!is_array($user_ids)){
            $user_ids = compact('user_ids');
        }

        $this->followings()->sync($user_ids,false);
    }

    /**
     * @param $user_ids
     * 取消关注事件
     */
    public function unfollow($user_ids)
    {

        if(!is_array($user_ids)){
            $user_ids = compact('user_ids');
        }

        $this->followings()->detach($user_ids);
    }

    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
