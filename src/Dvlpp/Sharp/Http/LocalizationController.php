<?php

namespace Dvlpp\Sharp\Http;

use Dvlpp\Sharp\Config\SharpConfig;

class LocalizationController extends Controller
{
    /**
     * @var array
     */
    protected $languages;

    /**
     * LocalizationController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->languages = SharpConfig::getLanguages();
    }


    /**
     * Switch current language, and redirects back
     *
     * @param $lang
     * @return mixed
     */
    public function change($lang)
    {
        if($this->languages) {
            if (!$lang || !array_key_exists($lang, $this->languages)) {
                $lang = array_values($this->languages)[0];
            }

            session()->put("sharp_lang", $lang);
        }

        return redirect()->back();
    }

}