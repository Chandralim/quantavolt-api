<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/internal/user', [\App\Http\Controllers\Internal\User\UserController::class, 'store']);

Route::get('/', function () {
    return "ok";
});

Route::get('files/{args?}', function ($args) {
    // $path = __DIR__.'/../../public/files/' . $args;
    $path = __DIR__ . '/../public/files/' . $args;
    if (file_exists($path)) {
        $p = explode("/", $path);
        $filename = array_pop($p);
        // return Response::file($path);
        return Response::make(file_get_contents($path), 200, [
            'Content-Type' => mct($filename),
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
})->where('args', '(.*)');

// Route::get('/{any}', 'App\Http\Controllers\SinglePageController@index')->where('any', '^(?!data|verify|rules).*$');
// Route::get('/verify/email/{email_token}', 'App\Http\Controllers\Verify@check');
// Route::get('/data/verify/email/{email_token}', 'App\Http\Controllers\Verify@check2');
// Route::get('/rules/privacy-policy',function(){
//   return view('policy_privacy');
// });

// Route::get('/rules/terms-conditions',function(){
//   return view('terms_conditions');
// });
