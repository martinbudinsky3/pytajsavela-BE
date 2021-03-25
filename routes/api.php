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

Route::post('/login', [AuthController::class, 'login']); // DONE

Route::group(['middleware' => ['auth:sanctum']], function () {
    
    Route::group(['prefix' => 'questions'], function () {
        Route::get('/', [QuestionController::class, 'index']); // DONE
        Route::post('/', [QuestionController::class, 'store']); // DONE
        Route::get('{id}/', [QuestionController::class, 'show']); // DONE
        Route::get('{id}/edit-form/', [QuestionController::class, 'edit']); // DONE
        Route::put('{id}/', [QuestionController::class, 'update']); // DONE
        Route::delete('{id}/', [QuestionController::class, 'destroy']); // DONE
        Route::post('{id}/answers/', [AnswerController::class, 'store']); // DONE
    });

    Route::group(['prefix' => 'answers'], function () {
        Route::get('{id}/edit-form/', [AnswerController::class, 'edit']); // DONE
        Route::put('{id}/', [AnswerController::class, 'update']);
        Route::delete('{id}/', [AnswerController::class, 'destroy']);
    });

    Route::group(['prefix' => 'tags'], function () {
        Route::get('/', [TagController::class, 'index']); // DONE
        Route::get('{id}/questions', [TagController::class, 'indexQuestions']); //DONE
    });

    Route::group(['prefix' => 'users/{id}'], function () {
        Route::get('/', [UserController::class, 'show']); // DONE
        Route::get('/questions', [UserController::class, 'indexQuestions']); // DONE
        Route::get('/answers', [UserController::class, 'indexAnswers']); // DONE
    });

    Route::get('/images/{id}', [ImageController::class, 'show']); // DONE

    Route::post('/logout', [AuthController::class, 'logout']); // DONE
});
