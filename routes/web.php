<?php

use Illuminate\Support\Facades\Route;

Route::get("/{any}", fn() => view("App"))
     ->middleware("NeedInstall")
     ->where("any", ".*");
