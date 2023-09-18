<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::get("/",function (){

// //   $data = \App\Model\Internal\Admin::all();
//   return response()->json([
//     "message"=>"test"
//   ],400);
// });

Route::post('/user', [\App\Http\Controllers\Internal\User\UserController::class, 'store']);


