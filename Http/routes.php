<?php

Route::group([
    'middleware' => ['web', 'auth', 'roles'],
    'roles' => ['admin'],
    'prefix' => \Helper::getSubdirectory(),
    'namespace' => 'Modules\CustomFields\Http\Controllers',
], function () {
    Route::get('/custom-fields', 'CustomFieldsController@index')->name('customfields.index');
    Route::get('/custom-fields/new', 'CustomFieldsController@create')->name('customfields.create');
    Route::post('/custom-fields/new', 'CustomFieldsController@store')->name('customfields.store');
    Route::get('/custom-fields/{id}', 'CustomFieldsController@edit')->name('customfields.edit');
    Route::post('/custom-fields/{id}', 'CustomFieldsController@updateField')->name('customfields.update');
    Route::post('/custom-fields/{id}/delete', 'CustomFieldsController@destroy')->name('customfields.destroy');
});

Route::group([
    'middleware' => ['web', 'auth'],
    'prefix' => \Helper::getSubdirectory(),
    'namespace' => 'Modules\CustomFields\Http\Controllers',
], function () {
    Route::post('/custom-fields/conversation/{conversation}', 'ConversationFieldsController@save')->name('customfields.conversation.save');
});
