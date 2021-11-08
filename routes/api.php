<?php

use App\Http\Controllers\ApiClientsControllers;
use App\Http\Controllers\ApiProduitsControllers;
use App\Http\Controllers\ApiVentesControllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::get('clients', [ApiClientsControllers::class,'getAllClients']);
Route::post('clients', [ApiClientsControllers::class,'createClient']);
Route::put('clients/{id}', [ApiClientsControllers::class,'updateClient']);
Route::delete('clients/{id}', [ApiClientsControllers::class,'deleteClient']);

Route::get('produits',[ApiProduitsControllers::class,'getProduits']);
Route::post('produits', [ApiProduitsControllers::class,'createProduits']);
Route::put('produits/{id}', [ApiProduitsControllers::class,'updateProduits']);
Route::delete('produits/{id}', [ApiProduitsControllers::class,'deleteProduits']);

Route::post('ventes', [ApiVentesControllers::class,'ventes']);
Route::get('factures/{code_facture}', [ApiVentesControllers::class,'read_facture']);
Route::get('imprimer_factures/{code_facture}', [ApiVentesControllers::class,'imprimer_facture']);

Route::get('dash',[ApiClientsControllers::class,'getData']);
