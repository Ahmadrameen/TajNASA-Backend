<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectMemberController;

// Public routes
Route::post('/auth/signup', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/countries', [CountryController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('photos');
    });

    // Protected Projects resource with 'index' method excluded
    Route::apiResource('/projects', ProjectController::class)->except('index');
    Route::get('/project-members/{project_id}/{member_id}', [ProjectMemberController::class, 'getMemberByProjectIdAndMemberId']);
    Route::get('/project-members/{project_id}', [ProjectMemberController::class, 'index']);
    Route::put('/updateUser', [AuthController::class, 'updateUser']);
    Route::apiResource('/project-members', ProjectMemberController::class)->except('index');
});
