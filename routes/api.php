<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\ClientTemplateController;
use App\Http\Controllers\UserTemplateController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\PasswordController;

//password reset

Route::post('/forgot-password', [PasswordController::class, 'sendResetCode']);
Route::post('/reset-password', [PasswordController::class, 'resetPassword']);

// contact us
Route::post('/contact-us', [ContactUsController::class, 'submitContactUs']);

//admin routes

Route::prefix('admin')->group(function () {
    Route::post('/register', [AdminController::class, 'register']);
    Route::post('/login', [AdminController::class, 'login']);
//    Route::post('/reset-password', [AdminController::class, 'resetPassword']);
   Route::get('/statistics', [AdminController::class, 'getStatistics']);

    Route::put('client/{id}/verify', [ClientController::class, 'verify']);
    Route::delete('clients/{id}', [ClientController::class, 'destroy']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AdminController::class, 'logout']);
        Route::get('/', [AdminController::class, 'index']);
        Route::post('/reset-password', [AdminController::class, 'resetPassword']);
        Route::get('/show', [AdminController::class, 'showByToken']); //get by token
        Route::get('/{id}', [AdminController::class, 'show']);
        Route::put('/{id}', [AdminController::class, 'update']);
        Route::delete('/{id}', [AdminController::class, 'destroy']);
    });
});

//client routes

Route::middleware('auth:sanctum')->get('/client/show', [ClientController::class, 'showByToken']);
Route::middleware('auth:sanctum')->get('/client/statistics', [ClientController::class, 'clientStatistics']);
Route::middleware('auth:sanctum')->get('/client/users', [ClientController::class, 'getAllUsers']);

Route::prefix('client')->group(function () {
    Route::post('/register', [ClientController::class, 'register']);
    Route::post('/login', [ClientController::class, 'login']);
    Route::get('/', [ClientController::class, 'index']);
    Route::get('/{id}', [ClientController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [ClientController::class, 'logout']);
        Route::get('/statistics', [ClientController::class, 'clientStatistics']);

        Route::post('/reset-password', [ClientController::class, 'resetPassword']);
     //   Route::get('/show', [ClientController::class, 'showByToken']);
        Route::put('/{id}', [ClientController::class, 'update']);
        Route::delete('/{id}', [ClientController::class, 'destroy']);
    });
});


//user routes
Route::middleware("auth:sanctum")->get('/user/assessment-counts', [UserController::class, 'assessmentCountsByUser']);
Route::middleware("auth:sanctum")->get('/user/completed-assessment-counts', [UserController::class, 'completedAssessmentCountsByUser']);
Route::middleware("auth:sanctum")->get('/user/rejected-assessment-counts', [UserController::class, 'RejectedAssessmentCountsByUser']);
Route::middleware("auth:sanctum")->post('/user/reset-password', [UserController::class, 'resetPassword']);

Route::prefix('user')->group(function () {
    Route::put('/{id}/verify', [UserController::class, 'verify']);
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/{id}', [UserController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);
       // Route::get('/assessment-counts', [UserController::class, 'assessmentCountsByUser']);
        //Route::get('/completed-assessment-counts', [UserController::class, 'completedAssessmentCountsByUser']);
        Route::get('/', [UserController::class, 'index']);
        Route::get('/show', [UserController::class, 'showByToken']); 
        Route::put('/', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });
});

//templates

Route::prefix('templates')->group(function () {
    Route::get('/', action:[TemplateController::class, 'index']);
    Route::post('/', [TemplateController::class, 'store']); 
    Route::get('/{template}', [TemplateController::class, 'show']); 
    Route::put('/{template}', [TemplateController::class, 'update']); 
    Route::delete('/{template}', [TemplateController::class, 'destroy']);
    //Route::middleware('auth:sanctum')->group(function () {
    //});
});

//assign client templates

Route::prefix('client-templates')->group(function () {
    Route::get('/', action:[ClientTemplateController	::class, 'index']);
    Route::post('/', [ClientTemplateController::class, 'store']); 
    Route::get('/{template}', [ClientTemplateController::class, 'show']);
    Route::put('/{template}', [ClientTemplateController::class, 'update']); 
    Route::delete('/{template}', [ClientTemplateController::class, 'destroy']);
    Route::get('/client/{clientId}', [ClientTemplateController::class, 'getTemplatesByClient']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('by-token/client', [ClientTemplateController::class, 'getTemplatesByAuthenticatedClient']);
    });
});

//assign user templates

Route::prefix('user-templates')->group(function () {
    Route::get('/', [UserTemplateController::class, 'index']);  
    Route::post('/', [UserTemplateController::class, 'store']); 
    Route::get('/{id}', [UserTemplateController::class, 'show']);
    Route::put('/{id}', [UserTemplateController::class, 'update']); 
    Route::delete('/{id}', [UserTemplateController::class, 'destroy']); 
    Route::get('/user/{userId}', [UserTemplateController::class, 'getTemplatesByUser']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('by-token/user', [UserTemplateController::class, 'getTemplatesByAuthenticatedUser']);
    });
});

//assessments
Route::middleware('auth:sanctum')->get('assessments/client', [AssessmentController::class, 'getAssessmentsForAuthenticatedClient']);

Route::prefix('assessments')->group(function () {
    Route::get('/', action:[AssessmentController	::class, 'index']);
    Route::post('/', [AssessmentController::class, 'store']); 
    Route::get('/completed-by-user', [AssessmentController::class, 'getCompletedAssessmentsByUser']);
    Route::get('/{id}', [AssessmentController::class, 'show']); 
    Route::put('/{id}', [AssessmentController::class, 'update']); 
    Route::delete('/{id}', [AssessmentController::class, 'destroy']);
    Route::get('/client/{clientId}', [AssessmentController::class, 'getAssessmentsByClientId']);
    Route::get('/user/{userId}', [AssessmentController::class, 'getAssessmentsByUserId']);

   Route::middleware('auth:sanctum')->group(function () {
    //Route::get('/client', [AssessmentController::class, 'getAssessmentsForAuthenticatedClient']);
    Route::get('/user', [AssessmentController::class, 'getAssessmentsForAuthenticatedUser']);
    });
});