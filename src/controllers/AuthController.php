<?php

use Dvlpp\Sharp\Auth\SharpLoginFormValidator;
use Dvlpp\Sharp\Config\SharpSiteConfig;
use Dvlpp\Sharp\Exceptions\ValidationException;

class AuthController extends BaseController {

    /**
     * @var Dvlpp\Sharp\Auth\SharpLoginFormValidator
     */
    protected $loginValidator;

    function __construct(SharpLoginFormValidator $validator)
    {
        $this->loginValidator = $validator;
    }

    public function index()
    {
        return View::make('sharp::auth.login');
    }

    public function login()
    {
        $data = Input::all();

        try {
            // First: validation
            $this->loginValidator->validate($data);

            // Next: check credentials
            $authService = SharpSiteConfig::getAuthService();
            if($user = $authService->login($data["login"], $data["password"]))
            {
                // Login succeed
                Session::put("sharp_user", $user);
                return Redirect::intended("cms");
            }
            else
            {
                return Redirect::back()->withInput()->with(["flashMessage" => Lang::get('sharp::messages.login_invalid')]);
            }
        }

        catch(ValidationException $e)
        {
            return Redirect::back()->withInput()->withErrors($e->getErrors());
        }
    }

    public function logout()
    {
        $authService = SharpSiteConfig::getAuthService();
        $authService->logout();
        Session::forget("sharp_user");

        return Redirect::to("/");
    }

} 