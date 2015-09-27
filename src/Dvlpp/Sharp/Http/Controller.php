<?php

namespace Dvlpp\Sharp\Http;

use Dvlpp\Sharp\Config\SharpConfig;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        // Load categories
        $categories = SharpConfig::listCategories();
        view()->share('cmsCategories', $categories);

        // Get current language
        $language = session("sharp_lang");
        $languages = SharpConfig::getLanguages();
        if ($languages) {
            if (!$language || !array_key_exists($language, $languages)) {
                $language = array_values($languages)[0];
            } else {
                $language = $languages[$language];
            }
        }
        view()->share('language', $language);

        // Get sharp version
        view()->share('sharpVersion', file_get_contents(__DIR__ . "/../../../../version.txt"));
    }
}