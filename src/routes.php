<?php

use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\Config\SharpSiteConfig;

Route::get('/admin', function () {
    return redirect()->route("cms");
});

Route::group(['middleware' => 'sharp_auth'], function () {

    // CMS Home
    Route::get('/admin/cms', [
        "as" => "cms",
        "uses" => '\Dvlpp\Sharp\Http\CmsController@index'
    ]);

    // Language management
    Route::get('/admin/cms/lang/{lang}', [
        "as" => "cms.lang",
        "uses" => '\Dvlpp\Sharp\Http\CmsController@lang'
    ]);

    // Entity
    Route::get('/admin/cms/{category}', [
        "as" => "cms.category",
        "uses" => '\Dvlpp\Sharp\Http\CmsController@category',
    ]);
    Route::get('/admin/cms/{category}/{entity}', [
        "as" => "cms.list",
        "uses" => '\Dvlpp\Sharp\Http\CmsController@listEntities',
    ]);
    Route::get('/admin/cms/{category}/{entity}/create', [
        "as" => "cms.create",
        "uses" => '\Dvlpp\Sharp\Http\CmsController@createEntity',
    ]);
    Route::get('/admin/cms/{category}/{entity}/{id}/edit', [
        "as" => "cms.edit",
        "uses" => '\Dvlpp\Sharp\Http\CmsController@editEntity',
    ]);
    Route::get('/admin/cms/{category}/{entity}/{id}/duplicate/{lang?}', [
        "as" => "cms.duplicate",
        "uses" => '\Dvlpp\Sharp\Http\CmsController@duplicateEntity',
    ]);
    Route::put('/admin/cms/{category}/{entity}/{id}', [
        "as" => "cms.update",
        "uses" => '\Dvlpp\Sharp\Http\CmsController@updateEntity',
    ]);
    Route::post('/admin/cms/{category}/{entity}', [
        "as" => "cms.store",
        "uses" => '\Dvlpp\Sharp\Http\CmsController@storeEntity',
    ]);
    Route::delete('/admin/cms/{category}/{entity}/{id}', [
        "as" => "cms.destroy",
        "uses" => '\Dvlpp\Sharp\Http\CmsController@destroyEntity',
    ]);

    Route::post('/admin/cms/{category}/{entity}/command/{action}/{id}', [
        "as" => "cms.entityCommand",
        "uses" => '\Dvlpp\Sharp\Http\CmsCommandsController@entityCommand'
    ]);
    Route::post('/admin/cms/{category}/{entity}/command/{action}', [
        "as" => "cms.listCommand",
        "uses" => '\Dvlpp\Sharp\Http\CmsCommandsController@entitiesListCommand'
    ]);

    Route::post('/admin/cms/{category}/{entity}/{id}/activate', [
        "as" => "cms.activate",
        "uses" => '\Dvlpp\Sharp\Http\CmsController@ax_activateEntity',
    ]);
    Route::post('/admin/cms/{category}/{entity}/{id}/deactivate', [
        "as" => "cms.deactivate",
        "uses" => '\Dvlpp\Sharp\Http\CmsController@ax_deactivateEntity',
    ]);

    Route::post('/admin/cms/{category}/{entity}/reorder', [
        "as" => "cms.reorder",
        "uses" => '\Dvlpp\Sharp\Http\CmsController@ax_reorderEntities',
    ]);

    Route::post('/admin/cms/{category}/{entity}/{field}/customSearchField', [
        "as" => "cms.customSearchField",
        "uses" => '\Dvlpp\Sharp\Http\CmsController@ax_customSearchField',
    ]);

    Route::post('/admin/upload', [
        "as" => "upload",
        "uses" => '\Dvlpp\Sharp\Http\UploadController@upload'
    ]);
    Route::post('/admin/uploadWithThumbnail', [
        "as" => "uploadWithThumbnail",
        "uses" => '\Dvlpp\Sharp\Http\UploadController@uploadWithThumbnail'
    ]);
    Route::get('/admin/download/{file?}', [
        "as" => "download",
        "uses" => '\Dvlpp\Sharp\Http\UploadController@download'
    ])->where('file', '(.*)');

    Route::get('/admin/logout', [
        "as" => "logout",
        "uses" => '\Dvlpp\Sharp\Http\AuthController@logout'
    ]);
});


Route::group(['middleware' => 'sharp_guest'], function () {

    Route::get('/admin/login', '\Dvlpp\Sharp\Http\AuthController@index');
    Route::post('/admin/login', [
        "as" => "login",
        "uses" => '\Dvlpp\Sharp\Http\AuthController@login'
    ]);
});


View::composer(['sharp::cms.cmslayout'], function ($view) {
    // Load categories
    $categories = SharpCmsConfig::listCategories();
    $view->with('cmsCategories', $categories);

    // Get current language
    $language = session("sharp_lang");
    $languages = SharpSiteConfig::getLanguages();
    if ($languages) {
        if (!$language || !array_key_exists($language, $languages)) {
            $language = array_values($languages)[0];
        } else {
            $language = $languages[$language];
        }
    }
    $view->with('language', $language);

    // Get sharp version
    $view->with('sharpVersion', File::get(__DIR__ . "/../version.txt"));
});
