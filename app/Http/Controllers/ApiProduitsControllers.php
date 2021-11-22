<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiProduitsControllers extends Controller
{
    //
    public function getProduits(){
        $date_jour = date("Y-m-d");
        $valeur = array();
        $valeur["element"] = array();
        $produits = DB::table('produits')
            ->join('catalogue_produits','catalogue_produits.code_produit','=','produits.code_produit')
            ->where('catalogue_produits.created_at','=',$date_jour)
            ->select('*')->get();
        foreach($produits as $produit){
            $element = DB::table('ventes')
                ->join('factures','factures.code_facture','=','ventes.code_facture')
                ->select(DB::raw('SUM(ventes.quantite_acheter) as vendu'))
                ->where('factures.date_facture','=',$date_jour)
                ->where('ventes.code_produit','=',$produit->code_produit)->first();
            if ($element->vendu == null){
                $quantite_disponible = (int)$produit->quantite_produit;
            }else{
                $quantite_disponible = (int)$produit->quantite_produit - (int)$element->vendu;
            }
            $e = array(
                "id_article" => $produit->id,
                "code_produit" => $produit->code_produit,
                "libelle_produit" => $produit->libelle_produit,
                "quantite_produit" => $quantite_disponible,
                "prix_produit" => $produit->prix_produit,
                "prix_produit_ttc" => $produit->prix_produit_ttc,
                "quantite_acheter"=>1,
                "consulter"=>false
            );

            $valeur["element"][] = $e;
        }

        return response()->json($valeur, 201);
    }


    public function createProduits(Request $request) {

        $date_jour = date("Y-m-d");

        $code_produit = $request->code_produit;
        $libelle_produit = $request->libelle_produit;
        $quantite_produit = $request->quantite_produit;
        $prix_produit = $request->prix_produit;
        $prix_produit_ttc = $request->prix_produit_ttc;

        $data_produits = array(
            'code_produit'=>$code_produit,
            'libelle_produit'=>$libelle_produit
        );

        $data_catalogues = array(
            'code_produit'=>$code_produit,
            'quantite_produit'=>$quantite_produit,
            'prix_produit'=>$prix_produit,
            'prix_produit_ttc'=>$prix_produit_ttc,
            'created_at'=>$date_jour
        );

        $clients = DB::table('produits')->insert($data_produits);

        $insert_catalogue = DB::table('catalogue_produits')->insert($data_catalogues);

        if ($clients){
            return response()->json($clients, 201);
        }

        else{
            return response()->json( null,400);

        }

    }


    public function updateProduits(Request $request,$code_prod) {

        $date_jour = date("Y-m-d");
        $code_produit = $request->code_produit;
        $libelle_produit = $request->libelle_produit;
        $quantite_produit = $request->quantite_produit;
        $prix_produit = $request->prix_produit;
        $prix_produit_ttc = $request->prix_produit_ttc;

        $data_produits = array(
            'code_produit'=>$code_produit,
            'libelle_produit'=>$libelle_produit
        );

        $data_catalogues = array(
            'code_produit'=>$code_produit,
            'quantite_produit'=>$quantite_produit,
            'prix_produit'=>$prix_produit,
            'prix_produit_ttc'=>$prix_produit_ttc,
        );

        $clients = DB::table('produits')->where('code_produit','=',$code_prod)
            ->update($data_produits);

        $insert_catalogue = DB::table('catalogue_produits')
            ->where('code_produit','=',$code_prod)
            ->where('created_at','=',$date_jour)
            ->update($data_catalogues);

        if ($clients){
            return response()->json($clients, 201);
        }

        else{
            return response()->json( null,400);

        }

    }

    public function deleteProduits ($code_prod) {
        $date_jour = date("Y-m-d");
        $delete = DB::table('produits')->where('code_produit','=',$code_prod)->delete();
        $delete_catalogue = DB::table('catalogue_produits')->where('created_at','=',$date_jour)->where('code_produit','=',$code_prod)->delete();
        if ($delete){
            return response()->json($delete, 201);
        }
        else{
            return response()->json( null,400);

        }
    }

    public function dateToFrench($date, $format)
    {
        $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
        $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $french_months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date) ) ) );
    }

    public function imprimer_rapport($date_demande){
        $valeur = array();
        $valeur["element"] = array();
        $valeur_totaux = array();
        $valeur_totaux["total"] = array();

        $quantite_produit_total = 0;
        $prix_produit_total = 0;
        $prix_produit_ttc_total = 0;
        $quantite_vendu_total = 0;
        $quantite_commander_total=0;
        $stock_ht_total = 0;
        $stock_ttc_total = 0;
        $commander_ht_total = 0;
        $commander_ttc_total = 0;
        $ventes_ht_total = 0;
        $ventes_ttc_total = 0;
        $produits_restants_total = 0;
        $produits_restants_ht_total = 0;
        $produits_restants_ttc_total = 0;

        $produits = DB::table('produits')->select('*')->get();
        foreach($produits as $produit){
            $code_produit = $produit->code_produit;
            $quantite_vendu = DB::table('ventes')
                ->join('factures','factures.code_facture','=','ventes.code_facture')
                ->where('factures.date_facture','=',$date_demande)->where('ventes.code_produit','=',$code_produit)
                ->sum('ventes.quantite_acheter');
            $quantite_commander = DB::table('commandes')
                ->join('bon_commande','bon_commande.code_commande','=','commandes.code_commande')
                ->where('bon_commande.date_commande','=',$date_demande)
                ->where('commandes.code_produit','=',$code_produit)->sum('commandes.quantite_acheter');
            $e = array(
                "code_produit"=> $code_produit,
                "libelle_produit"=> $produit->libelle_produit,
                "quantite_produit"=> $quantite_produit = (int)$produit->quantite_produit,
                "prix_produit"=> $prix_produit = (float)$produit->prix_produit,
                "prix_produit_ttc"=> $prix_produit_ttc = (float)$produit->prix_produit_ttc,
                "quantite_vendu"=> (int)$quantite_vendu,
                "quantiter_commander"=> (int)$quantite_commander,
                "stock_ht"=> $stock_ht = (int)$produit->quantite_produit * (float)$produit->prix_produit,
                "stock_ttc"=> $stock_ttc = (int)$produit->quantite_produit * (float)$produit->prix_produit_ttc,
                "commander_ht"=> $commander_ht = (int)$quantite_commander * (float)$produit->prix_produit,
                "commander_ttc"=> $commander_ttc = (int)$quantite_commander * (float)$produit->prix_produit_ttc,
                "ventes_ht"=> $ventes_ht = (int)$quantite_vendu * (float)$produit->prix_produit,
                "ventes_ttc"=> $ventes_ttc = (int)$quantite_vendu * (float)$produit->prix_produit_ttc,
                "produits_restants"=> $produits_restants = ((int)$produit->quantite_produit-(int)$quantite_vendu),
                "produits_restants_ht"=> $produits_restants_ht=((int)$produit->quantite_produit-(int)$quantite_vendu) * (float)$produit->prix_produit,
                "produits_restants_ttc"=> $produits_restants_ttc=((int)$produit->quantite_produit-(int)$quantite_vendu) * (float)$produit->prix_produit_ttc,
            );



            $quantite_produit_total = $quantite_produit_total+$quantite_produit;
            $prix_produit_total = $prix_produit_total + $prix_produit;
            $prix_produit_ttc_total = $prix_produit_ttc_total + $prix_produit_ttc;
            $quantite_vendu_total = $quantite_vendu_total + $quantite_vendu;
            $quantite_commander_total=$quantite_commander_total + $quantite_commander;
            $stock_ht_total = $stock_ht_total + $stock_ht;
            $stock_ttc_total = $stock_ttc_total + $stock_ttc;
            $commander_ht_total = $commander_ht_total + $commander_ht;
            $commander_ttc_total = $commander_ttc_total + $commander_ttc;
            $ventes_ht_total = $ventes_ht_total + $ventes_ht;
            $ventes_ttc_total = $ventes_ttc_total + $ventes_ttc;
            $produits_restants_total = $produits_restants_total + $produits_restants;
            $produits_restants_ht_total = $produits_restants_ht_total + $produits_restants_ht;
            $produits_restants_ttc_total = $produits_restants_ttc_total + $produits_restants_ttc;


            $valeur["element"][] = $e;

        }

        $totaux_valeurs = array(
            "quantite_produit_total" => $quantite_produit_total,
            "prix_produit_total" => $prix_produit_total,
            "prix_produit_ttc_total" => $prix_produit_ttc_total,
            "quantite_vendu_total" => $quantite_vendu_total,
            "quantite_commander_total"=>$quantite_commander_total,
            "stock_ht_total" => $stock_ht_total,
            "stock_ttc_total" => $stock_ttc_total,
            "commander_ht_total" => $commander_ht_total ,
            "commander_ttc_total" => $commander_ttc_total,
            "ventes_ht_total" => $ventes_ht_total,
            "ventes_ttc_total" => $ventes_ttc_total,
            "produits_restants_total" => $produits_restants_total,
            "produits_restants_ht_total" => $produits_restants_ht_total,
            "produits_restants_ttc_total" => $produits_restants_ttc_total
        );

        $valeur_totaux["total"][] = $totaux_valeurs;
        $title = "RAPPORT DU ".$this->dateToFrench($date_demande,'l j F Y');
        return view('rapport',compact(['valeur','totaux_valeurs','title']));

        /*
        $html_content = $view->render();
        $pdf = new StatePDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetMargins(14, 40, PDF_MARGIN_RIGHT,true);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $title = "RAPPORT DU ".$this->dateToFrench($date_demande,'l j F Y');
        $pdf->setTitle($title);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage('L', 'A4');
        $pdf->writeHTML($html_content, true, false, false, false, '');
        $pdf->Output("rapport_du_{$date_demande}.pdf", 'I');
        */

    }

    public function getrapport($date_demande){
        $valeur = array();
        $valeur["element"] = array();
        $valeur_totaux = array();
        $valeur_totaux["total"] = array();

        $quantite_produit_total = 0;
        $prix_produit_total = 0;
        $prix_produit_ttc_total = 0;
        $quantite_vendu_total = 0;
        $quantite_commander_total=0;
        $stock_ht_total = 0;
        $stock_ttc_total = 0;
        $commander_ht_total = 0;
        $commander_ttc_total = 0;
        $ventes_ht_total = 0;
        $ventes_ttc_total = 0;
        $produits_restants_total = 0;
        $produits_restants_ht_total = 0;
        $produits_restants_ttc_total = 0;

        $produits = DB::table('produits')
            ->join('catalogue_produits','catalogue_produits.code_produit','=','produits.code_produit')
            ->where('catalogue_produits.created_at','=',$date_demande)
            ->select('*')->get();

        foreach($produits as $produit){
            $code_produit = $produit->code_produit;

            $quantite_vendu = DB::table('ventes')
                ->join('factures','factures.code_facture','=','ventes.code_facture')
                ->where('factures.date_facture','=',$date_demande)->where('ventes.code_produit','=',$code_produit)
                ->sum('ventes.quantite_acheter');

            $quantite_commander = DB::table('commandes')
                ->join('bon_commande','bon_commande.code_commande','=','commandes.code_commande')
                ->where('bon_commande.date_commande','=',$date_demande)
                ->where('commandes.code_produit','=',$code_produit)->sum('commandes.quantite_acheter');


            $quantite_produit = (int)$produit->quantite_produit;


            $e = array(
                "code_produit"=> $code_produit,
                "libelle_produit"=> $produit->libelle_produit,
                "quantite_produit"=> $quantite_produit,
                "prix_produit"=> $prix_produit = (float)$produit->prix_produit,
                "prix_produit_ttc"=> $prix_produit_ttc = (float)$produit->prix_produit_ttc,
                "quantite_vendu"=> (int)$quantite_vendu,
                "quantiter_commander"=> (int)$quantite_commander,
                "stock_ht"=> $stock_ht = (int)$produit->quantite_produit * (float)$produit->prix_produit,
                "stock_ttc"=> $stock_ttc = (int)$produit->quantite_produit * (float)$produit->prix_produit_ttc,
                "commander_ht"=> $commander_ht = (int)$quantite_commander * (float)$produit->prix_produit,
                "commander_ttc"=> $commander_ttc = (int)$quantite_commander * (float)$produit->prix_produit_ttc,
                "ventes_ht"=> $ventes_ht = (int)$quantite_vendu * (float)$produit->prix_produit,
                "ventes_ttc"=> $ventes_ttc = (int)$quantite_vendu * (float)$produit->prix_produit_ttc,
                "produits_restants"=> $produits_restants = ((int)$produit->quantite_produit-(int)$quantite_vendu),
                "produits_restants_ht"=> $produits_restants_ht=((int)$produit->quantite_produit-(int)$quantite_vendu) * (float)$produit->prix_produit,
                "produits_restants_ttc"=> $produits_restants_ttc=((int)$produit->quantite_produit-(int)$quantite_vendu) * (float)$produit->prix_produit_ttc,
            );



            $quantite_produit_total = $quantite_produit_total+$quantite_produit;
            $prix_produit_total = $prix_produit_total + $prix_produit;
            $prix_produit_ttc_total = $prix_produit_ttc_total + $prix_produit_ttc;
            $quantite_vendu_total = $quantite_vendu_total + $quantite_vendu;
            $quantite_commander_total=$quantite_commander_total + $quantite_commander;
            $stock_ht_total = $stock_ht_total + $stock_ht;
            $stock_ttc_total = $stock_ttc_total + $stock_ttc;
            $commander_ht_total = $commander_ht_total + $commander_ht;
            $commander_ttc_total = $commander_ttc_total + $commander_ttc;
            $ventes_ht_total = $ventes_ht_total + $ventes_ht;
            $ventes_ttc_total = $ventes_ttc_total + $ventes_ttc;
            $produits_restants_total = $produits_restants_total + $produits_restants;
            $produits_restants_ht_total = $produits_restants_ht_total + $produits_restants_ht;
            $produits_restants_ttc_total = $produits_restants_ttc_total + $produits_restants_ttc;


            $valeur["element"][] = $e;

        }

        $totaux_valeurs = array(
            "quantite_produit_total" => $quantite_produit_total,
            "prix_produit_total" => $prix_produit_total,
            "prix_produit_ttc_total" => $prix_produit_ttc_total,
            "quantite_vendu_total" => $quantite_vendu_total,
            "quantite_commander_total"=>$quantite_commander_total,
            "stock_ht_total" => $stock_ht_total,
            "stock_ttc_total" => $stock_ttc_total,
            "commander_ht_total" => $commander_ht_total ,
            "commander_ttc_total" => $commander_ttc_total,
            "ventes_ht_total" => $ventes_ht_total,
            "ventes_ttc_total" => $ventes_ttc_total,
            "produits_restants_total" => $produits_restants_total,
            "produits_restants_ht_total" => $produits_restants_ht_total,
            "produits_restants_ttc_total" => $produits_restants_ttc_total
        );

        $valeur_totaux["total"][] = $totaux_valeurs;

        return response()->json(['element'=>$valeur,'total'=>$totaux_valeurs],201);
    }
}
