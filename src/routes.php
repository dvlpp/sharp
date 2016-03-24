<?php


Route::get('/admin', function () {
    return redirect()->route("sharp.cms");
});

Route::group(['middleware' => ['web', 'sharp_auth']], function () {

    // CMS Home
    Route::get('/admin/cms', [
        "as" => "sharp.cms",
        "uses" => '\Dvlpp\Sharp\Http\CategoryController@index'
    ]);

    // Language management
    Route::get('/admin/cms/lang/{lang}', [
        "as" => "sharp.cms.lang",
        "uses" => '\Dvlpp\Sharp\Http\LocalizationController@change'
    ]);

    // Entity
    Route::get('/admin/cms/{category}', [
        "as" => "sharp.cms.category",
        "uses" => '\Dvlpp\Sharp\Http\CategoryController@show',
    ]);
    Route::get('/admin/cms/{category}/{entity}', [
        "as" => "sharp.cms.list",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@index',
    ]);
    Route::get('/admin/cms/{category}/{entity}/create', [
        "as" => "sharp.cms.create",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@create',
    ]);
    Route::get('/admin/cms/{category}/{entity}/{id}/edit', [
        "as" => "sharp.cms.edit",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@edit',
    ]);
    Route::get('/admin/cms/{category}/{entity}/{id}/duplicate', [
        "as" => "sharp.cms.duplicate",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@duplicate',
    ]);
    Route::put('/admin/cms/{category}/{entity}/{id}', [
        "as" => "sharp.cms.update",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@update',
    ]);
    Route::post('/admin/cms/{category}/{entity}', [
        "as" => "sharp.cms.store",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@store',
    ]);
    Route::delete('/admin/cms/{category}/{entity}/{id}', [
        "as" => "sharp.cms.destroy",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@destroy',
    ]);

    Route::post('/admin/cms/{category}/{entity}/command/{action}/{id}', [
        "as" => "sharp.cms.entityCommand",
        "uses" => '\Dvlpp\Sharp\Http\CommandController@entityCommand'
    ]);
    Route::post('/admin/cms/{category}/{entity}/command/{action}', [
        "as" => "sharp.cms.listCommand",
        "uses" => '\Dvlpp\Sharp\Http\CommandController@entitiesListCommand'
    ]);

    Route::post('/admin/cms/{category}/{entity}/changeState', [
        "as" => "sharp.cms.changeState",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@changeState',
    ]);

    Route::post('/admin/cms/{category}/{entity}/reorder', [
        "as" => "sharp.cms.reorder",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@reorder',
    ]);

    Route::post('/admin/cms/{category}/{entity}/{field}/customSearchField', [
        "as" => "sharp.cms.customSearchField",
        "uses" => '\Dvlpp\Sharp\Http\EntityController@ax_customSearchField',
    ]);

    Route::post('/admin/upload', [
        "as" => "sharp.upload",
        "uses" => '\Dvlpp\Sharp\Http\UploadController@upload'
    ]);
    Route::get('/admin/download/{file?}', [
        "as" => "sharp.download",
        "uses" => '\Dvlpp\Sharp\Http\UploadController@download'
    ])->where('file', '(.*)');

    Route::get('/admin/logout', [
        "as" => "sharp.logout",
        "uses" => '\Dvlpp\Sharp\Http\AuthController@logout'
    ]);
});


Route::group(['middleware' => ['web', 'sharp_guest']], function () {

    Route::get('/admin/login', '\Dvlpp\Sharp\Http\AuthController@index');

    Route::post('/admin/login', [
        "as" => "sharp.login",
        "uses" => '\Dvlpp\Sharp\Http\AuthController@login'
    ]);
});
