<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Notification;
// use App\User;

// use App\Events\SolarCellDataReceived;
// Route::get('/test',function(){
//     broadcast(new SolarCellDataReceived(["data"]));
//     return "test";
// });

// Route::post('/visitor/login', 'App\Http\Controllers\Data\Visitor\Accounts@login');
// Route::post('/visitor/autoLogin', 'App\Http\Controllers\Data\Visitor\Accounts@autoLogin');
// Route::post('/visitor/logout', 'App\Http\Controllers\Data\Visitor\Accounts@logout');
// Route::get('/visitor/getProfileInfo', 'App\Http\Controllers\Data\Visitor\Accounts@getProfileInfo');
// Route::post('/visitor/updateProfile', 'App\Http\Controllers\Data\Visitor\Accounts@updateProfile');
// Route::post('/visitor/updatePassword', 'App\Http\Controllers\Data\Visitor\Accounts@updatePassword');
// Route::post('/visitor/checkLogin', 'App\Http\Controllers\Data\Visitor\Accounts@checkLogin');

// Route::get('/visitor/dashboard/getVisitorStat', 'App\Http\Controllers\Data\Visitor\Dashboard@getVisitorStat');

// Route::post('/visitor/quotation/getVisitorData', 'App\Http\Controllers\Data\Visitor\Quotations@getVisitorData');
// Route::post('/visitor/quotation/getCategoryData', 'App\Http\Controllers\Data\Visitor\Quotations@getCategoryData');
// Route::post('/visitor/quotation/getCompanyData', 'App\Http\Controllers\Data\Visitor\Quotations@getCompanyData');
// Route::get('/visitor/quotation/getAdsQuotations', 'App\Http\Controllers\Data\Visitor\Quotations@getAdsQuotations');
// Route::post('/visitor/quotation/sendQuote', 'App\Http\Controllers\Data\Visitor\Quotations@sendQuote');
// Route::post('/visitor/quotation/sendQuote', 'App\Http\Controllers\Data\Visitor\Quotations@sendQuote');
// Route::get('/visitor/quotations', 'App\Http\Controllers\Data\Visitor\Quotations@index');

// Route::post('/visitor/directory/getCategories', 'App\Http\Controllers\Data\Visitor\Directories@getCategories');
// Route::post('/visitor/directory/getCompaniesByCategory', 'App\Http\Controllers\Data\Visitor\Directories@getCompaniesByCategory');
// Route::post('/visitor/directory/getPremiumStands', 'App\Http\Controllers\Data\Visitor\Directories@getPremiumStands');
// Route::post('/visitor/directory/getCompanies', 'App\Http\Controllers\Data\Visitor\Directories@getCompanies');

// Route::post('/visitor/company/getCompanyInfo', 'App\Http\Controllers\Data\Visitor\Directories@getCompanyInfo');

// Route::get('/home/vendors', 'App\Http\Controllers\Data\Homes@vendors');

// Route::get('/getCountries', 'App\Http\Controllers\Data\Common@getCountries');
// Route::get('/getProvinces', 'App\Http\Controllers\Data\Common@getProvinces');
// Route::get('/getVersion', 'App\Http\Controllers\Data\Common@getVersion');

// Route::post('/visitor/register', 'App\Http\Controllers\Data\Visitor\Accounts@register');

// Route::post('/visitor/getForgetPasswordLink', 'App\Http\Controllers\Data\Visitor\Accounts@getForgetPasswordLink');
// Route::post('/visitor/getForgetPasswordLink2', 'App\Http\Controllers\Data\Visitor\Accounts@getForgetPasswordLink2');
// Route::post('/visitor/checkForgetPasswordLink', 'App\Http\Controllers\Data\Visitor\Accounts@checkForgetPasswordLink');
// Route::post('/visitor/setNewPassword', 'App\Http\Controllers\Data\Visitor\Accounts@setNewPassword');

// Route::get('/visitor/getUser', 'App\Http\Controllers\Data\Visitor\Accounts@getUser');

// // Route::get('/internal/users', 'App\Http\Controllers\Internal\OfficeUser@index');
// // Route::post('/internal/user', 'App\Http\Controllers\Internal\OfficeUser@store');
// // Route::post('/internal/user/login', 'App\Http\Controllers\Internal\OfficeUser@login');
// // Route::get('/internal/user/getUser', 'App\Http\Controllers\Internal\OfficeUser@getUser');
// // Route::post('/internal/user/change_password', 'App\Http\Controllers\Internal\OfficeUser@change_password');
// //
// // Route::get('/internal/quotations', 'App\Http\Controllers\Internal\VisitorQuotation@index');
// // Route::put('/internal/quotation/updateDoneWA', 'App\Http\Controllers\Internal\VisitorQuotation@updateDoneWA');
// // Route::put('/internal/quotation/updateStatus', 'App\Http\Controllers\Internal\VisitorQuotation@updateStatus');
Route::get('/home/product/brochures', 'App\Http\Controllers\Main\ProductController@brochures');
Route::get('/home/product/portfolios', 'App\Http\Controllers\Main\ProductController@portfolios');
Route::get('/home/product/articles', 'App\Http\Controllers\Main\ProductController@articles');


// Route::get('/events', 'App\Http\Controllers\Main\ProductController@articles');

Route::get('/events', 'App\Http\Controllers\Main\EventController@index');
Route::get('/event', 'App\Http\Controllers\Main\EventController@show');
Route::get('/event_files', 'App\Http\Controllers\Main\EventFileController@index');



Route::get('/hub/info', 'App\Http\Controllers\Main\HubertController@index');
Route::post('/hub/info', 'App\Http\Controllers\Main\HubertController@store');
Route::put('/hub/info', 'App\Http\Controllers\Main\HubertController@update');
Route::delete('/hub/info', 'App\Http\Controllers\Main\HubertController@delete');

Route::get('/test/post', 'App\Http\Controllers\TestController@post');
