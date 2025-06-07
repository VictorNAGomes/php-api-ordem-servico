<?php 

use App\Http\Route;

// Auth routes
Route::post('/auth/login', 'AuthController@login');

// Client routes (protegidas por autenticação)
Route::get('/clients', 'ClientController@getAll', ['middleware' => ['auth']]);
Route::get('/clients/{id}', 'ClientController@getOne', ['middleware' => ['auth']]);
Route::post('/clients', 'ClientController@create', ['middleware' => ['auth']]);
Route::put('/clients/{id}', 'ClientController@update', ['middleware' => ['auth']]);
Route::delete('/clients/{id}', 'ClientController@delete', ['middleware' => ['auth']]);

// Products routes (protegidas por autenticação)
Route::get('/products', 'ProductController@getAll', ['middleware' => ['auth']]);
Route::get('/products/{id}', 'ProductController@getOne', ['middleware' => ['auth']]);
Route::post('/products', 'ProductController@create', ['middleware' => ['auth']]);
Route::put('/products/{id}', 'ProductController@update', ['middleware' => ['auth']]);
Route::delete('/products/{id}', 'ProductController@delete', ['middleware' => ['auth']]);
