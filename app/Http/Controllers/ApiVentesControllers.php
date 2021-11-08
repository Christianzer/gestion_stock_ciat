<?php

namespace App\Http\Controllers;


use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ApiVentesControllers extends Controller
{
    //
    public function genererCodeFacture(){
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longueurMax = strlen($caracteres);
        $chaineAleatoire = '';
        for ($i = 0; $i < 4; $i++)
        {
            $chaineAleatoire .= $caracteres[random_int(0, $longueurMax - 1)];
        }
        return 'GSCIAT'.$chaineAleatoire;

    }

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

    public function dateToFrench($date, $format)
    {
        $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
        $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $french_months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date) ) ) );
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
        //return view("facture",compact(['valeur','date_jour']));
         $pdf = PDF::loadView("facture",compact(['valeur','date_jour']))->setPaper('a4', 'portrait')
            ->setWarnings(false);
        return $pdf->stream();
        //return $pdf->output();

    }


}
