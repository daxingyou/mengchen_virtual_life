<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use GroupIdMap;

    protected $table = 'users';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'account', 'password', 'email', 'phone', 'group_id', 'parent_id', 'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email', 'phone'
    ];

    /**
     * 自动更新created_at和updated_at字段
     *
     * @var bool
     */
    public $timestamps = true;

    //所属组
    public function group()
    {
        return $this->hasOne('App\Models\Group', 'id', 'group_id');
    }

    //上级
    public function parent()
    {
        if ($this->parent_id == -1) {
            //如果不存在上级则parent返回它自身
            return $this->hasOne('App\Models\User', 'id', 'id');
        }
        return $this->hasOne('App\Models\User', 'id', 'parent_id');
    }

    //定义访问器
    public function getIsAdminAttribute()
    {
        return (string) $this->attributes['group_id'] === $this->adminGid;
    }

    //指定mail通知channel的地址（默认就为email字段）
    public function routeNotificationForMail()
    {
        return $this->email;
    }

    //查询是否是给定的用户id的下级
    public function isChild($parentId)
    {
        return $parentId == $this->parent_id;
    }

    //是否存在下级
    public function hasChild()
    {
        return User::where('parent_id', $this->id)->get()->count();
    }

    //下级代理商
    public function children()
    {
        return $this->hasMany('App\Models\User', 'parent_id', 'id');
    }
}
