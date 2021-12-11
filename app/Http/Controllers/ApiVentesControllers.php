<?php

namespace App\Http\Controllers;


use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ApiVentesControllers extends Controller
{
    //

    /*------ ------*/
    public function dateToFrench($date, $format)
    {
        $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
        $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $french_months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date) ) ) );
    }

    /*----- Ventes ------*/

    public function genererCodeFacture(){
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longueurMax = strlen($caracteres);
        $chaineAleatoire = '';
        for ($i = 0; $i < 4; $i++)
        {
            $chaineAleatoire .= $caracteres[random_int(0, $longueurMax - 1)];
        }
        return 'FACTGSOBF'.$chaineAleatoire;

    }


    /*----- Factures ------*/

    public function genererCodeCommande(){
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longueurMax = strlen($caracteres);
        $chaineAleatoire = '';
        for ($i = 0; $i < 4; $i++)
        {
            $chaineAleatoire .= $caracteres[random_int(0, $longueurMax - 1)];
        }
        return 'CMMTGSOBF'.$chaineAleatoire;

    }

    public function commandes(Request $request){
        $produits = $request->produits;
        $code_commande = $this->genererCodeCommande();
        foreach ($produits as $prod){
            $prix = (float)((int)$prod['quantite_acheter'] * (float)$prod['prix_vente']);
            $prix_vente_ttc = floor((float)$prod['prix_vente'] * 1.18);
            $prix_ttc = (float)((int)$prod['quantite_acheter'] * $prix_vente_ttc);
            DB::table('commandes')->insert(array(
                'code_produit'=>$prod['code_produit'],
                'quantite_acheter'=>$prod['quantite_acheter'],
                'code_commande'=>$code_commande,
                'prix_vente'=>(float)$prod['prix_vente'],
                'prix_ventes_ttc'=>$prix_vente_ttc,
                'total_payer'=>$prix,
                'total_payer_ttc'=>$prix_ttc
            ));
        }

        DB::table('bon_commande')->insert(array(
            'code_commande'=>$code_commande,
            'montant_total'=>(float)$request->montant_total,
            'montant_total_ttc'=>(float)$request->montant_total_ttc,
            'matricule_clients'=>(int)$request->clients,
            'date_commande'=>$request->date_commande,
        ));

        return response()->json($code_commande, 201);
    }

    public function read_commande($code_commande) {
        $facture_data = DB::table('bon_commande')
            ->join('clients','clients.id','bon_commande.matricule_clients')
            ->where('bon_commande.code_commande','=',$code_commande)->first();
        $element_facture = DB::table('commandes')
            ->join('produits','produits.code_produit','commandes.code_produit')
            ->join('catalogue_produits','catalogue_produits.code_produit','=','commandes.code_produit')
            ->where('catalogue_produits.created_at','=',date('Y-m-d'))
            ->where('commandes.code_commande','=',$code_commande)->get();
        $valeur = array('factures'=>$facture_data, 'element'=>$element_facture);
        return response()->json($valeur, 201);
    }

    public function imprimer_commande($code_commande){
        set_time_limit(300);
        $facture_data = DB::table('bon_commande')
            ->join('clients','clients.id','bon_commande.matricule_clients')
            ->where('bon_commande.code_commande','=',$code_commande)->first();
        $element_facture = DB::table('commandes')
            ->join('produits','produits.code_produit','commandes.code_produit')
            ->join('catalogue_produits','catalogue_produits.code_produit','=','commandes.code_produit')
            ->where('catalogue_produits.created_at','=',date('Y-m-d'))
            ->where('commandes.code_commande','=',$code_commande)->get();
        $valeur = array('factures'=>$facture_data, 'element'=>$element_facture);
        $date_jour = $this->dateToFrench($valeur['factures']->date_commande,'l j F Y');
        return view("commande",compact(['valeur','date_jour']));
        //$pdf = PDF::loadView("commande",compact(['valeur','date_jour']))->setPaper('a4', 'portrait')->setWarnings(false);
        //return $pdf->stream();
        //return $pdf->output();

    }

    public function imprimer_livraison($code_commande){
        set_time_limit(300);
        $facture_data = DB::table('bon_commande')
            ->join('clients','clients.id','bon_commande.matricule_clients')
            ->where('bon_commande.code_commande','=',$code_commande)->first();
        $element_facture = DB::table('commandes')
            ->join('produits','produits.code_produit','commandes.code_produit')
            ->join('catalogue_produits','catalogue_produits.code_produit','=','commandes.code_produit')
            ->where('catalogue_produits.created_at','=',date('Y-m-d'))
            ->where('commandes.code_commande','=',$code_commande)->get();
        $valeur = array('factures'=>$facture_data, 'element'=>$element_facture);
        $date_jour = $this->dateToFrench($valeur['factures']->date_commande,'l j F Y');
        return view("livraison",compact(['valeur','date_jour']));
        //$pdf = PDF::loadView("livraison",compact(['valeur','date_jour']))->setPaper('a4', 'portrait')->setWarnings(false);
        //return $pdf->stream();
        //return $pdf->output();

    }


    /**Commandes */

    public function listes_commandes(){
        $commandes = DB::table('bon_commande')
            ->selectRaw('versement.code_facture,bon_commande.code_commande,
clients.nom,clients.prenoms,
bon_commande.date_commande,bon_commande.statut_livraison,
bon_commande.statut_prod,sum(versement.montant_verser) as verser,bon_commande.montant_total,bon_commande.montant_total_ttc')
            ->join('clients','clients.id','bon_commande.matricule_clients')
            ->where('bon_commande.statut_prod','=',1)
            ->leftJoin('factures','factures.code_facture','=','bon_commande.code_facture')
            ->leftJoin('versement','factures.code_facture','=','versement.code_facture')
            ->groupByRaw('versement.code_facture,bon_commande.statut_livraison,bon_commande.date_commande,bon_commande.code_commande,clients.nom,clients.prenoms,bon_commande.statut_prod,bon_commande.montant_total,bon_commande.montant_total_ttc')
            ->get();
        return response($commandes,201);
    }

    public function listes_commandes_effectuer(){
        $commandes = DB::table('bon_commande')
           ->selectRaw('distinctrow bon_commande.matricule_clients,
clients.nom,clients.prenoms')
            ->join('clients','bon_commande.matricule_clients','=','clients.id')
            ->where('statut_prod','=',2)
            ->get();
        return response($commandes,201);
    }

    public function remplir_facture($code_commande){

        $data = DB::table('bon_commande')
            ->leftJoin('factures','factures.code_facture','=','bon_commande.code_facture')
            ->join('clients','clients.id','bon_commande.matricule_clients')
            ->where('bon_commande.code_commande','=',$code_commande)
            ->first();

        $versement = DB::table('versement')
            ->where('code_facture','=',$data->code_facture)
            ->sum('montant_verser');

        $facture_data = array(
            'id_bon_commande'=>$data->id_bon_commande,
            "code_commande"=> $data->code_commande,
            "montant_total"=> $data->montant_total,
            "montant_verser"=>$versement,
            "matricule_clients"=> $data->matricule_clients,
            "statut_prod"=> $data->statut_prod,
            "code_facture"=> $data->code_facture,
            "id"=> $data->id,
            "matricule"=> $data->matricule,
            "nom"=> $data->nom,
            "prenoms"=> $data->prenoms,
            "telephone"=> $data->telephone,
            "compte_contr"=> $data->compte_contr,
            "mail"=> $data->mail,
            "contact"=> $data->contact,
        );

        $element_facture = DB::table('commandes')
            ->join('produits','produits.code_produit','commandes.code_produit')
            ->join('catalogue_produits','catalogue_produits.code_produit','=','commandes.code_produit')
            ->where('catalogue_produits.created_at','=',date('Y-m-d'))
            ->where('commandes.code_commande','=',$code_commande)->get();
        $valeur = array('factures'=>$facture_data, 'element'=>$element_facture);

        return response()->json($valeur, 201);
    }

    /* Ventes */

    public function ventes(Request $request){
        $statut = (int) $request->statut_produit;
        if($statut == 1){
            $produits = $request->produits;
            $code_facture = $this->genererCodeFacture();
            foreach ($produits as $prod){
                $prix = (float)((int)$prod['quantite_acheter'] * (float)$prod['prix_vente']);
                $prix_vente_ttc = floor((float)$prod['prix_vente'] * 1.18);
                $prix_ttc = (float)((int)$prod['quantite_acheter'] * $prix_vente_ttc);
                DB::table('ventes')->insert(array(
                    'code_produit'=>$prod['code_produit'],
                    'quantite_acheter'=>$prod['quantite_acheter'],
                    'prix_vente'=>(float)$prod['prix_vente'],
                    'prix_ventes_ttc'=>$prix_vente_ttc,
                    'code_facture'=>$code_facture,
                    'total_payer'=>$prix,
                    'total_payer_ttc'=>$prix_ttc
                ));
            }
            DB::table('factures')->insert(array(
                'code_facture'=>$code_facture,
                'montant_total_factures'=>(float)$request->montant_total,
                'montant_total_factures_ttc'=>(float)$request->montant_total_ttc,
                'matricule_clients_factures'=>(int)$request->clients,
                'date_facture'=>$request->date_facture
            ));

            DB::table('versement')->insert(array(
                'code_facture'=>$code_facture,
                'montant_verser'=>(float)$request->somme_verse
            ));

            DB::table('bon_commande')->where('code_commande','=',$request->code_commande)->update(array(
                'statut_prod' =>2,'code_facture'=>$code_facture
            ));

        }else{
            $code_facture = $request->code_facture;
            DB::table('versement')->insert(array(
                'code_facture'=>$code_facture,
                'montant_verser'=>(float)$request->somme_verse
            ));
        }

        return response()->json($code_facture, 201);
    }

    public function read_facture($code_facture) {
        $facture_data = DB::table('factures')
            ->join('clients','clients.id','factures.matricule_clients_factures')
            ->where('factures.code_facture','=',$code_facture)->first();
        $versement = DB::table('versement')
            ->where('code_facture','=',$code_facture)
            ->sum('montant_verser');
        $versements_data = DB::table('versement')
            ->where('code_facture','=',$code_facture)
            ->get();
        $element_facture = DB::table('ventes')
            ->join('produits','produits.code_produit','ventes.code_produit')
            ->join('catalogue_produits','catalogue_produits.code_produit','=','ventes.code_produit')
            ->where('catalogue_produits.created_at','=',date('Y-m-d'))
            ->where('ventes.code_facture','=',$code_facture)->get();
        $valeur = array('factures'=>$facture_data, 'element'=>$element_facture,'versement'=>$versement,'versements_data'=>$versements_data);
        return response()->json($valeur, 201);
    }

    public function imprimer_facture($code_facture){
        $facture_data = DB::table('factures')
            ->join('clients','clients.id','factures.matricule_clients_factures')
            ->where('factures.code_facture','=',$code_facture)->first();


        $element_facture = DB::table('ventes')
            ->join('produits','produits.code_produit','ventes.code_produit')
            ->join('catalogue_produits','catalogue_produits.code_produit','=','ventes.code_produit')
            ->where('catalogue_produits.created_at','=','2021-11-23')
            ->where('ventes.code_facture','=',$code_facture)->get();

        $versement = DB::table('versement')
            ->where('code_facture','=',$code_facture)
            ->sum('montant_verser');

        $versements_data = DB::table('versement')
            ->where('code_facture','=',$code_facture)
            ->get();

        $valeur = array('factures'=>$facture_data, 'element'=>$element_facture,'versement'=>$versement,'versements_data'=>$versements_data);
        $date_jour = $this->dateToFrench($valeur['factures']->date_facture,'l j F Y');

        return view("facture",compact(['valeur','date_jour']));
        //$pdf = PDF::loadView("facture",compact(['valeur','date_jour']))->setPaper('a4', 'portrait')->setWarnings(false);
        //return $pdf->stream();
        //return $pdf->output();

    }

    //bon_livraison

    public function read_bon_livraison($code_commande){

        $produits = DB::table('produits')
            ->selectRaw('
            produits.id as id_bon_livraison,produits.code_produit,produits.libelle_produit
       ,catalogue_produits.quantite_produit , sum(ventes.quantite_acheter) as quantite_vendu,
commandes.quantite_acheter,catalogue_produits.prix_produit,catalogue_produits.prix_produit_ttc,commandes.prix_vente,commandes.prix_ventes_ttc,commandes.quantite_acheter as comm_quantite
            ')
            ->join('catalogue_produits','catalogue_produits.code_produit','=','produits.code_produit')
            ->join('commandes','commandes.code_produit','produits.code_produit')
            ->leftJoin('ventes','ventes.code_produit','=','produits.code_produit')
            ->where('commandes.code_commande','=',$code_commande)
            ->where('catalogue_produits.created_at','=',date('Y-m-d'))
            ->groupByRaw('commandes.quantite_acheter,catalogue_produits.prix_produit_ttc,produits.code_produit, produits.libelle_produit,
            produits.id, catalogue_produits.prix_produit,catalogue_produits.quantite_produit,commandes.prix_vente,commandes.prix_ventes_ttc')
            ->get();

        foreach($produits as $produit){
            $quantite_disponible = (int)$produit->quantite_produit - (int)$produit->quantite_vendu;
            $e = array(
                "id_article" => $produit->id_bon_livraison,
                "code_produit" => $produit->code_produit,
                "libelle_produit" => $produit->libelle_produit,
                "quantite_produit" => $quantite_disponible,
                "prix_produit" => $produit->prix_produit,
                "prix_produit_ttc"=>$produit->prix_produit_ttc,
                "prix_vente" => $produit->prix_vente,
                "prix_vente_ttc"=>$produit->prix_ventes_ttc,
                "quantite_acheter"=>$produit->comm_quantite,
            );

            $valeur["element"][] = $e;
        }
        return response()->json($valeur, 201);
    }


    public function update_livraison(Request $request,$code_commande){

        $produits = $request->produits;

        foreach ($produits as $prod){

            $prix = (float)((int)$prod['quantite_acheter'] * (float)$prod['prix_vente']);
            $prix_vente_ttc = floor((float)$prod['prix_vente'] * 1.18);
            $prix_ttc = (float)((int)$prod['quantite_acheter'] * $prix_vente_ttc);

            DB::table('commandes')
                ->where('code_produit','=',$prod['code_produit'])
                ->where('code_commande','=',$code_commande)
                ->update(array(
                    'quantite_acheter'=>$prod['quantite_acheter'],
                    'prix_vente'=>(float)$prod['prix_vente'],
                    'prix_ventes_ttc'=>$prix_vente_ttc,
                    'total_payer'=>$prix,
                    'total_payer_ttc' =>$prix_ttc
                ))
            ;
        }

        $update = DB::table('bon_commande')
            ->where('code_commande','=',$code_commande)
            ->update(array(
                'montant_total' =>(float)$request->montant_total,
                'montant_total_ttc' =>(float)$request->montant_total_ttc,
                'statut_livraison'=>2,
                'date_commande' => $request->date_commande
            ));

        return response()->json($update, 201);


    }



}
