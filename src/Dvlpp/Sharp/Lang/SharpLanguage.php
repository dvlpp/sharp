<?php

namespace Dvlpp\Sharp\Lang;

class SharpLanguage {

    public static function current()
    {
        $languages = config("sharp.languages");

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