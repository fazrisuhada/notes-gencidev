<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\NoteController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('notes', [NoteController::class, 'index']);
    Route::post('notes', [NoteController::class, 'store']);
    Route::get('notes/{uuid}', [NoteController::class, 'show']);
    Route::put('notes/{uuid}', [NoteController::class, 'update']);
    Route::delete('notes/{uuid}', [NoteController::class, 'delete']);
    Route::post('notes/{uuid}/restore', [NoteController::class, 'restore']);
    Route::delete('notes/{uuid}/destroy', [NoteController::class, 'destroy']);
});