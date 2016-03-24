<?php

namespace Dvlpp\Sharp\Http;

class CategoryController extends Controller
{

    /**
     * Sharp home page.
     *
     * @return mixed
     */
    public function index()
    {
        return view('sharp::cms.index');
    }

    /**
     * Redirects to list of first entity of the selected category.
     *
     * @param $categoryKey
     * @return mixed
     */
    public function show($categoryKey)
    {
        // Find Category class
        $category = sharp_category($categoryKey);

        $k=0;
        $entityKey = $category->entities()[$k];

        // Find the first entity for which we are abilited
        while(!check_ability("list", $categoryKey, $entityKey)) {
            $entityKey = sizeof($category->entities()) > $k
                ? $category->entities()[++$k]
                : null;
        }

        if(!$entityKey) {
            abort(403);
        }

        return redirect()->route("sharp.cms.list", [$categoryKey, $entityKey]);
    }
}