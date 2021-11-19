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
            $prix = (float)((int)$prod['quantite_acheter'] * (float)$prod['prix_produit']);
            DB::table('commandes')->insert(array(
                'code_produit'=>$prod['code_produit'],
                'quantite_acheter'=>$prod['quantite_acheter'],
                'code_commande'=>$code_commande,
                'total_payer'=>$prix
            ));
        }
        DB::table('bon_commande')->insert(array(
            'code_commande'=>$code_commande,
            'montant_total'=>(float)$request->montant_total,
            'matricule_clients'=>(int)$request->clients
        ));

        return response()->json($code_commande, 201);
    }

    public function read_commande($code_commande) {
        $facture_data = DB::table('bon_commande')
            ->join('clients','clients.id','bon_commande.matricule_clients')
            ->where('bon_commande.code_commande','=',$code_commande)->first();
        $element_facture = DB::table('commandes')
            ->join('produits','produits.code_produit','commandes.code_produit')
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
            ->where('commandes.code_commande','=',$code_commande)->get();
        $valeur = array('factures'=>$facture_data, 'element'=>$element_facture);
        $date_jour = $this->dateToFrench(date('Y-m-d'),'l j F Y');
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
            ->where('commandes.code_commande','=',$code_commande)->get();
        $valeur = array('factures'=>$facture_data, 'element'=>$element_facture);
        $date_jour = $this->dateToFrench(date('Y-m-d'),'l j F Y');
        return view("livraison",compact(['valeur','date_jour']));
        //$pdf = PDF::loadView("livraison",compact(['valeur','date_jour']))->setPaper('a4', 'portrait')->setWarnings(false);
        //return $pdf->stream();
        //return $pdf->output();

    }


    /**Commandes */

    public function listes_commandes(){
        $commandes = DB::table('bon_commande')
            ->join('clients','clients.id','bon_commande.matricule_clients')
            ->get();
        return response($commandes,201);
    }

    public function remplir_facture($code_commande){
        $facture_data = DB::table('bon_commande')
            ->join('clients','clients.id','bon_commande.matricule_clients')
            ->where('bon_commande.code_commande','=',$code_commande)->first();

        $element_facture = DB::table('commandes')
            ->join('produits','produits.code_produit','commandes.code_produit')
            ->where('commandes.code_commande','=',$code_commande)->get();
        $valeur = array('factures'=>$facture_data, 'element'=>$element_facture);

        return response()->json($valeur, 201);
    }

    /* Ventes */

    public function ventes(Request $request){
        $produits = $request->produits;
        $code_facture = $this->genererCodeFacture();

        foreach ($produits as $prod){
            $prix = (float)((int)$prod['quantite_acheter'] * (float)$prod['prix_produit']);
            DB::table('ventes')->insert(array(
                'code_produit'=>$prod['code_produit'],
                'quantite_acheter'=>$prod['quantite_acheter'],
                'code_facture'=>$code_facture,
                'total_payer'=>$prix
            ));
        }

        DB::table('factures')->insert(array(
            'code_facture'=>$code_facture,
            'montant_total'=>(float)$request->montant_total,
            'montant_verser'=>(float)$request->somme_verse,
            'montant_rendu'=>(float)$request->somme_rendu,
            'matricule_clients'=>(int)$request->clients
        ));

        DB::table('bon_commande')->where('code_commande','=',$request->code_commande)->update(array(
            'statut_prod' =>2,'code_facture'=>$code_facture
        ));

        return response()->json($code_facture, 201);
    }

    public function read_facture($code_facture) {
        $facture_data = DB::table('factures')
            ->join('clients','clients.id','factures.matricule_clients')
            ->where('factures.code_facture','=',$code_facture)->first();
        $element_facture = DB::table('ventes')
            ->join('produits','produits.code_produit','ventes.code_produit')
            ->where('ventes.code_facture','=',$code_facture)->get();
        $valeur = array('factures'=>$facture_data, 'element'=>$element_facture);
        return response()->json($valeur, 201);
    }

    public function imprimer_facture($code_facture){
        set_time_limit(300);
        $facture_data = DB::table('factures')
            ->join('clients','clients.id','factures.matricule_clients')
            ->where('factures.code_facture','=',$code_facture)->first();
        $element_facture = DB::table('ventes')
            ->join('produits','produits.code_produit','ventes.code_produit')
            ->where('ventes.code_facture','=',$code_facture)->get();
        $valeur = array('factures'=>$facture_data, 'element'=>$element_facture);
        $date_jour = $this->dateToFrench(date('Y-m-d'),'l j F Y');
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
       ,produits.quantite_produit , sum(ventes.quantite_acheter) as quantite_vendu,
commandes.quantite_acheter,produits.prix_produit,commandes.quantite_acheter as comm_quantite
            ')
            ->join('commandes','commandes.code_produit','produits.code_produit')
            ->leftJoin('ventes','ventes.code_produit','=','produits.code_produit')
            ->where('commandes.code_commande','=',$code_commande)
            ->groupByRaw('commandes.quantite_acheter, produits.code_produit, produits.libelle_produit, produits.id, produits.prix_produit')
            ->get();

        foreach($produits as $produit){
            $quantite_disponible = (int)$produit->quantite_produit - (int)$produit->quantite_vendu;
            $e = array(
                "id_article" => $produit->id_bon_livraison,
                "code_produit" => $produit->code_produit,
                "libelle_produit" => $produit->libelle_produit,
                "quantite_produit" => $quantite_disponible,
                "prix_produit" => $produit->prix_produit,
                "quantite_acheter"=>$produit->comm_quantite,
            );

            $valeur["element"][] = $e;
        }
        return response()->json($valeur, 201);
    }


    public function update_livraison(Request $request,$code_commande){

        $produits = $request->produits;

        foreach ($produits as $prod){
            $prix = (float)((int)$prod['quantite_acheter'] * (float)$prod['prix_produit']);
            DB::table('commandes')
                ->where('code_produit','=',$prod['code_produit'])
                ->where('code_commande','=',$code_commande)
                ->update(array(
                    'quantite_acheter'=>$prod['quantite_acheter'],
                    'total_payer'=>$prix
                ))
            ;
        }

        $update = DB::table('bon_commande')
            ->where('code_commande','=',$code_commande)
            ->update(array(
            'montant_total' =>(float)$request->montant_total
        ));

        return response()->json($update, 201);


    }



}
