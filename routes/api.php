<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('auth')->group(function () {

    Route::post('/sign-in', 'Api\Auth@signIn');
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::prefix('user-management')->group(function () {

        Route::prefix('user')->group(function () {

            Route::get('/index', 'Api\UserManagement\User@index');
            Route::get('/unique', 'Api\UserManagement\User@unique');
            Route::get('/get-access-rights', 'Api\UserManagement\User@getAccessRights');

            Route::post('/store', 'Api\UserManagement\User@store');

            Route::put('/save', 'Api\UserManagement\User@save');
            Route::put('/reset-password', 'Api\UserManagement\User@resetPassword');

            Route::delete('/destroy', 'Api\UserManagement\User@destroy');
        });
    });

    Route::prefix('task-management')->group(function () {

        Route::prefix('task')->group(function () {

            Route::get('/index', 'Api\TaskManagement\Task@index');
            Route::get('/unique', 'Api\TaskManagement\Task@unique');

            Route::post('/store', 'Api\TaskManagement\Task@store');

            Route::put('/save', 'Api\TaskManagement\Task@save');

            Route::delete('/destroy', 'Api\TaskManagement\Task@destroy');
        });
    });

    Route::prefix('role')->group(function () {

        Route::get('/index', 'Api\Role@index');
        Route::get('/unique', 'Api\Role@unique');
        Route::get('/get-role', 'Api\Role@getRole');
        Route::get('/get-permission', 'Api\Role@getPermission');

        Route::post('/store', 'Api\Role@store');

        Route::put('/save', 'Api\Role@save');

        Route::delete('/destroy', 'Api\Role@destroy');
    });

    Route::prefix('module-management')->group(function () {

        Route::prefix('module')->group(function () {

            Route::get('/index', 'Api\ModuleManagement\Module@index');
            Route::get('/unique', 'Api\ModuleManagement\Module@unique');

            Route::post('/store', 'Api\ModuleManagement\Module@store');

            Route::put('/save', 'Api\ModuleManagement\Module@save');

            Route::delete('/destroy', 'Api\ModuleManagement\Module@destroy');
        });

        Route::prefix('sub-module')->group(function () {

            Route::get('/index', 'Api\ModuleManagement\SubModule@index');
            Route::get('/unique', 'Api\ModuleManagement\SubModule@unique');

            Route::post('/store', 'Api\ModuleManagement\SubModule@store');

            Route::put('/save', 'Api\ModuleManagement\SubModule@save');

            Route::delete('/destroy', 'Api\ModuleManagement\SubModule@destroy');
        });
    });
});

Route::get('version', function () {
    return phpinfo();
});
