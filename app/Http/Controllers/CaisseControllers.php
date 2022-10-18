<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaisseControllers extends Controller
{
    //


    public function recherche(Request $request){
        $date_debut = $request->date_debut;
        $date_fin = $request->date_fin;

        $date = [$date_debut,$date_fin];

        $information = DB::table('factures')
            ->join('versement','versement.code_facture','=','factures.code_facture')
            ->join('clients','clients.id','=','factures.matricule_clients_factures')
            ->join("information_paiement","information_paiement.code_versement",'=',"versement.code_versement")
            ->whereBetween('versement.date_versement',$date)
            ->get();

        $total = DB::table('factures')
            ->join('versement','versement.code_facture','=','factures.code_facture')
            ->join('clients','clients.id','=','factures.matricule_clients_factures')
            ->join("information_paiement","information_paiement.code_versement",'=',"versement.code_versement")
            ->whereBetween('versement.date_versement',$date)
            ->sum('versement.montant_verser');

        $info = array(
          "information"=>$information,
          "total"=>$total
        );

        return response()->json($info,201);

    }

    public function information($code){
        $information = DB::table('factures')
            ->join('versement','versement.code_facture','=','factures.code_facture')
            ->join('clients','clients.id','=','factures.matricule_clients_factures')

            ->join("information_paiement","information_paiement.code_versement",'=',"versement.code_versement")
            ->where('versement.code_versement','=',$code)
            ->first();


        $listes = DB::table('factures')
            ->join('versement','versement.code_facture','=','factures.code_facture')
            ->where('versement.code_facture','=',$information->code_facture)
            ->groupBy('factures.code_facture')
            ->sum('versement.montant_verser');




        $paiement = json_decode($information->paiement);

        $info = array(
            "code_versement"=>$information->code_versement,
            "date_versement"=>$information->date_versement,
            "clients"=>$information->nom." ".$information->prenoms,
            "telephone"=>$information->telephone,
            "factures"=>$information->code_facture,
            "contact"=>$information->contact,
            "montant_verser"=>$information->montant_verser,
            "montant_facture"=>$information->montant_total_factures_ttc,
            "reste_payer"=>(double)((double)$information->montant_total_factures_ttc -(double)$listes),
            "paiement"=>$paiement,
            "type_paiement"=>$information->type_paiement,
            "a_payer"=>(double)$information->montant_verser+((double)$information->montant_total_factures_ttc -(double)$listes),
        );

        return response()->json($info,201);
    }

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

    public function faire_versement(Request $request){

        $paiement = $request->paiement;

        $data = array(
            "montant_verser"=>$request->montant_verser,
            "code_facture"=>$request->code_facture,
            "date_versement"=>$request->date_versement,
            "type_paiement"=>$request->type_paiment,
        );

        $id_insert = DB::table("versement")->insertGetId($data);

        $code = "OBF-ET".date('Y')."N".$id_insert;


        DB::table("versement")->where('id_versement','=',$id_insert)->update(array("code_versement"=>$code));


        $dataPaiement = array(
          "code_versement"=>$code,
          "paiement"=>json_encode(array(
              "type_paiement"=>$paiement["type_paiement"],
              "montant"=>$paiement["montant"],
              "banque"=>$paiement["banque"],
              "numero_cheque"=>$paiement["numero_cheque"],
              "numero_telephone"=>$paiement["numero_telephone"],
              "reseau"=>$paiement["reseau"]
          ))
        );

        DB::table("information_paiement")->insert($dataPaiement);


        return response()->json($code,201);


    }


}
