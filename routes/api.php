<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
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


//Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    
    Route::group(['prefix' => 'questions'], function () {
        Route::get('/', [QuestionController::class, 'index']);
        Route::post('/', [QuestionController::class, 'store']);
        Route::get('{id}/', [QuestionController::class, 'show']);
        Route::get('{id}/edit-form/', [QuestionController::class, 'edit']);
        Route::put('{id}/', [QuestionController::class, 'update']);
        Route::delete('{id}/', [QuestionController::class, 'destroy']);
    });

    Route::group(['prefix' => 'answers'], function () {
        Route::post('/', [AnswerController::class, 'store']);
        Route::get('{id}/edit-form/', [AnswerController::class, 'edit']);
        Route::put('{id}/', [AnswerController::class, 'update']);
        Route::delete('{id}/', [AnswerController::class, 'destroy']);
    });

    Route::group(['prefix' => 'tags'], function () {
        Route::get('/', [TagController::class, 'index']);
        Route::get('{id}/questions', [TagController::class, 'indexQuestions']);
    });

    Route::group(['prefix' => 'users/{id}'], function () {
        Route::get('/', [UserController::class, 'show']);
        Route::get('/questions', [UserController::class, 'indexQuestions']);
        Route::get('/answers', [UserController::class, 'indexAnswers']);
    });

    Route::get('/images/{id}', [ImageController::class, 'show']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
