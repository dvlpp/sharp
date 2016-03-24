<?php

namespace Dvlpp\Sharp\Http\Middleware;

trait WithLang
{
    function addLangToView()
    {
        $language = session("sharp_lang");

        if (sharp_languages()) {
            if (!$language || !array_key_exists($language, sharp_languages())) {
                $language = array_values(sharp_languages())[0];
            } else {
                $language = sharp_languages()[$language];
            }
        }

        view()->share('language', $language);
    }
}