<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// UI to insert, show, update and delete data (calls the API in routes/api.php)
Route::get('/library', function () {
    return view('library');
});
