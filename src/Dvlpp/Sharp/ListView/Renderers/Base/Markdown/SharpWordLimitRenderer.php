<?php namespace Dvlpp\Sharp\ListView\Renderers\Base\Markdown;

use Dvlpp\Sharp\ListView\Renderers\SharpRenderer;
use Illuminate\Support\Str;
use HTML;

class SharpWordLimitRenderer implements SharpRenderer {

    function render($instance, $key, $options)
    {
        if($instance->$key)
        {
            $limit = 100;
            if($options && intval($options))
            {
                $limit = intval($options);
            }

            $str = sharp_markdown($instance->$key);

            $strW = Str::words($str, $limit, '');

            $append = strlen($str) != strlen($strW) ? '...' : '';

            return '<div class="markdown-limit-renderer">'
                . $this->replaceParagraphsByBr($this->closeTags($strW)) . $append
                . '</div>';
        }

        return null;
    }

    private function closeTags($html)
    {
        $single_tags = ['meta', 'img', 'br', 'link', 'area', 'input', 'hr', 'col', 'param', 'base'];

        preg_match_all('~<([a-z0-9]+)(?: .*)?(?<![/|/ ])>~iU', $html, $result);
        $openedtags = $result[1];
        preg_match_all('~</([a-z0-9]+)>~iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);

        if (count($closedtags) == $len_opened)
        {
            return $html;
        }

        $openedtags = array_reverse($openedtags);

        for ($i = 0; $i < $len_opened; $i++)
        {
            if ( ! in_array($openedtags[$i], $single_tags))
            {
                if (($key = array_search($openedtags[$i], $closedtags)) !== FALSE)
                {
                    unset($closedtags[$key]);
                }
                else
                {
                    $html .= '</'.$openedtags[$i].'>';
                }
            }
        }

        return $html;
    }

    private function replaceParagraphsByBr($html)
    {
        $html = preg_replace("/<p[^>]*?>/", "", $html);
        $html = trim(str_replace("</p>", "<br />", $html));
        if(Str::endsWith($html, "<br />"))
        {
            $html = substr($html, 0, strlen($html)-strlen("<br />"));
        }
        return $html;
    }

}