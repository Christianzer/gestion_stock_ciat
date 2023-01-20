<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaisseControllers extends Controller
{
    //

    public function entrer_caisse(Request $request){

        $type = (int)$request->input('type');

        if ($type == 1){

            $code = $request->input('code');
            $libelle = $request->input('libelle');
            $date_entre = $request->input('date_entre');
            $observation = $request->input('observation');
            $montant =(float)$request->input('montant');

            $data = array(

                'code_entre'=>$code,
                'libelle_entre_caisse'=>$libelle,
                'montant_entre_caisse'=>$montant,
                'date_entre_caisse'=>$date_entre,
                'observation'=>$observation,

            );

            $clients = DB::table('entre_caisse')->insert($data);

            if ($clients){
                return response()->json($clients, 201);
            }

            else{
                return response()->json( null,400);

            }

        }elseif ($type == 2){

            $code = $request->input('code');
            $libelle = $request->input('libelle');
            $date_entre = $request->input('date_entre');
            $observation = $request->input('observation');
            $montant =(float)$request->input('montant');

            $data = array(

                'code_entre'=>$code,
                'libelle_entre_caisse'=>$libelle,
                'montant_entre_caisse'=>$montant,
                'date_entre_caisse'=>$date_entre,
                'observation'=>$observation,

            );

            $clients = DB::table('entre_caisse')
                ->where("code_entre","=",$code)
                ->update($data)
            ;

            return response()->json($clients, 201);

        }

    }

    public function upload(Request $request){
        $uploadedFiles=$request->pics;
        $update=(int)$request->update;
        foreach ($uploadedFiles as $file){
            $upload_path = public_path('upload');
            $name=$file->getClientOriginalName();
            $file->move($upload_path, $name);

            if ($update == 2){
                DB::table('justif_entre')
                    ->where("code_entre",'=',$request->code)
                    ->delete();
                DB::table('justif_entre')
                    ->insert(array(
                        'code_entre'=>$request->code
                    ,'justif'=>$name));
            }else{
                DB::table('justif_entre')
                    ->insert(array(
                        'code_entre'=>$request->code
                    ,'justif'=>$name));
            }



        }
        return response('success',200);

    }

    public function codege($code){
        $test = DB::table('entre_caisse')->where('code_entre','=',$code)
            ->first();
        if ($test){
            $data = $this->codeincrementer($test->code_entre);
            $code = "CIAT-ET".date('Y').$data;
        }
        return $code;

    }

    public function generercodeentre(){
        $dernier = DB::table('entre_caisse')
            ->orderByDesc('id_entre_caisse')->first();
        if (isset($dernier)){
            $data = $this->codeincrementer($dernier->code_entre);
            $code = "OBF-APP".date('Y').$data;
        }else{
            $code = "OBF-APP".date('Y')."N00001";
        }

        $code = $this->codege($code);

        return response()->json($code, 201);


    }

    public function entre_listes(){
        $listes_paiement = DB::table('entre_caisse')
            ->leftJoin("justif_entre",'entre_caisse.code_entre','=','justif_entre.code_entre')
            ->select('entre_caisse.*'
                ,DB::raw("DATE_FORMAT(entre_caisse.date_entre_caisse, '%d/%m/%Y') as date_entre")
                ,DB::raw("(GROUP_CONCAT(justif_entre.justif)) as `justif`")
            )
            ->orderByDesc('entre_caisse.code_entre')
            ->groupBy("entre_caisse.code_entre")
            ->get();
        $total = DB::table('entre_caisse')->sum('montant_entre_caisse');
        $data = array("listes"=>$listes_paiement,"total"=>$total);
        return response($data,200);
    }

    public function listes_justif($code){
        $data = DB::table('justif_entre')->where('code_entre','=',$code)->get();
        return response()->json($data, 201);
    }

    public function dowload($id){
        $upload_path = public_path('upload');
        $element = DB::table('justif_entre')->where('id_justif','=',$id)->first();
        $ele = $upload_path.'/'.$element->justif;
        return response()->download($ele,$element->justif);
    }

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
        $update=(int)$request->update;
        foreach ($uploadedFiles as $file){
            $upload_path = public_path('upload');
            $name=$file->getClientOriginalName();
            $file->move($upload_path, $name);
            if ($update == 2){
                DB::table('justif_sortie')
                    ->where("code_sortie",'=',$request->code)
                    ->delete();
                DB::table('justif_sortie')
                    ->insert(array(
                        'code_sortie'=>$request->code
                    ,'justif'=>$name));
            }else{
                DB::table('justif_sortie')
                    ->insert(array(
                        'code_sortie'=>$request->code
                    ,'justif'=>$name));
            }
        }
        return response('success',200);

    }
    public function sortirCaisse(Request $request){



        $type = (int)$request->input('type');

        if ($type == 1){
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

        }elseif ($type == 2){
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

            $clients = DB::table('sortie_caisse')
                ->where("code_sortie",'=',$code)
                ->update($data);

            return response()->json($clients, 201);

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
        $total_appro = DB::table('entre_caisse')->sum('montant_entre_caisse');
        $total_sortie = DB::table('sortie_caisse')->sum('montant_sortie_caisse');
        $total = ($total_entre + $total_appro) - $total_sortie;
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
        $total_appro = DB::table('entre_caisse')->sum('montant_entre_caisse');
        $data = array("listes"=>$listes_paiement,"total"=>$total,"encaisse"=>$total_entre,"appro"=>$total_appro);
        return response($data,200);
    }

    public function detail_rapport(){
        $array_clients = [];
        $clients = DB::table("clients")
            ->select('*')
            ->get();
        foreach ($clients as $client){
            $nom = $client->nom." ".$client->prenoms;
            $id = $client->id;
            array_push($array_clients,array("nom"=>$nom,"id"=>$id));
        }
        $factures = DB::table("versement")->select('*')->groupBy("code_facture")->get();
        return response()->json(array("clients"=>$array_clients,"factures"=>$factures),201);
    }
    public function recherche(Request $request){
        $date_debut = $request->date_debut;
        $date_fin = $request->date_fin;
        $rapport = (int)$request->type_rapport;
        $detail_rapport = (int)$request->detail_rapport;
        $facture = $request->facture;
        $client = $request->client;
        $type_paiement = (int)$request->type_paiement;


        $date = [$date_debut,$date_fin];

        if ($rapport == 1):
            $information_debut = DB::table('factures')
                ->join('versement','versement.code_facture','=','factures.code_facture')
                ->join('clients','clients.id','=','factures.matricule_clients_factures')
                ->join("information_paiement","information_paiement.code_versement",'=',"versement.code_versement");

            $total_debut = DB::table('factures')
                ->join('versement','versement.code_facture','=','factures.code_facture')
                ->join('clients','clients.id','=','factures.matricule_clients_factures')
                ->join("information_paiement","information_paiement.code_versement",'=',"versement.code_versement");

            if ($detail_rapport == 2):
                $information_debut->whereIn("versement.code_facture",$facture);
                $total_debut->whereIn("versement.code_facture",$facture);
            endif;
            if ($detail_rapport == 3):
                $information_debut->whereIn("clients.id",$client);
                $total_debut->whereIn("clients.id",$client);
            endif;

            if ($type_paiement == 2){
                $information_debut->where("versement.type_paiement",'=',1);
                $total_debut->where("versement.type_paiement",'=',1);
            }

            if ($type_paiement == 3){
                $information_debut->where("versement.type_paiement",'=',2);
                $total_debut->where("versement.type_paiement",'=',2);
            }

            if ($type_paiement == 4){
                $information_debut->where("versement.type_paiement",'=',3);
                $total_debut->where("versement.type_paiement",'=',3);
            }



            $information = $information_debut->whereBetween('versement.date_versement',$date)
                ->orderByDesc('versement.date_versement')
                ->get();

            $total = $total_debut->whereBetween('versement.date_versement',$date)
                ->sum('versement.montant_verser');
        elseif ($rapport == 3):

            $information = DB::table('entre_caisse')
                ->whereBetween('entre_caisse.date_entre_caisse',$date)
                ->orderByDesc('entre_caisse.date_entre_caisse')
                ->get();

            $total = DB::table('entre_caisse')
                ->whereBetween('entre_caisse.date_entre_caisse',$date)
                ->sum('entre_caisse.montant_entre_caisse');

        elseif ($rapport == 2):
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
    public function imprimerPoint($date1,$date2,$type,$detail_rapport,$facture,$client,$type_paiement){

        $date_debut = $date1;
        $date_fin = $date2;
        $rapport = (int)$type;
        $detail_rapport = (int)$detail_rapport;
        $type_paiement = (int)$type_paiement;

        $titre_paiement = "";

        $factureIn = explode(',', $facture);
        $clientIn = explode(',', $client);



        $date = [$date_debut,$date_fin];

        if ($rapport == 1):
            $add_title = " ";
            $information_debut = DB::table('factures')
                ->join('versement','versement.code_facture','=','factures.code_facture')
                ->join('clients','clients.id','=','factures.matricule_clients_factures')
                ->join("information_paiement","information_paiement.code_versement",'=',"versement.code_versement");

            $total_debut = DB::table('factures')
                ->join('versement','versement.code_facture','=','factures.code_facture')
                ->join('clients','clients.id','=','factures.matricule_clients_factures')
                ->join("information_paiement","information_paiement.code_versement",'=',"versement.code_versement");

            if ($detail_rapport == 2):
                $information_debut->whereIn("versement.code_facture",$factureIn);
                $total_debut->whereIn("versement.code_facture",$factureIn);
            endif;
            if ($detail_rapport == 3):
                $information_debut->whereIn("clients.id",$clientIn);
                $total_debut->whereIn("clients.id",$clientIn);
            endif;

            if ($type_paiement == 2){
                $titre_paiement = " MODE DE PAIEMENT : ESPECES ";
                $information_debut->where("versement.type_paiement",'=',1);
                $total_debut->where("versement.type_paiement",'=',1);
            }

            if ($type_paiement == 3){
                $titre_paiement = " MODE DE PAIEMENT : CHEQUES ";
                $information_debut->where("versement.type_paiement",'=',2);
                $total_debut->where("versement.type_paiement",'=',2);
            }

            if ($type_paiement == 4){
                $titre_paiement = " MODE DE PAIEMENT : MOBILE MONNEY ";
                $information_debut->where("versement.type_paiement",'=',3);
                $total_debut->where("versement.type_paiement",'=',3);
            }

            $information = $information_debut->whereBetween('versement.date_versement',$date)
                ->orderByDesc('versement.date_versement')
                ->get();

            $total = $total_debut->whereBetween('versement.date_versement',$date)
                ->sum('versement.montant_verser');

            if ($date1 == $date2){
                $title = "ENCAISSEMENT DU ".date("d-m-Y", strtotime($date1));
            }else{
                $title = "ENCAISSEMENT DU ".date("d-m-Y", strtotime($date1))." AU ".date("d-m-Y", strtotime($date2));
            }
        elseif ($rapport == 3):

            $information = DB::table('entre_caisse')
                ->whereBetween('entre_caisse.date_entre_caisse',$date)
                ->orderByDesc('entre_caisse.date_entre_caisse')
                ->get();

            $total = DB::table('entre_caisse')
                ->whereBetween('entre_caisse.date_entre_caisse',$date)
                ->sum('entre_caisse.montant_entre_caisse');


            if ($date1 == $date2){
                $title = "APPROVISIONNEMENT DU ".date("d-m-Y", strtotime($date1));
            }else{
                $title = "APPROVISIONNEMENT DU ".date("d-m-Y", strtotime($date1))." AU ".date("d-m-Y", strtotime($date2));
            }



        elseif ($rapport == 2):
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

            $title = $title.$titre_paiement;

        return view("encaissement",compact('information','total','title','rapport'));

    }

    public function information($code){

        $information = DB::table('factures')
            ->join('versement','versement.code_facture','=','factures.code_facture')
            ->join('clients','clients.id','=','factures.matricule_clients_factures')

            ->join("information_paiement","information_paiement.code_versement",'=',"versement.code_versement")
            ->where('versement.code_versement','=',$code)
            ->first();

        $monnaie = DB::table("monnaie")
            ->where('code_versement','=',$code)
            ->where('client','=',$information->matricule_clients_factures)
            ->where('statut','!=',3)
            ->sum("monnaie");


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
            "monnaie"=>$monnaie,
            "a_payer"=>(double)$information->montant_verser+((double)$information->montant_total_factures_ttc -(double)$listes),
        );

        return response()->json($info,201);
    }


    public function inforRecu(){
        $listes =  DB::table("versement")
            ->orderByDesc('id_versement')
            ->get();
        return response()->json($listes,201);
    }


    public function imprimerEntete($type,$id){
        if ($type == 1){
            $commandes = DB::table('bon_commande')
                ->selectRaw('versement.code_facture,bon_commande.code_commande,
clients.nom,clients.prenoms,bon_commande.id_bon_commande,
bon_commande.date_commande,bon_commande.statut_livraison,clients.*,
bon_commande.statut_prod,sum(versement.montant_verser) as verser,bon_commande.montant_total,bon_commande.montant_total_ttc')
                ->join('clients','clients.id','bon_commande.matricule_clients')
                ->leftJoin('factures','factures.code_facture','=','bon_commande.code_facture')
                ->leftJoin('versement','factures.code_facture','=','versement.code_facture')
                ->groupByRaw('versement.code_facture,bon_commande.statut_livraison,bon_commande.date_commande,bon_commande.date_commande_update,bon_commande.code_commande,clients.nom,clients.prenoms,bon_commande.statut_prod,bon_commande.montant_total,bon_commande.montant_total_ttc')
                ->where('bon_commande.encaisser','=',1)
                ->where('clients.id','=',$id)
                ->whereNull('bon_commande.code_facture')
                ->orderByDesc('bon_commande.id_bon_commande')
                ->get();
        }else{
            $commandes = DB::table('bon_commande')
                ->selectRaw('versement.code_facture,bon_commande.code_commande,
clients.nom,clients.prenoms,bon_commande.id_bon_commande,bon_commande.code_facture,
bon_commande.date_commande,bon_commande.statut_livraison,clients.*,
bon_commande.statut_prod,sum(versement.montant_verser) as verser,bon_commande.montant_total,bon_commande.montant_total_ttc')
                ->join('clients','clients.id','bon_commande.matricule_clients')
                ->leftJoin('factures','factures.code_facture','=','bon_commande.code_facture')
                ->leftJoin('versement','factures.code_facture','=','versement.code_facture')
                ->groupByRaw('versement.code_facture,bon_commande.statut_livraison,bon_commande.date_commande,bon_commande.date_commande_update,bon_commande.code_commande,clients.nom,clients.prenoms,bon_commande.statut_prod,bon_commande.montant_total,bon_commande.montant_total_ttc')
                ->where('bon_commande.encaisser','=',1)
                ->where('clients.id','=',$id)
                ->whereNotNull('bon_commande.code_facture')
                ->orderByDesc('bon_commande.id_bon_commande')
                ->get();
        }

        return view("point",compact('commandes','type'));
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


        $info = array();

        $listes = DB::table("factures")
            ->join('clients','clients.id','=','factures.matricule_clients_factures')
            ->leftJoin('versement','versement.code_facture','=','factures.code_facture')
            ->select(DB::raw('SUM(versement.montant_verser) as verser'),'factures.*','clients.*')
            ->havingRaw('factures.montant_total_factures_ttc > COALESCE(verser, 0 )')
            ->groupBy('factures.code_facture')
            ->get();
        foreach ($listes as $liste){

            $monnaie = DB::table("monnaie")
                ->where("client",'=',$liste->matricule_clients_factures)
                ->where("statut",'=',1)
                ->sum("monnaie");
            $data = array(
                "client_id"=>$liste->matricule_clients_factures,
                "code_facture"=>$liste->code_facture,
                "matricule_clients"=>$liste->nom." ".$liste->prenoms,
                "montant_total_factures_ttc"=>$liste->montant_total_factures_ttc,
                "verser"=>$liste->verser,
                "monnaie"=>$monnaie,
            );

            array_push($info,$data);
        }

        return response()->json($info,201);
    }

    public function faire_versement(Request $request){

        $paiement = $request->paiement;
        $type = (int)$request->type;
        $montant_payer = $request->montant_verser - $request->monnaie;

        $data = array(
            "montant_verser"=>$montant_payer,
            "code_facture"=>$request->code_facture,
            "date_versement"=>$request->date_versement,
            "type_paiement"=>$request->type_paiment,
        );




        $id_insert = DB::table("versement")->insertGetId($data);

        $code = "OBF-ET".date('Y')."N".$id_insert;


        DB::table("versement")->where('id_versement','=',$id_insert)->update(array("code_versement"=>$code));


        if ($type == 2){
            if ($request->monnaie > 0){
                $client = DB::table("factures")->where('code_facture','=',$request->code_facture)->first();
                DB::table("monnaie")->insert(array("statut"=>1,"client"=>$client->matricule_clients_factures,"monnaie"=>$request->monnaie,"code_versement"=>$code));
            }
        }


        if ($type == 5){
            DB::table("monnaie")
                ->where("client",'=',$request->client_id)
                ->where("statut",'=',1)
                ->update(array(
                    "statut"=>3,
                    "code_facture"=>$request->code_facture
                ));
        }

        if ($type == 1){
            if ($request->monnaie > 0){
                $client = DB::table("factures")->where('code_facture','=',$request->code_facture)->first();
                DB::table("monnaie")->insert(array("statut"=>2,"client"=>$client->matricule_clients_factures,"monnaie"=>$request->monnaie,"code_versement"=>$code));
            }
        }

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


        DB::table('bon_commande')
            ->where("bon_commande.code_facture",'=',$request->code_facture)
            ->update(array("encaisser"=>2));

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



        $monnaie = DB::table("monnaie")
            ->where('code_versement','=',$code_recu)
            ->where('client','=',$information->matricule_clients_factures)
            ->where('statut','!=',3)
            ->sum("monnaie");




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
            "monnaie"=>$monnaie,
            "a_payer"=>(double)$information->montant_verser+((double)$information->montant_total_factures_ttc -(double)$listes),
        );


        $date_jour = $this->dateToFrench($information->date_versement,'l j F Y');

        return view("recu",compact(['info','date_jour']));

        //$pdf = PDF::loadView("commande",compact(['valeur','date_jour']))->setPaper('a4', 'portrait')->setWarnings(false);
        //return $pdf->stream();
        //return $pdf->output();

    }

}
