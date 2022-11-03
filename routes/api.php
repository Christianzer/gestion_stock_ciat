<?php

use App\Http\Controllers\ApiClientsControllers;
use App\Http\Controllers\ApiModControllers;
use App\Http\Controllers\ApiProduitsControllers;
use App\Http\Controllers\ApiVentesControllers;
use App\Http\Controllers\CaisseControllers;
use App\Http\Controllers\MobileApiControllers;
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
Route::post('produits_update', [ApiProduitsControllers::class,'updateProduits']);
Route::delete('produits/{code_prod}', [ApiProduitsControllers::class,'deleteProduits']);

Route::post('ventes', [ApiVentesControllers::class,'ventes']);
Route::get('factures/{code_facture}', [ApiVentesControllers::class,'read_facture']);
Route::get('imprimer_factures/{code_facture}', [ApiVentesControllers::class,'imprimer_facture']);

Route::post('commandes', [ApiVentesControllers::class,'commandes']);
Route::get('commandes/{code_commande}', [ApiVentesControllers::class,'read_commande']);
Route::get('imprimer_commandes/{code_commande}', [ApiVentesControllers::class,'imprimer_commande']);

Route::get('listes_commandes', [ApiVentesControllers::class,'listes_commandes']);
Route::get('listes_commandes_effectuer', [ApiVentesControllers::class,'listes_commandes_effectuer']);
Route::get('remplir_facture/{code_commande}', [ApiVentesControllers::class,'remplir_facture']);

Route::get('remplir_bon_livraison/{code_commande}', [ApiVentesControllers::class,'read_bon_livraison']);
Route::put('livraison/{code_commande}', [ApiVentesControllers::class,'update_livraison']);
Route::get('imprimer_livraison/{code_commande}', [ApiVentesControllers::class,'imprimer_livraison']);

Route::get('dash',[ApiClientsControllers::class,'getData']);
Route::get('rapport/{date_demande}', [ApiProduitsControllers::class,'getrapport']);
Route::get('new_rapport/{date_demande}', [ApiProduitsControllers::class,'getnewrapport']);
Route::get('imprimer_rapport/{date_demande}', [ApiProduitsControllers::class,'imprimer_rapport']);

Route::get('listes_commandes_data/{matricule}',[ApiClientsControllers::class,'getClientsCommande']);
Route::get('listes_factures_data/{matricule}',[ApiClientsControllers::class,'getClientsFactures']);

Route::delete('supprimer/{code_commande}',[ApiModControllers::class,'delete_commande']);

Route::get('mobile/clients',[MobileApiControllers::class,'MobileListesClients']);
Route::get('mobile/produits',[MobileApiControllers::class,'getProduitsMobiles']);
Route::post('mobile/produits/achat',[MobileApiControllers::class,'FacturesMobiles']);


Route::get('imprimer_rapport_compta', [ApiProduitsControllers::class,'imprimer_rapport_perso']);

Route::post("facture_directe",[ApiVentesControllers::class,'facture_directe']);


Route::get("factures_listes",[CaisseControllers::class,"listes"]);
Route::post('faire_paiement',[CaisseControllers::class,'faire_versement']);

Route::get('information/{code}',[CaisseControllers::class,'information']);
Route::get('imprimer_recu/{code}',[CaisseControllers::class,'imprimer_recu']);


Route::get("ancien",[CaisseControllers::class,'inforRecu']);

Route::get('listes_sortie',[CaisseControllers::class,'sortie_listes']);
Route::get("code_sortie",[CaisseControllers::class,'generercodeentre_sortie']);
Route::post('facture_sortie',[CaisseControllers::class,'sortirCaisse']);
Route::post('upload_sortie', [CaisseControllers::class,'upload_sortie']);
Route::get('dowload_sortie/{id}',[CaisseControllers::class,'dowload_sortie']);
Route::get('listes_justif_sortie/{code}',[CaisseControllers::class,'listes_justif_sortie']);
Route::post('recherche',[CaisseControllers::class,'recherche']);
Route::get('imprimer/point/{date1}/{date2}/{type}/{detail_rapport}/{facture}/{client}/{type_paiement}',[CaisseControllers::class,'imprimerPoint']);



Route::get('detail_rapport',[CaisseControllers::class,'detail_rapport']);
Route::get('dowload/{id}',[CaisseControllers::class,'dowload']);
Route::get('listes_entre',[CaisseControllers::class,'entre_listes']);
Route::get('listes_justif/{code}',[CaisseControllers::class,'listes_justif']);
Route::get("code",[CaisseControllers::class,'generercodeentre']);
Route::post('upload', [CaisseControllers::class,'upload']);
Route::post('entre/facture',[CaisseControllers::class,'entrer_caisse']);
