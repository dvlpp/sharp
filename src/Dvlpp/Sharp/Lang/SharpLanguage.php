<?php namespace Dvlpp\Sharp\Lang;

use Dvlpp\Sharp\Config\SharpSiteConfig;

class SharpLanguage {

    function current()
    {
        $languages = SharpSiteConfig::getLanguages();

        if($languages) {
            $lang = session("sharp_lang");

            if(!$lang || !array_key_exists($lang, $languages)) {
                $lang = array_keys($languages)[0];
                session()->put("sharp_lang", $lang);
            }

            return $lang;
        }

        return null;
    }

} 