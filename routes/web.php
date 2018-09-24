<?php

Route::group([
    'prefix' => 'admin/themes',
    'middleware' => ['web', 'admin.user'],
    'namespace' => '\Versatile\Themes\Http\Controllers',
], function () {

	Route::get('/', [
		'uses' => 'ThemesController@index',
		'as' => 'versatile.theme.index'
	]);

	Route::get('/activate/{theme}', [
		'uses' => 'ThemesController@activate',
		'as' => 'versatile.theme.activate'
	]);

    Route::get('/deactivate/{theme}', [
        'uses' => 'ThemesController@deactivate',
        'as' => 'versatile.theme.deactivate'
    ]);

	Route::delete('/delete', [
		'uses' => 'ThemesController@delete',
		'as' => 'versatile.theme.delete'
	]);

});