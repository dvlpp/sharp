<?php namespace Dvlpp\Sharp\Http;

use Dvlpp\Sharp\Auth\SharpLoginFormValidator;
use Dvlpp\Sharp\Config\SharpSiteConfig;
use Dvlpp\Sharp\Exceptions\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller {

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('sharp::auth.login');
    }

    /**
     * @param Request $request
     * @param SharpLoginFormValidator $loginValidator
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException
     */
    public function login(Request $request, SharpLoginFormValidator $loginValidator)
    {
        $data = $request->all();

        try {
            // First: validation
            $loginValidator->validate($data);

            // Next: check credentials
            $authService = SharpSiteConfig::getAuthService();
            if($user = $authService->login($data["login"], $data["password"]))
            {
                // Login succeed
                \Session::put("sharp_user", $user);
                return redirect()->intended("cms");
            }
            else
            {
                return redirect()->back()
                    ->withInput()
                    ->with(["flashMessage" => trans('sharp::messages.login_invalid')]);
            }
        }

        catch(ValidationException $e)
        {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->getErrors());
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException
     */
    public function logout()
    {
        $authService = SharpSiteConfig::getAuthService();
        $authService->logout();
        \Session::forget("sharp_user");

        return redirect()->to("/");
    }

} 