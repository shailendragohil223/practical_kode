<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\API\UserController;
use App\Http\Controllers\Admin\API\RoleController;
use App\Http\Controllers\Admin\API\Apiuserauthenticate;
use App\Http\Controllers\Admin\API\DashboardController;
use App\Http\Controllers\Admin\API\BlogController;


        Route::post('getUserLoggedIn', [Apiuserauthenticate::class, 'checkUSERISValid']);
        Route::post('forgetPassword', [Apiuserauthenticate::class, 'forgetPassword']);
        Route::post('getUserDetails', [Apiuserauthenticate::class, 'getUserDetails']);
        Route::post('getUserMenu', [Apiuserauthenticate::class, 'getUserMenu']);
        Route::post('resetPassword', [Apiuserauthenticate::class, 'resetPassword']);
        Route::post('LogoutUser', [Apiuserauthenticate::class, 'LogoutApiUser']);

        Route::post('users', [UserController::class, 'index']);
        Route::post('usersstore', [UserController::class, 'store']);
        Route::post('usersshow', [UserController::class, 'show']);
        Route::post('usersupdate', [UserController::class, 'update']);
        Route::post('userdestroy', [UserController::class, 'destroy']);

        Route::post('blog', [BlogController::class, 'index']);
        Route::post('blogstore', [BlogController::class, 'store']);
        Route::post('blogshow', [BlogController::class, 'show']);
        Route::post('blogupdate', [BlogController::class, 'update']);
        Route::post('destroy', [BlogController::class, 'destroy']);
        Route::post('blog_like_toggle', [BlogController::class, 'blog_like_toggle']);
