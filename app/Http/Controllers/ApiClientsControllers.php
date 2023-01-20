<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ifsnop\Mysqldump as IMysqldump;

class ApiClientsControllers extends Controller
{
    public function getAllClients() {
        $clients = DB::table('clients')
            ->where('statut','=',1)
            ->select('*')
            ->get()->toJson();
        return response($clients,200);
    }
    
      public function getClientsFacturesNew($matricule){
        $information = DB::table('factures')
            ->join('bon_commande','bon_commande.code_facture','=',"factures.code_facture")
            ->where('factures.matricule_clients_factures','=',$matricule)
            ->where('bon_commande.encaisser','=',2)
            ->get();
        return response()->json($information, 201);
    }
    
    public function getAllClientsFactures() {
        $clients = DB::table('bon_commande')
            ->join('clients','clients.id','bon_commande.matricule_clients')
            ->where('clients.statut','=',1)
            ->groupBy('clients.id')
            ->select('*')
            ->get()->toJson();
        return response($clients,200);
    }

    public function createClient(Request $request) {

        $matricule = $request->input('matricule');
        $nom = $request->input('nom');
        $prenoms = $request->input('prenoms');
        $telephone = $request->input('telephone');
        $contact = $request->input('contact');
        $compte_contr = $request->input('compte_contr');
        $mail = $request->input('mail');

        $data = array(
            'matricule'=>$matricule,
            'nom'=>$nom,
            'prenoms'=>$prenoms,
            'telephone'=>$telephone,
            'contact'=>$contact,
            'compte_contr'=>$compte_contr,
            'mail'=>$mail
        );

        $clients = DB::table('clients')->insert($data);

        if ($clients){
            return response()->json($clients, 201);
        }

        else{
            return response()->json( null,400);

        }

    }


    public function updateClient(Request $request,$id) {
        $matricule = $request->input('matricule');
        $nom = $request->input('nom');
        $prenoms = $request->input('prenoms');
        $telephone = $request->input('telephone');
        $contact = $request->input('contact');
        $compte_contr = $request->input('compte_contr');
        $mail = $request->input('mail');

        $data = array(
            'matricule'=>$matricule,
            'nom'=>$nom,
            'prenoms'=>$prenoms,
            'telephone'=>$telephone,
            'contact'=>$contact,
            'compte_contr'=>$compte_contr,
            'mail'=>$mail
        );

        $clients = DB::table('clients')->where('id','=',$id)
            ->update($data);

        if ($clients){
            return response()->json($clients, 201);
        }

        else{
            return response()->json( null,400);

        }

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


    public function deleteClient ($id) {
        $update = DB::table('clients')
            ->where('id','=',$id)->update(array(
                'statut'=>2
            ));
        if ($update){
            return response()->json($update, 201);
        }
        else{
            return response()->json( null,400);

        }
    }



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



    public function getData() {




        //pour les versements sans date
        /*
        $versement = DB::table('versement')
            ->where("code_versement",'=',null)
            ->where("date_versement",'=',null)
            ->get();
        foreach ($versement as $value){
            $date_facture = DB::table('factures')->where('code_facture','=',$value->code_facture)->first();
            if (isset($date_facture)){
                if (is_null($date_facture->date_facture_update)){
                    $date_versement = $date_facture->date_facture;
                }else{
                    $date_versement = $date_facture->date_facture_update;
                }
                $code = "OBF-ET".date('Y')."N".$value->id_versement;
                DB::table("versement")
                    ->where('code_facture','=',$date_facture->code_facture)
                    ->update(array(
                        "date_versement"=>$date_versement,
                        "code_versement"=>$code
                    ));
                $dataPaiement = array(
                    "code_versement"=>$code,
                    "paiement"=>json_encode(array(
                        "type_paiement"=>"Espèce",
                        "montant"=>$value->montant_verser,
                        "banque"=>null,
                        "numero_cheque"=>null,
                        "numero_telephone"=>null,
                        "reseau"=>null
                    ))
                );

                DB::table("information_paiement")->insert($dataPaiement);
            }
        }
*/

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


        $clients = DB::table('clients')->where('statut','=',1)
            ->select(DB::raw('count(id) as nbre_clients'))->first();

        $produits = DB::table('produits')->select(DB::raw('count(id) as id_produit'))->first();


        $chiffres_affaires_jour = DB::table('catalogue_produits')
            ->join('produits','catalogue_produits.code_produit','=','produits.code_produit')
           ->groupBy('catalogue_produits.code_produit')
            ->orderBy('catalogue_produits.created_at','asc')
            ->limit(5)
            ->get();

        foreach ($chiffres_affaires_jour as $prod){

            $vendu_produits = DB::table('bon_commande')
                ->join('commandes','commandes.code_commande','=','bon_commande.code_commande')
                ->where('commandes.code_produit','=',$prod->code_produit)
                ->where('bon_commande.statut_livraison','=',2)
                 ->sum('commandes.quantite_acheter');


            $conso_prod = (int)$prod->quantite_produit;


            $e = array(
                'code_produit'=>$prod->code_produit,
                'libelle_produit'=>$prod->libelle_produit,
                'quantite_produit'=>$conso_prod,
                'quantite_vendu'=>$vendu_produits
            );

            $valeur["element"][] = $e;

        }

        $chiffres_affaire = DB::table('produits')
            ->join('catalogue_produits','catalogue_produits.code_produit','=','produits.code_produit')
            ->selectRaw('produits.code_produit,produits.libelle_produit,catalogue_produits.quantite_produit,
            sum(ventes.quantite_acheter) as quantite_vendu,(catalogue_produits.quantite_produit * prix_produit) as a_vendre
            ,sum(ventes.total_payer) as payer')
            ->leftJoin('ventes','produits.code_produit','=','ventes.code_produit')
            ->groupByRaw('produits.code_produit,produits.libelle_produit,catalogue_produits.quantite_produit,catalogue_produits.prix_produit')->get();

        $ventes_realiser_ttc = DB::table('versement')->select(DB::raw('sum(montant_verser) as montant_total'))->first();
        $ventes_realiser = DB::table('versement')->select(DB::raw('sum(montant_verser)/1.18 as montant_total'))->first();


        $ventes_a_realiser = 0;
        $ventes_a_realiser_ttc = 0;

       $produits_jour = DB::table('catalogue_produits')
            ->join('produits','catalogue_produits.code_produit','=','produits.code_produit')
            ->groupBy('catalogue_produits.code_produit')
            ->orderBy('catalogue_produits.created_at','asc')
            ->limit(5)
            ->get();

        foreach ($produits_jour as $prod){

            $vendu_produits = DB::table('factures')
                ->join('ventes','ventes.code_facture','=','factures.code_facture')
                ->where('ventes.code_produit','=',$prod->code_produit)
                ->where('factures.date_facture','<',$date_jour)->sum('ventes.quantite_acheter');

            $conso_prod = (int)$prod->quantite_produit + (int)$vendu_produits;

            $prix_prod_ht = $conso_prod * $prod->prix_produit;
            $prix_ttc = $conso_prod * $prod->prix_produit_ttc;

            $ventes_a_realiser = $ventes_a_realiser + $prix_prod_ht;
            $ventes_a_realiser_ttc = $ventes_a_realiser_ttc + $prix_ttc;

        }

        $top_five_clients = DB::table('ventes')
            ->selectRaw('clients.nom,clients.prenoms,clients.telephone,sum(ventes.quantite_acheter) as prod_acheter')
            ->join('factures','ventes.code_facture','=','factures.code_facture')
            ->join('clients','clients.id','=','factures.matricule_clients_factures')
            ->groupByRaw('clients.id,clients.nom,clients.prenoms,clients.telephone')
            ->orderByDesc('prod_acheter')
            ->limit(5)
            ->get();

        $top_fives_produits = DB::table('produits')
            ->selectRaw('produits.code_produit,produits.libelle_produit,sum(ventes.quantite_acheter) as prod_acheter')
            ->join('ventes','ventes.code_produit','=','produits.code_produit')
            ->groupByRaw('produits.code_produit,produits.libelle_produit')
            ->orderByDesc('prod_acheter')
            ->limit(3)
            ->get();

        $decaissement = DB::table('sortie_caisse')->sum('sortie_caisse.montant_sortie_caisse');
        $appro = DB::table('entre_caisse')->sum('entre_caisse.montant_entre_caisse');

        return response()->json(array(
            'clients'=>$clients,
            'produits'=>$produits,
            'chiffres_affaire'=>$valeur["element"],
            'ventes_realiser'=>$ventes_realiser,
            'ventes_a_realiser' =>$ventes_a_realiser,
            'ventes_realiser_ttc'=>$ventes_realiser_ttc,
            'ventes_a_realiser_ttc' =>$ventes_a_realiser_ttc,
            'top_five_clients'=>$top_five_clients,
            'top_fives_produits'=>$top_fives_produits,
            'decaissement'=>$decaissement,
            'appro'=>$appro
        ), 201);

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

    public function getClientsCommande($matricule){
        $information = DB::table('bon_commande')
            ->where('statut_prod','=',2)
            ->where('matricule_clients','=',$matricule)
            ->get();
        return response()->json($information, 201);

    }

    public function getClientsFactures($matricule){
        $information = DB::table('factures')->where('matricule_clients_factures','=',$matricule)->get();
        return response()->json($information, 201);
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


}
