<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('layouts.app'))->name('home');
