<?php namespace Dvlpp\Sharp\Lang;

use Dvlpp\Sharp\Config\SharpSiteConfig;
use Session;

class SharpLanguage {

    function current()
    {
        $languages = SharpSiteConfig::getLanguages();

        if($languages)
        {
            $lang = Session::get("sharp_lang");
            if(!$lang || !array_key_exists($lang, $languages))
            {
                $lang = array_keys($languages)[0];
                Session::put("sharp_lang", $lang);
            }

            return $lang;
        }

        return null;
    }

} 