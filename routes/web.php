<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/api')->group(function () {
    include_once 'api.php';
});
