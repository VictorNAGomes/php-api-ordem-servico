<?php 

use App\Http\Route;

// Client routes
Route::get('/clients', 'ClientController@getAll');
// Route::get('/clients/{id}', 'ClientController@show');
// Route::post('/clients', 'ClientController@store');
// Route::put('/clients/{id}', 'ClientController@update');
// Route::delete('/clients/{id}', 'ClientController@delete');

// Products routes
Route::get('/products', 'ProductController@getAll');
Route::get('/products/{id}', 'ProductController@getOne');
Route::post('/products', 'ProductController@create');
Route::put('/products/{id}', 'ProductController@update');
Route::delete('/products/{id}', 'ProductController@delete');
