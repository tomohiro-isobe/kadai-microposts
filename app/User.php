<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    public function follow($userId) { // 既にフォローしているかの確認
    
    $exist = $this->is_following($userId); //自分自身ではないかの確認
    
    $its_me = $this->id == $userId;
    
        if ($exist || $its_me) {
            
            //既にフォローしていれば何もしない
            
            return false;
        } else {
            
            //未フォローであればフォローする
            
            $this->followings()->attach($userId);
            
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        //既にフォローしているかの確認
        $exist = $this->is_following($userId);
        //自分自身ではないかの確認
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            //既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            //未フォローであれば何もしない
            return false;
        }
    }
    
    public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()->lists('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    
    // お気に入りに追加、削除機能
    public function is_favoriteing($micropostId) {
        
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
    }
    
    
    public function favorite($micropostId) {
        $exits = $this->is_favoriteing($micropostId);
        
        if ($exits) {
            
            return false;
        } else {
            
            $this->favorites()->attach($micropostId);
            
            return true;
        }
    }
    
    public function unfavorite($micropostId) {
        $exits = $this->is_favoriteing($micropostId);
        
        if ($exits) {
            
            $this->favorites()->detach($micropostId);
            
            return true;
        } else {
            
            return false;
        }
    }
    
    //タイムライン表示
    public function feed_favorites()
    {
        $favorite_user_ids = $this->favorites()->lists('microposts.id')->toArray();
        
        return Micropost::whereIn('user_id', $favorite_user_ids);
    }
    
}
