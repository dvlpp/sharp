<?php namespace Dvlpp\Sharp\Auth\Impl\Config;

use Dvlpp\Sharp\Auth\SharpAuth as SharpAuthInterface;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Session\Store;

/**
 * Class SharpAuthConfigImpl
 * @package Sharp\Auth
 *
 * A simple SharpAuth implementation with single power-user
 * and login / pwd declared in config file (or most likely in .env file)
 */
class SharpAuth implements SharpAuthInterface {

    protected $user = false;

    /**
     * @var Repository
     */
    private $config;
    /**
     * @var Store
     */
    private $session;

    function __construct(Repository $config, Store $session)
    {
        $this->config = $config;
        $this->session = $session;
    }

    public function checkAdmin()
    {
        return $this->getUser();
    }

    public function login($login, $password)
    {
        if($login == $this->config->get("sharp.auth_user")
            && $password == $this->config->get("sharp.auth_pwd"))
        {
            $this->session->set("sharp_user", $login);
            return $login;
        }

        $this->logout();

        return false;
    }

    public function logout()
    {
        $this->session->forget("sharp_user");
    }

    public function checkAccess($user, $type, $action, $key)
    {
        return $this->getUser();
    }

    public function getUser()
    {
        if($this->user == false)
        {
            $this->user = $this->session->get("sharp_user");
        }

        return $this->user;
    }
}