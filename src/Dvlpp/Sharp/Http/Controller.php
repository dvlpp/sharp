<?php

namespace Dvlpp\Sharp\Http;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        // Get current language
        $language = session("sharp_lang");
        if (sharp_languages()) {
            if (!$language || !array_key_exists($language, sharp_languages())) {
                $language = array_values(sharp_languages())[0];
            } else {
                $language = sharp_languages()[$language];
            }
        }
        view()->share('language', $language);

        // Get sharp version
        view()->share('sharpVersion', file_get_contents(__DIR__ . "/../../../../version.txt"));
    }
}