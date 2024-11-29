<?php

use App\Http\Controllers\AlumniController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(AlumniController::class)->group(function () {
    Route::post('/register-alumni', 'registerAlumni');
    Route::get('/get-alumni', 'getAlumni');
    Route::delete('/delete-alumni/{id}', 'deleteAlumni');
    Route::post('/update-alumni/{id}', 'updateAlumni');
});


Route::controller(PostController::class)->group(function () {
    Route::post('/create-post', 'createPost');
    Route::get('/get-post', 'getPost');
    Route::delete('/delete-post/{id}', 'deletePost');
    Route::patch('/update-post', 'updatePost');
});


Route::controller(BlogController::class)->group(function () {
    Route::post('/create-blog', 'createBlog');
    Route::get('/get-blog', 'getBlog');
    Route::delete('/delete-blog/{id}', 'deleteBlog');
    Route::patch('/update-blog', 'updateBlog');
});


Route::controller(UserController::class)->group(function () {
    Route::post('/create-user', 'createUser');
    Route::get('/get-user', 'getUser');
    Route::delete('/delete-user/{id}', 'deleteUser');
    Route::post('/update-user/{id}', 'updateUser');
});


Route::controller(ImageController::class)->group(function () {
    Route::post('/create-image', 'createImage');
    Route::get('/get-image', 'getImages');
    Route::delete('/delete-image/{id}', 'deleteImage');
    Route::patch('/update-image/{id}', 'updateImage');
});
