<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable; //消息通知相关功能引用
use Illuminate\Foundation\Auth\User as Authenticatable; //是授权相关功能的引用
use App\Notifications\ResetPassword;
use Auth;

class User extends Authenticatable
{

    // protected $table = 'users';


    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }


    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });
    }


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }



    public function statuses()
    {
        return $this->hasMany(Status::class);
    }


    public function feed()
    {
        $user_ids = Auth::user()->followings->pluck('id')->toArray(); //方法取出所有关注用户的信息
        // dump($user_ids);exit();
        array_push($user_ids, Auth::user()->id); //将当前用户的 id 加入到 user_ids 数组中
        //取出所有用户的微博动态并进行倒序排序
        return Status::whereIn('user_id', $user_ids)
            ->with('user')
            ->orderBy('created_at', 'desc');
    }


    public function followers()
    {
        return $this->belongsToMany(User::Class, 'followers', 'user_id', 'follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::Class, 'followers', 'follower_id', 'user_id');
    }

    public function follow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }

    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
