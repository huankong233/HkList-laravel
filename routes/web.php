<?php

use Illuminate\Support\Facades\Route;

Route::view('/{any}', "App")
//     ->middleware("installCheck:haveInstall")
     ->where('any', '.*');
