<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });

// $router->get('/key',function(){
//     return str_random(32);
// });

$router->get("/",'homeController@index');
$router->post("/register", "authController@register");
$router->post("/login", "authController@login");

$router->group(['middleware' => 'auth'], function () use ($router) {

    $router->group(['prefix' => 'user'], function($router){
        $router->get('/','userController@index');
        $router->get('/profile','userController@profile');
    });

    $router->group(['prefix' => 'dashboard'], function($router){
        $router->get('/','dashController@index');
        $router->get('/job-list','dashController@jobList');
        $router->get('/job-list-detail','dashController@jobListDetail');
    });
});

