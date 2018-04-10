<?php
namespace App\Extensions;

use App\Models\Players;
use GuzzleHttp\Client;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\UserProvider;
use Overtrue\Socialite\User;

class WechatWebOauthGuard implements Guard
{
    use GuardHelpers;

    protected $request;
    protected $session;
    protected $wechatConfigAccount = 'default'; //微信配置文件的默认配置

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

    protected function retrieveUserFromSession()
    {
        $sessionKey = 'wechat.oauth_user.' . $this->wechatConfigAccount;
        $userInfo = $this->session->get($sessionKey);
        if (!empty($userInfo)) {
            $player = $this->getPlayer($userInfo);  //通过用户的微信信息获取Players模型
            $this->setUser($player);
        };
    }

    /**
     * @param \Overtrue\Socialite\User $user
     * @return \App\Models\Players
     */
    protected function getPlayer(User $user)
    {
        $openId = $user->getId();
        $player = Players::where('openid', $openId)->first();
        if (empty($player)) {
            $player = $this->createPlayer($user);
        }
        return $player;
    }

    /**
     * @param \Overtrue\Socialite\User $user
     * @return \App\Models\Players
     */
    protected function createPlayer(User $user)
    {
        $userInfo = $user->getOriginal();
        $player = Players::create([
            'openid' => $userInfo['openid'],
            'nickname' => $userInfo['nickname'],
            'gender' => $userInfo['sex'],
        ]);

        //下载图片到本地
        $subDir = 'avatar';
        $avatarUrl = config('filesystems.disks.wechat.root') . '/' . $subDir . '/' . $player->id;
        $httpClient = new Client([
            'verify' => false,
        ]);
        $httpClient->get($userInfo['headimgurl'], [
            'sink' => $avatarUrl,
        ]);

        return $player;
    }
}