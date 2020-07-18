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
        $router->post('/user-profile','userController@userProfile');
        $router->post('/user-fill-identity','userController@userFillIdentity');
        $router->put('/user-edit','userController@userEditIdentity');
    });

    $router->group(['middleware' => 'checkAdmin'], function () use ($router) {

        $router->group(['prefix' => 'dashboard'], function($router){
            $router->post('/','dashController@index');
            $router->post('/job-list','dashController@jobList');
            $router->post('/createJob','dashController@createJob');
            $router->post('/update-job','dashController@updateJob');
            $router->post('/update-job-status','dashController@updateJobStatus');

            $router->post('/job-status','dashController@jobStatus');
            $router->get('/show-job-submit','dashController@showJobSubmit');
            $router->post('/show-job-submited','dashController@showJobSubmited');
        });

    });

    $router->group(['prefix' => 'freelance'], function() use ($router){
        $router->get('/','freelanceController@index');
        $router->post('/show-job-list','freelanceController@showJobList');
        $router->post('/job-submit','freelanceController@jobSubmit');
    });

});

