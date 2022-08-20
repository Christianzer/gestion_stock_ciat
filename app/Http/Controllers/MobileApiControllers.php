<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ifsnop\Mysqldump as IMysqldump;


class MobileApiControllers extends Controller
{
    public function verifiez_bd_jour(){
        $date_jour = date("Y-m-d");
        $db_exists = DB::table('sauvegarde_bd')->where('date_sauvegarde','=',$date_jour)->exists();
        return $db_exists ;
    }

    public function verifiez_bd_avant(){
        $date_jour = date("Y-m-d");
        $datebdrecent = DB::table('sauvegarde_bd')->where('date_sauvegarde','<',$date_jour)->exists();
        return $datebdrecent;
    }

    public function NbJours($debut, $fin) {

        $tDeb = explode("-", $debut);
        $tFin = explode("-", $fin);

        $diff = mktime(0, 0, 0, $tFin[1], $tFin[2], $tFin[0]) -
            mktime(0, 0, 0, $tDeb[1], $tDeb[2], $tDeb[0]);

        return(($diff / 86400));

    }

    public function sauvegarde_bd(){

        $date_jour = date("Y-m-d");

        $datebdrecent = DB::table('sauvegarde_bd')->where('date_sauvegarde','<',$date_jour)
            ->orderByDesc('date_sauvegarde')
            ->limit(1)
            ->first();


        if($datebdrecent == null){
            $date_default = $date_jour;
        }else{
            $date_default = $datebdrecent->date_sauvegarde;
        }


        $nbres_jours = $this->NbJours($date_default,$date_jour);

        return $nbres_jours > 7;

    }

    public function db_save(){
        $date_jour = date("Y-m-d");
        $username = env('DB_USERNAME');
        $mdp = env('DB_PASSWORD');
        $host = env('DB_HOST');
        $database = env('DB_DATABASE');
        $filenames = "\save_bd_".$date_jour.".sql";
        $filename = storage_path() . "\app\backup" . $filenames;
        try {
            $dump = new IMysqldump\Mysqldump('mysql:host='.$host.';dbname='.$database.'', $username, $mdp);
            $dump->start($filename);
            DB::table('sauvegarde_bd')->insert(array(
                'date_sauvegarde'=>$date_jour
            ));
        } catch (\Exception $e) {
            echo 'mysqldump-php error: ' . $e->getMessage();
        }
    }

    public function initialiser($date_initiator){

        $date_avant = DB::table('catalogue_produits')
            ->where('created_at','<',$date_initiator)
            ->orderByDesc('created_at')
            ->limit(1)
            ->first();

        $voir_element_avant = DB::table('catalogue_produits')
            ->where('created_at','=',$date_avant->created_at)
            ->get();

        foreach ($voir_element_avant as $ajouter_prod){

            $voir_vente = DB::table('bon_commande')
                ->join('commandes','commandes.code_commande','=','bon_commande.code_commande')
                ->where('commandes.code_produit','=',$ajouter_prod->code_produit)
                ->where('bon_commande.statut_livraison','=',2)
                ->where('bon_commande.date_commande','=',$date_avant->created_at)->sum('commandes.quantite_acheter');

            DB::table('catalogue_produits')->insert(array(
                'code_produit'=>$ajouter_prod->code_produit,
                'quantite_produit'=>(int)$ajouter_prod->quantite_produit - (int)$voir_vente,
                'prix_produit'=>(float)$ajouter_prod->prix_produit,
                'prix_produit_ttc'=>(float)$ajouter_prod->prix_produit_ttc,
                'created_at'=>$date_initiator
            ));
        }
    }

    public function getData() {


        $verifiez_bd_jour = $this->verifiez_bd_jour();
        $verifiez_avant = $this->verifiez_bd_avant();
        $maj_recent = $this->sauvegarde_bd();

        if (!$verifiez_bd_jour){
            if(!$verifiez_avant){
                $this->db_save();
            }else{
                if($maj_recent){
                    $this->db_save();
                }
            }
        }


        $valeur = array();
        $valeur["element"] = array();

        //initialiser produit jour
        $date_jour = date("Y-m-d");

        $voir_element_jour = DB::table('catalogue_produits')->where('created_at','=',$date_jour)->exists();

        $voir_element_avant = DB::table('catalogue_produits')
            ->where('created_at','<',$date_jour)
            ->orderByDesc('created_at')->exists();

        if(!$voir_element_jour && $voir_element_avant){
            $this->initialiser($date_jour);
        }


    }

    public function MobilecreateClient(Request $request) {

        $matricule = $request->input('matricule');
        $nom = $request->input('nom');
        $prenoms = $request->input('prenoms');
        $telephone = $request->input('telephone');

        $data = array(
            'matricule'=>$matricule,
            'nom'=>$nom,
            'prenoms'=>$prenoms,
            'telephone'=>$telephone,
        );

        $clients = DB::table('clients')->insert($data);

        if ($clients){
            return response()->json($clients, 201);
        }

        else{
            return response()->json( null,400);

        }

    }

    public function MobileListesClients(){
        $this->getData();
        $clients = DB::table('clients')
            ->where('statut','=',1)
            ->select('*')
            ->get()->toJson();
        return response($clients,200);
    }
}
