<?php


Route::get('/admin', function () {
    return redirect()->route("cms");
});

Route::group(['middleware' => 'sharp_auth'], function () {

    // CMS Home
    Route::get('/admin/cms', [
        "as" => "cms",
        "uses" => '\Dvlpp\Sharp\Http\CategoryController@index'
    ]);

    // Language management
    Route::get('/admin/cms/lang/{lang}', [
        "as" => "cms.lang",
        "uses" => '\Dvlpp\Sharp\Http\LocalizationController@change'
    ]);

    // Entity
    Route::get('/admin/cms/{category}', [
        "as" => "cms.category",
        "uses" => '\Dvlpp\Sharp\Http\CategoryController@show',
    ]);
    Route::get('/admin/cms/{category}/{entity}', [
        "as" => "cms.list",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@index',
    ]);
    Route::get('/admin/cms/{category}/{entity}/create', [
        "as" => "cms.create",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@create',
    ]);
    Route::get('/admin/cms/{category}/{entity}/{id}/edit', [
        "as" => "cms.edit",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@edit',
    ]);
    Route::get('/admin/cms/{category}/{entity}/{id}/duplicate', [
        "as" => "cms.duplicate",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@duplicate',
    ]);
    Route::put('/admin/cms/{category}/{entity}/{id}', [
        "as" => "cms.update",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@update',
    ]);
    Route::post('/admin/cms/{category}/{entity}', [
        "as" => "cms.store",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@store',
    ]);
    Route::delete('/admin/cms/{category}/{entity}/{id}', [
        "as" => "cms.destroy",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@destroy',
    ]);

    Route::post('/admin/cms/{category}/{entity}/command/{action}/{id}', [
        "as" => "cms.entityCommand",
        "uses" => '\Dvlpp\Sharp\Http\CommandController@entityCommand'
    ]);
    Route::post('/admin/cms/{category}/{entity}/command/{action}', [
        "as" => "cms.listCommand",
        "uses" => '\Dvlpp\Sharp\Http\CommandController@entitiesListCommand'
    ]);

    Route::post('/admin/cms/{category}/{entity}/{id}/activate', [
        "as" => "cms.activate",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@activate',
    ]);
    Route::post('/admin/cms/{category}/{entity}/{id}/deactivate', [
        "as" => "cms.deactivate",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@deactivate',
    ]);

    Route::post('/admin/cms/{category}/{entity}/reorder', [
        "as" => "cms.reorder",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@reorder',
    ]);

    Route::post('/admin/cms/{category}/{entity}/{field}/customSearchField', [
        "as" => "cms.customSearchField",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@ax_customSearchField',
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
