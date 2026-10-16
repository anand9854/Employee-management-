<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::view('/employees-page', 'employees.index');
Route::view('/', 'auth')->name('auth.page');
Route::view('/employees', 'employees')->name('employees.page');