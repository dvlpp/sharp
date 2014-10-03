<?php

use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\Config\SharpSiteConfig;
use Illuminate\Support\Str;

Route::get('/admin', function() {

    $authService = \Dvlpp\Sharp\Config\SharpSiteConfig::getAuthService();
    if($authService && !$authService->checkAdmin())
    {
        return Redirect::guest("admin/login");
    }

    return Redirect::route("cms");

});

Route::group(['before' => 'sharp_auth'], function() {

    // Routes for embedded field
    Route::match(['PUT','POST','GET'], '/admin/cms/embedded/{category}/{entity}/{fieldKey}/{embeddedCategory}/{embeddedEntity}/create', ["as"=>"cms.embedded.create", "uses"=>"CmsEmbeddedEntityController@create", "before"=>"sharp_access_granted:entity create *embedded_entity"]);
    Route::match(['PUT','POST','GET'], '/admin/cms/embedded/{category}/{entity}/{fieldKey}/{embeddedCategory}/{embeddedEntity}/edit/{id}', ["as"=>"cms.embedded.edit", "uses"=>"CmsEmbeddedEntityController@edit", "before"=>"sharp_access_granted:entity update *embedded_entity"]);
    Route::put('/admin/cms/embedded/{category}/{entity}/{fieldKey}/{embeddedCategory}/{embeddedEntity}/{id}', ["as"=>"cms.embedded.update", "uses"=>"CmsEmbeddedEntityController@update", "before"=>"sharp_access_granted:entity update *embedded_entity"]);
    Route::post('/admin/cms/embedded/{category}/{entity}/{fieldKey}/{embeddedCategory}/{embeddedEntity}', ["as"=>"cms.embedded.store", "uses"=>"CmsEmbeddedEntityController@store", "before"=>"sharp_access_granted:entity create *embedded_entity"]);
    Route::post('/admin/cms/embedded/{category}/{entity}/cancel', ["as"=>"cms.embedded.cancel", "uses"=>"CmsEmbeddedEntityController@cancel", "before"=>"sharp_access_granted:entity update *entity"]);

    // CMS Home
    Route::get('/admin/cms', ["as"=>"cms", "uses"=>"CmsController@index"]);

    // Language management
    Route::get('/admin/cms/lang/{lang}', ["as"=>"cms.lang", "uses"=>"CmsController@lang"]);

    // Entity
    Route::get('/admin/cms/{category}', ["as"=>"cms.category", "uses"=>"CmsController@category", "before"=>"sharp_access_granted:category view *category"]);
    Route::get('/admin/cms/{category}/{entity}', ["as"=>"cms.list", "uses"=>"CmsController@listEntities", "before"=>"sharp_access_granted:entity list *entity"]);
    Route::get('/admin/cms/{category}/{entity}/{id}/edit', ["as"=>"cms.edit", "uses"=>"CmsController@editEntity", "before"=>"sharp_access_granted:entity update *entity"])->where('id', '[0-9]+');
    Route::get('/admin/cms/{category}/{entity}/{id}/duplicate/{lang?}', ["as"=>"cms.duplicate", "uses"=>"CmsController@duplicateEntity", "before"=>"sharp_access_granted:entity create *entity"])->where('id', '[0-9]+');
    Route::get('/admin/cms/{category}/{entity}/create', ["as"=>"cms.create", "uses"=>"CmsController@createEntity", "before"=>"sharp_access_granted:entity create *entity"]);
    Route::put('/admin/cms/{category}/{entity}/{id}', ["as"=>"cms.update", "uses"=>"CmsController@updateEntity", "before"=>"sharp_access_granted:entity update *entity"])->where('id', '[0-9]+');
    Route::post('/admin/cms/{category}/{entity}', ["as"=>"cms.store", "uses"=>"CmsController@storeEntity", "before"=>"sharp_access_granted:entity create *entity"]);
    Route::delete('/admin/cms/{category}/{entity}/{id}', ["as"=>"cms.destroy", "uses"=>"CmsController@destroyEntity", "before"=>"sharp_access_granted:entity delete *entity"]);

    Route::get('/admin/cms/{category}/{entity}/command/{action}/{id}', ["as"=>"cms.entityCommand", "uses"=>"CmsCommandsController@entityCommand", "before"=>"sharp_access_granted:entity update *entity"]);
    Route::get('/admin/cms/{category}/{entity}/command/{action}', ["as"=>"cms.listCommand", "uses"=>"CmsCommandsController@entitiesListCommand", "before"=>"sharp_access_granted:entity update *entity"]);

    Route::post('/admin/cms/{category}/{entity}/{id}/activate', ["as"=>"cms.activate", "uses"=>"CmsController@ax_activateEntity", "before"=>"sharp_access_granted:entity update *entity"])->where('id', '[0-9]+');
    Route::post('/admin/cms/{category}/{entity}/{id}/deactivate', ["as"=>"cms.deactivate", "uses"=>"CmsController@ax_deactivateEntity", "before"=>"sharp_access_granted:entity update *entity"])->where('id', '[0-9]+');

    Route::post('/admin/cms/{category}/{entity}/reorder', ["as"=>"cms.reorder", "uses"=>"CmsController@ax_reorderEntities", "before"=>"sharp_access_granted:entity update *entity"])->where('id', '[0-9]+');

    Route::post('/admin/upload', ["as"=>"upload", "uses"=>"UploadController@upload"]);
    Route::post('/admin/uploadWithThumbnail', ["as"=>"uploadWithThumbnail", "uses"=>"UploadController@uploadWithThumbnail"]);

    Route::get('/admin/logout', ["as"=>"logout", "uses"=>"AuthController@logout"]);
});


Route::group(['before' => 'sharp_guest'], function() {

    Route::get('/admin/login', "AuthController@index");
    Route::post('/admin/login', ["as"=>"login", "uses"=>"AuthController@login"]);
});


View::composer(['sharp::cms.cmslayout'], function($view)
{
    // Load categories
    $categories = SharpCmsConfig::listCategories();
    $view->with('cmsCategories', $categories);

    // Get current language
    $language = Session::get("sharp_lang");
    $languages = SharpSiteConfig::getLanguages();
    if($languages)
    {
        if (!$language || !array_key_exists($language, $languages))
        {
            $language = array_values($languages)[0];
        }
        else
        {
            $language = $languages[$language];
        }
    }
    $view->with('language', $language);

    // Get sharp version
    $view->with('sharpVersion', File::get(__DIR__ . "/../version.txt"));
});


Route::filter('sharp_auth', function()
{
    $authService = \Dvlpp\Sharp\Config\SharpSiteConfig::getAuthService();
    if($authService && !$authService->checkAdmin())
    {
        return Redirect::guest("admin/login");
    }
});

Route::filter('sharp_guest', function()
{
    $authService = \Dvlpp\Sharp\Config\SharpSiteConfig::getAuthService();
    if(!$authService || $authService->checkAdmin())
    {
        return Redirect::to("/admin/cms");
    }
});

Route::filter('sharp_access_granted', function($route, $request, $value)
{
    list($type, $action, $key) = explode(" ", $value);
    if(Str::startsWith($key, "*"))
    {
        $key = $route->getParameter(substr($key, 1));
    }
    if(! \Dvlpp\Sharp\Auth\SharpAccessManager::granted($type, $action, $key))
    {
        return Redirect::to("/admin/cms");
    }
});