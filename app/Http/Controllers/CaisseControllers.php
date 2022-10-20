<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaisseControllers extends Controller
{
    //

    public function listes_justif_sortie($code){
        $data = DB::table('justif_sortie')->where('code_sortie','=',$code)->get();
        return response()->json($data, 201);
    }

    public function dowload_sortie($id){
        $upload_path = public_path('upload');
        $element = DB::table('justif_sortie')->where('id_justif','=',$id)->first();
        $ele = $upload_path.'/'.$element->justif;
        return response()->download($ele,$element->justif);
    }

    public function upload_sortie(Request $request){
        $uploadedFiles=$request->pics;
        foreach ($uploadedFiles as $file){
            $upload_path = public_path('upload');
            $name=$file->getClientOriginalName();
            $file->move($upload_path, $name);
            DB::table('justif_sortie')
                ->insert(array(
                    'code_sortie'=>$request->code
                ,'justif'=>$name));
        }
        return response('success',200);

    }
    public function sortirCaisse(Request $request){


        $code = $request->input('code');
        $libelle = $request->input('libelle');
        $date_entre = $request->input('date_entre');
        $observation = $request->input('observation');
        $montant =(float)$request->input('montant');

        $data = array(

            'code_sortie'=>$code,
            'libelle_sortie_caisse'=>$libelle,
            'montant_sortie_caisse'=>$montant,
            'date_sortie_caisse'=>$date_entre,
            'observation'=>$observation,

        );

        $clients = DB::table('sortie_caisse')->insert($data);

        if ($clients){
            return response()->json($clients, 201);
        }

        else{
            return response()->json( null,400);

        }

    }

    public function codeincrementer($valeur){
        $element = substr($valeur,-6);
        return ++$element;
    }

    public function generercodeentre_sortie(){
        $dernier = DB::table('sortie_caisse')
            ->orderByDesc('id_sortie_sortie')->first();
        if (isset($dernier)){
            $data = $this->codeincrementer($dernier->code_sortie);
            $code = "OBF-ST".date('Y').$data;
        }else{
            $code = "OBF-ST".date('Y')."N00001";
        }

        $code = $this->codege_sortie($code);

        $total_entre = DB::table('versement')->sum('montant_verser');
        $total_sortie = DB::table('sortie_caisse')->sum('montant_sortie_caisse');
        $total = $total_entre - $total_sortie;
        $data = array("code"=>$code,"total"=>$total);
        return response()->json($data, 201);


    }

    public function codege_sortie($code){
        $test = DB::table('sortie_caisse')->where('code_sortie','=',$code)
            ->first();
        if ($test){
            $data = $this->codeincrementer($test->code_sortie);
            $code = "OBF-ST".date('Y').$data;
        }
        return $code;

    }

    public function sortie_listes(){
        $listes_paiement = DB::table('sortie_caisse')
            ->leftJoin("justif_sortie",'sortie_caisse.code_sortie','=','justif_sortie.code_sortie')
            ->select('sortie_caisse.*'
                ,DB::raw("DATE_FORMAT(sortie_caisse.date_sortie_caisse, '%d/%m/%Y') as date_sortie")
                ,DB::raw("(GROUP_CONCAT(justif_sortie.justif)) as `justif`")
            )
            ->orderByDesc('sortie_caisse.date_sortie_caisse')
            ->groupBy("sortie_caisse.code_sortie")
            ->get();
        $total = DB::table('sortie_caisse')->sum('montant_sortie_caisse');
        $total_entre = DB::table('versement')->sum('montant_verser');
        $data = array("listes"=>$listes_paiement,"total"=>$total,"encaisse"=>$total_entre);
        return response($data,200);
    }

    public function recherche(Request $request){
        $date_debut = $request->date_debut;
        $date_fin = $request->date_fin;
        $rapport = (int)$request->type_rapport;

        $date = [$date_debut,$date_fin];

        if ($rapport == 1):
            $information = DB::table('factures')
                ->join('versement','versement.code_facture','=','factures.code_facture')
                ->join('clients','clients.id','=','factures.matricule_clients_factures')
                ->join("information_paiement","information_paiement.code_versement",'=',"versement.code_versement")
                ->whereBetween('versement.date_versement',$date)
                ->orderByDesc('versement.date_versement')
                ->get();

            $total = DB::table('factures')
                ->join('versement','versement.code_facture','=','factures.code_facture')
                ->join('clients','clients.id','=','factures.matricule_clients_factures')
                ->join("information_paiement","information_paiement.code_versement",'=',"versement.code_versement")
                ->whereBetween('versement.date_versement',$date)
                ->sum('versement.montant_verser');
        else:
            $information = DB::table('sortie_caisse')
                ->whereBetween('sortie_caisse.date_sortie_caisse',$date)
                ->orderByDesc('sortie_caisse.date_sortie_caisse')
                ->get();

            $total = DB::table('sortie_caisse')
                ->whereBetween('sortie_caisse.date_sortie_caisse',$date)
                ->sum('sortie_caisse.montant_sortie_caisse');
        endif;


        $info = array(
            "information"=>$information,
            "total"=>$total
        );

        return response()->json($info,201);

    }
    public function imprimerPoint($date1,$date2,$type){

        $date_debut = $date1;
        $date_fin = $date2;
        $rapport = (int)$type;

        $date = [$date_debut,$date_fin];

        if ($rapport == 1):
            $information = DB::table('factures')
                ->join('versement','versement.code_facture','=','factures.code_facture')
                ->join('clients','clients.id','=','factures.matricule_clients_factures')
                ->join("information_paiement","information_paiement.code_versement",'=',"versement.code_versement")
                ->whereBetween('versement.date_versement',$date)
                ->orderByDesc('versement.date_versement')
                ->get();

            $total = DB::table('factures')
                ->join('versement','versement.code_facture','=','factures.code_facture')
                ->join('clients','clients.id','=','factures.matricule_clients_factures')
                ->join("information_paiement","information_paiement.code_versement",'=',"versement.code_versement")
                ->whereBetween('versement.date_versement',$date)
                ->sum('versement.montant_verser');
            if ($date1 == $date2){
                $title = "ENCAISSEMENT DU ".date("d-m-Y", strtotime($date1));
            }else{
                $title = "ENCAISSEMENT DU ".date("d-m-Y", strtotime($date1))." AU ".date("d-m-Y", strtotime($date2));
            }
        else:
            $information = DB::table('sortie_caisse')
                ->whereBetween('sortie_caisse.date_sortie_caisse',$date)
                ->orderByDesc('sortie_caisse.date_sortie_caisse')
                ->get();

            $total = DB::table('sortie_caisse')
                ->whereBetween('sortie_caisse.date_sortie_caisse',$date)
                ->sum('sortie_caisse.montant_sortie_caisse');
            if ($date1 == $date2){
                $title = "DECAISSEMENT DU ".date("d-m-Y", strtotime($date1));
            }else{
                $title = "DECAISSEMENT DU ".date("d-m-Y", strtotime($date1))." AU ".date("d-m-Y", strtotime($date2));
            }
        endif;




        return view("encaissement",compact('information','total','title','rapport'));

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
        /*
        $listes = DB::table('factures')
            ->join('versement','versement.code_facture','=','factures.code_facture')
            ->join('clients','clients.id','=','factures.matricule_clients_factures')
            ->select(DB::raw('SUM(versement.montant_verser) as verser'),'factures.*','clients.*',DB::raw('(factures.montant_total_factures_ttc - SUM(versement.montant_verser)) as restant'))
            ->havingRaw('factures.montant_total_factures_ttc > COALESCE(verser, 0 )')
            ->groupBy('factures.code_facture')
            ->get();
        */

        $listes = DB::table("factures")
            ->join('clients','clients.id','=','factures.matricule_clients_factures')
            ->leftJoin('versement','versement.code_facture','=','factures.code_facture')
            ->select(DB::raw('SUM(versement.montant_verser) as verser'),'factures.*','clients.*')
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

    public function dateToFrench($date, $format)
    {
        $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
        $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $french_months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date) ) ) );
    }

    public function imprimer_recu($code_recu){

        set_time_limit(300);

        $information = DB::table('factures')
            ->join('versement','versement.code_facture','=','factures.code_facture')
            ->join('clients','clients.id','=','factures.matricule_clients_factures')

            ->join("information_paiement","information_paiement.code_versement",'=',"versement.code_versement")
            ->where('versement.code_versement','=',$code_recu)
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


        $date_jour = $this->dateToFrench($information->date_versement,'l j F Y');

        return view("recu",compact(['info','date_jour']));

        //$pdf = PDF::loadView("commande",compact(['valeur','date_jour']))->setPaper('a4', 'portrait')->setWarnings(false);
        //return $pdf->stream();
        //return $pdf->output();

    }

}
