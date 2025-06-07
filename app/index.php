<?php 

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ ."/routes.php";

use App\Core\Core as Core;
use App\Http\Route;

Core::dispatch(Route::routes());