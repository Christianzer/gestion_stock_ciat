<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiModControllers extends Controller
{
    //
    public function delete_commande($code_commande){
        $bon_commande = DB::table('bon_commande')
            ->where('code_commande','=',$code_commande)->first();
        DB::table('bon_commande')
            ->where('code_commande','=',$code_commande)->delete();
        DB::table('commandes')->where('code_commande','=',$code_commande)->delete();
        if (!is_null($bon_commande->code_facture)){
            DB::table('factures')->where('code_facture','=',$bon_commande->code_facture)->delete();
            DB::table('ventes')->where('code_facture','=',$bon_commande->code_facture)->delete();
            $versement = DB::table('versement')->where('code_facture','=',$bon_commande->code_facture)->first();
            if (!is_null($versement->code_facture)){
                DB::table('versement')->where('code_versement','=',$versement->code_versement)->delete();
                DB::table('information_paiement')->where('code_versement','=',$versement->code_versement)->delete();
                DB::table('monnaie')->where('code_versement','=',$versement->code_versement)->delete();
            }
        }
        return response()->json(null, 201);
    }
}
