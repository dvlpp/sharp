<?php

use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\Config\SharpSiteConfig;
use Illuminate\Support\Str;

Route::get('/admin', function() {

    $authService = SharpSiteConfig::getAuthService();

    if($authService && !$authService->checkAdmin()) return redirect()->guest("admin/login");

    return redirect()->route("cms");
});

Route::group(['before' => 'sharp_auth'], function() {

    // Routes for embedded field
    Route::match(['PUT','POST','GET'], '/admin/cms/embedded/{category}/{entity}/{fieldKey}/{embeddedCategory}/{embeddedEntity}/create', [
        "as"=>"cms.embedded.create",
        "uses"=>'\Dvlpp\Sharp\Http\CmsEmbeddedEntityController@create',
        "before"=>"sharp_access_granted:entity create *embedded_entity"
    ]);
    Route::match(['PUT','POST','GET'], '/admin/cms/embedded/{category}/{entity}/{fieldKey}/{embeddedCategory}/{embeddedEntity}/edit/{id}', [
        "as"=>"cms.embedded.edit",
        "uses"=>'\Dvlpp\Sharp\Http\CmsEmbeddedEntityController@edit',
        "before"=>"sharp_access_granted:entity update *embedded_entity"
    ]);
    Route::put('/admin/cms/embedded/{category}/{entity}/{fieldKey}/{embeddedCategory}/{embeddedEntity}/{id}', [
        "as"=>"cms.embedded.update",
        "uses"=>'\Dvlpp\Sharp\Http\CmsEmbeddedEntityController@update',
        "before"=>"sharp_access_granted:entity update *embedded_entity"
    ]);
    Route::post('/admin/cms/embedded/{category}/{entity}/{fieldKey}/{embeddedCategory}/{embeddedEntity}', [
        "as"=>"cms.embedded.store",
        "uses"=>'\Dvlpp\Sharp\Http\CmsEmbeddedEntityController@store',
        "before"=>"sharp_access_granted:entity create *embedded_entity"
    ]);
    Route::post('/admin/cms/embedded/{category}/{entity}/cancel', [
        "as"=>"cms.embedded.cancel",
        "uses"=>'\Dvlpp\Sharp\Http\CmsEmbeddedEntityController@cancel',
        "before"=>"sharp_access_granted:entity update *entity"
    ]);

    // CMS Home
    Route::get('/admin/cms', [
        "as"=>"cms",
        "uses"=>'\Dvlpp\Sharp\Http\CmsController@index'
    ]);

    // Language management
    Route::get('/admin/cms/lang/{lang}', [
        "as"=>"cms.lang",
        "uses"=>'\Dvlpp\Sharp\Http\CmsController@lang'
    ]);

    // Entity
    Route::get('/admin/cms/{category}', [
        "as"=>"cms.category",
        "uses"=>'\Dvlpp\Sharp\Http\CmsController@category',
        "before"=>"sharp_access_granted:category view *category"
    ]);
    Route::get('/admin/cms/{category}/{entity}', [
        "as"=>"cms.list",
        "uses"=>'\Dvlpp\Sharp\Http\CmsController@listEntities',
        "before"=>"sharp_access_granted:entity list *entity"
    ]);
    Route::get('/admin/cms/{category}/{entity}/create', [
        "as"=>"cms.create",
        "uses"=>'\Dvlpp\Sharp\Http\CmsController@createEntity',
        "before"=>"sharp_access_granted:entity create *entity"
    ]);
    Route::get('/admin/cms/{category}/{entity}/{id}/edit', [
        "as"=>"cms.edit",
        "uses"=>'\Dvlpp\Sharp\Http\CmsController@editEntity',
        "before"=>"sharp_access_granted:entity update *entity"
    ]);
    Route::get('/admin/cms/{category}/{entity}/{id}/duplicate/{lang?}', [
        "as"=>"cms.duplicate", "uses"=>'\Dvlpp\Sharp\Http\CmsController@duplicateEntity',
        "before"=>"sharp_access_granted:entity create *entity"
    ]);
    Route::put('/admin/cms/{category}/{entity}/{id}', [
        "as"=>"cms.update",
        "uses"=>'\Dvlpp\Sharp\Http\CmsController@updateEntity',
        "before"=>"sharp_access_granted:entity update *entity"
    ]);
    Route::post('/admin/cms/{category}/{entity}', [
        "as"=>"cms.store",
        "uses"=>'\Dvlpp\Sharp\Http\CmsController@storeEntity',
        "before"=>"sharp_access_granted:entity create *entity"]);
    Route::delete('/admin/cms/{category}/{entity}/{id}', [
        "as"=>"cms.destroy",
        "uses"=>'\Dvlpp\Sharp\Http\CmsController@destroyEntity',
        "before"=>"sharp_access_granted:entity delete *entity"
    ]);

    Route::match(['POST','GET'], '/admin/cms/{category}/{entity}/command/{action}/{id}', [
        "as"=>"cms.entityCommand",
        "uses"=>'\Dvlpp\Sharp\Http\CmsCommandsController@entityCommand'
    ]);
    Route::get('/admin/cms/{category}/{entity}/command/{action}', [
        "as"=>"cms.listCommand",
        "uses"=>'\Dvlpp\Sharp\Http\CmsCommandsController@entitiesListCommand'
    ]);

    Route::post('/admin/cms/{category}/{entity}/{id}/activate', [
        "as"=>"cms.activate",
        "uses"=>'\Dvlpp\Sharp\Http\CmsController@ax_activateEntity',
        "before"=>"sharp_access_granted:entity update *entity"
    ]);
    Route::post('/admin/cms/{category}/{entity}/{id}/deactivate', [
        "as"=>"cms.deactivate",
        "uses"=>'\Dvlpp\Sharp\Http\CmsController@ax_deactivateEntity',
        "before"=>"sharp_access_granted:entity update *entity"
    ]);

    Route::post('/admin/cms/{category}/{entity}/reorder', [
        "as"=>"cms.reorder", "uses"=>'\Dvlpp\Sharp\Http\CmsController@ax_reorderEntities',
        "before"=>"sharp_access_granted:entity update *entity"
    ]);

    Route::post('/admin/upload', [
        "as"=>"upload",
        "uses"=>'\Dvlpp\Sharp\Http\UploadController@upload'
    ]);
    Route::post('/admin/uploadWithThumbnail', [
        "as"=>"uploadWithThumbnail",
        "uses"=>'\Dvlpp\Sharp\Http\UploadController@uploadWithThumbnail'
    ]);
    Route::get('/admin/download/{file?}', [
        "as"=>"download",
        "uses"=>'\Dvlpp\Sharp\Http\UploadController@download'
    ])->where('file', '(.*)');

    Route::get('/admin/logout', [
        "as"=>"logout",
        "uses"=>'\Dvlpp\Sharp\Http\AuthController@logout'
    ]);
});


Route::group(['before' => 'sharp_guest'], function() {

    Route::get('/admin/login', '\Dvlpp\Sharp\Http\AuthController@index');
    Route::post('/admin/login', [
        "as"=>"login",
        "uses"=>'\Dvlpp\Sharp\Http\AuthController@login'
    ]);
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
    $authService = SharpSiteConfig::getAuthService();

    if($authService && !$authService->checkAdmin()) return redirect()->guest("admin/login");
});

Route::filter('sharp_guest', function()
{
    $authService = SharpSiteConfig::getAuthService();

    if( ! $authService || $authService->checkAdmin()) return redirect()->to("/admin/cms");
});

Route::filter('sharp_access_granted', function($route, $request, $value)
{
    list($type, $action, $key) = explode(" ", $value);

    if(starts_with($key, "*")) $key = $route->getParameter(substr($key, 1));

    if( ! sharp_granted($type, $action, $key)) return redirect()->to("/admin/cms");
});