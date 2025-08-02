<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to('/control-panel/login');
});
