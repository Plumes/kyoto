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

$app->get('/', function () use ($app) {
    if(isMobile()) {
        return redirect('mobile/goals.html');
    }
    return $app->version();
});

$app->get('/test', ['middleware'=>'auth','uses'=>'ExampleController@test']);

$app->post('/login', ['uses'=>'UserController@login']);
$app->post('/register', ['uses'=>'UserController@register']);

$app->group(['middleware'=>'auth'], function () use ($app) {
    $app->get('/profile', ['uses'=>'UserController@getUserProfile']);
    $app->get('/goals', ['uses'=>'MainController@getGoalList']);
    $app->post('/goal', ['uses'=>'MainController@addGoal']);
    $app->get('/goal/{id}', ['uses'=>'MainController@getGoalDetail']);
    $app->post('/goal/{id}/checkin', ['uses'=>'MainController@checkIn']);
    $app->get('/goal/{goal_id}/{year}/{month}', ['uses'=>'MainController@getCheckedInDays']);
    $app->get('/goal/{goal_id}/{year}/{month}/{day_of_m}', ['uses'=>'MainController@getDailyFootprints']);
});