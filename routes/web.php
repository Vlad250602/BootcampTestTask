<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/get-collections', [\App\Http\Controllers\CollectionController::class,'getCollections']);

Route::post('/add-collection', [\App\Http\Controllers\CollectionController::class,'addCollection']);
Route::post('/update-collection/{id}', [\App\Http\Controllers\CollectionController::class,'updateCollection']);

Route::delete('/delete-collection/{id}', [\App\Http\Controllers\CollectionController::class, 'deleteCollection']);


Route::post('/add-contributor', [\App\Http\Controllers\ContributorController::class,'addContributor']);
Route::post('/update-contributor/{id}', [\App\Http\Controllers\ContributorController::class,'updateContributor']);

Route::delete('/delete-contributor/{id}', [\App\Http\Controllers\ContributorController::class, 'deleteContributor']);
