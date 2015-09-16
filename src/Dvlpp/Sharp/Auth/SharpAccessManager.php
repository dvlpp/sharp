<?php namespace Dvlpp\Sharp\Auth;


use Dvlpp\Sharp\Config\SharpSiteConfig;

class SharpAccessManager
{

    /**
     * Return true if current user is authorized for the action.
     *
     * @param $type string view, update, create or delete
     * @param $action
     * @param $key
     * @return bool
     * @throws \Dvlpp\Sharp\Exceptions\MandatoryClassNotFoundException
     */
    static function granted($type, $action, $key)
    {
        $authService = SharpSiteConfig::getAuthService();

        if (!$authService) {
            // No access management
            return true;
        }

        return $authService->checkAccess(session("sharp_user"), $type, $action, $key);
    }

} 