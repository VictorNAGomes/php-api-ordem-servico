<?php 

use App\Http\Route;

// Auth routes
Route::post('/auth/login', 'AuthController@login');

// Client routes (protegidas por autenticação e role)
Route::get('/clients', 'ClientController@getAll', ['middleware' => ['admin']]);
Route::get('/clients/{id}', 'ClientController@getOne', ['middleware' => ['admin']]);
Route::post('/clients', 'ClientController@create', ['middleware' => ['admin']]);
Route::put('/clients/{id}', 'ClientController@update', ['middleware' => ['admin']]);
Route::delete('/clients/{id}', 'ClientController@delete', ['middleware' => ['admin']]);

// Products routes (protegidas por autenticação e role)
Route::get('/products', 'ProductController@getAll', ['middleware' => ['admin']]);
Route::get('/products/{id}', 'ProductController@getOne', ['middleware' => ['admin']]);
Route::post('/products', 'ProductController@create', ['middleware' => ['admin']]);
Route::put('/products/{id}', 'ProductController@update', ['middleware' => ['admin']]);
Route::delete('/products/{id}', 'ProductController@delete', ['middleware' => ['admin']]);