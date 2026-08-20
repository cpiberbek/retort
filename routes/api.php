
<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SsoLoginController;

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

Route::post('plant-sync', [ApiController::class, 'syncPlant']);
Route::post('user-sync', [ApiController::class, 'syncUser']);
Route::post('user-desync', [ApiController::class, 'desyncUser']);
Route::post('activation', [ApiController::class, 'activation']);
Route::post('password-change', [ApiController::class, 'changePassword']);

Route::post('/sso/logout', [SsoLoginController::class, 'ssoLogout']);