<?php

namespace Dvlpp\Sharp\Http;

use Dvlpp\Sharp\Config\SharpConfig;

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
     * @param $categoryName
     * @return mixed
     */
    public function show($categoryName)
    {
        // Find Category config (from sharp CMS config file)
        $category = SharpConfig::findCategory($categoryName);

        $entityName = $category->entities->current();

        // Find the first entity for which we are abilited
        while(!check_ability("list", $categoryName, $entityName)) {
            $category->entities->next();
            $entityName = $category->entities->valid() ? $category->entities->current() : null;
        }

        if(!$entityName) {
            abort(403);
        }

        return redirect()->route("cms.list", [$categoryName, $entityName]);
    }
}