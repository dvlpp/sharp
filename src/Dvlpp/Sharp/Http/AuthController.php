<?php

namespace Dvlpp\Sharp\Http;

use Dvlpp\Sharp\Http\Request\LoginFormRequest;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class AuthController extends Controller
{
    use ThrottlesLogins;

    /**
     * @var Guard
     */
    private $auth;

    public function __construct()
    {
        parent::__construct();

        $this->auth = sharp_auth_guard();
    }

    /**
     * Show login form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('sharp::auth.login');
    }

    /**
     * Try to login.
     *
     * @param LoginFormRequest $request
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException
     */
    public function login(LoginFormRequest $request)
    {
        // First: throttles
        if ($this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = [
            $this->username() => $request->get("login"),
            "password" => $request->get("password")
        ];

        if ($this->auth->attempt($credentials, $request->has('remember'))) {
            // Login OK

            if (!is_sharp_user()) {
                // User exists, but he's not a sharp user...
                $this->auth->logout();

            } else {
                // User OK
                $this->clearLoginAttempts($request);

                return redirect()->intended("admin/cms");
            }
        }

        $this->incrementLoginAttempts($request);

        return redirect()->back()
            ->withInput()
            ->withErrors([
                $this->username() => trans('sharp::messages.login_invalid')
            ]);
    }

    /**
     * Logout from Sharp.
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException
     */
    public function logout()
    {
        $this->auth->logout();

        return redirect()->to("/");
    }

    /**
     * Get username field name (used by throttle trait)
     *
     * @return string
     */
    protected function username()
    {
        return get_user_login_field_name();
    }

    /**
     * @deprecated
     * @return string
     */
    protected function loginUsername()
    {
        return $this->username();
    }

    /**
     * Get login form path (used by throttle trait)
     *
     * @return string
     */
    protected function loginPath()
    {
        return "/admin/login";
    }

} 