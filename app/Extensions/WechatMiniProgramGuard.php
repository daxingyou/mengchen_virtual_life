<?php
namespace App\Extensions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\UserProvider;

class WechatMiniProgramGuard implements Guard
{
    use GuardHelpers;

    protected $request;
    protected $session;
    protected $authKey = 'auth_code';   //微信认证时传的参数名
    protected $loginKey = 'js_code';    //微信小程序登录是传的key名

    /**
     * The name of the Guard. Typically "session".
     *
     * Corresponds to guard name in authentication configuration.
     *
     * @var string
     */
    protected $name;

    public function __construct($name, UserProvider $provider, $request, $session)
    {
        $this->name = $name;
        $this->provider = $provider;
        $this->request = $request;
        $this->session = $session;
        $this->retrieveUserFromSession();
    }

    public function user()
    {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (! is_null($this->user)) {
            return $this->user;
        }
    }

    public function validate(array $credentials = [])
    {
        return ! is_null($this->user());
    }

    protected function getAuthCode()
    {
        $authCode = $this->request->query($this->authKey);
        return ! is_null($authCode) ? $authCode : '';  //不能返回null，否则session->get(null)返回的不是null
    }

    protected function retrieveUserFromSession()
    {
        $userFromSession = $this->session->get($this->getAuthCode());
        if (!empty($userFromSession)) {
            $this->setUser($userFromSession);
        };
    }
}