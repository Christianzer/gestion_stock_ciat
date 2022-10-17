<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaisseControllers extends Controller
{
    //
    public function listes(){
        $listes = DB::table('factures')
            ->join('versement','versement.code_facture','=','factures.code_facture')
            ->join('clients','clients.id','=','factures.matricule_clients_factures')

            ->select(DB::raw('SUM(versement.montant_verser) as verser'),'factures.*','clients.*',DB::raw('(factures.montant_total_factures_ttc - SUM(versement.montant_verser)) as restant'))
            ->havingRaw('factures.montant_total_factures_ttc > COALESCE(verser, 0 )')
            ->groupBy('factures.code_facture')
            ->get();

        return response()->json($listes,201);
    }
}
