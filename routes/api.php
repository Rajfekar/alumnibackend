<?php

use App\Http\Controllers\AlumniController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(AuthController::class)->group(function () {
    Route::post('/register-user', 'register');
    Route::post('/login', 'login');
    Route::post('/login-with-provider', 'loginWithProvider');
    Route::post('/login-with-mail', 'loginWithMail');
    Route::post('/check-token-expiration', 'checkTokenExpiration');
    Route::post('/logout', 'logout');
    Route::get('/get-users', 'getUsers');
    Route::get('/get-user/{user}', 'getUser');
    Route::get('/find-user/{email}', 'findUser');
});

Route::controller(StudentAuthController::class)->group(function () {
    Route::post('/login-student', 'loginStudent');
});

Route::controller(AlumniController::class)->group(function () {
    Route::post('/register-alumni', 'registerAlumni');
    Route::get('/get-alumni', 'getAlumni');
    Route::get('/get-alumnis', 'getAlumnis');
    Route::delete('/delete-alumni/{id}', 'deleteAlumni');
    Route::post('/update-alumni/{id}', 'updateAlumni');
});


Route::controller(PostController::class)->group(function () {
    Route::post('/create-post', 'createPost');
    Route::get('/get-post', 'getPost');
    Route::delete('/delete-post/{id}', 'deletePost');
    Route::post('/update-post/{id}', 'updatePost');
});


Route::controller(BlogController::class)->group(function () {
    Route::post('/create-blog', 'createBlog');
    Route::get('/get-blog', 'getBlog');
    Route::get('/get-blog/{id}', 'getBlogById');
    Route::delete('/delete-blog/{id}', 'deleteBlog');
    Route::post('/update-blog/{id}', 'updateBlog');
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


Route::controller(ContactController::class)->group(function () {
    Route::post('create-contact', 'createContact');
    Route::get('get-contact', 'getContact');
    Route::delete('delete-contact/{id}', 'deleteContact');
});
