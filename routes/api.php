<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookAuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// index, store, show, update, destroy
Route::apiResource('book-types', BookTypeController::class);
Route::apiResource('authors', AuthorController::class);
Route::apiResource('books', BookController::class);

// tblBookAuthor has a composite key (BookID + AuthorID)
Route::get('book-authors', [BookAuthorController::class, 'index']);
Route::post('book-authors', [BookAuthorController::class, 'store']);
Route::get('book-authors/{bookId}/{authorId}', [BookAuthorController::class, 'show']);
Route::put('book-authors/{bookId}/{authorId}', [BookAuthorController::class, 'update']);
Route::delete('book-authors/{bookId}/{authorId}', [BookAuthorController::class, 'destroy']);
