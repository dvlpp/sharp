<?php namespace Dvlpp\Sharp\Auth;


use Dvlpp\Sharp\Config\SharpSiteConfig;

class SharpAccessManager {

    static function granted($type, $action, $key)
    {
        $authService = SharpSiteConfig::getAuthService();

        if(!$authService)
        {
            // No access management
            return true;
        }

        return $authService->checkAccess(\Session::get("sharp_user"), $type, $action, $key);
    }

} 