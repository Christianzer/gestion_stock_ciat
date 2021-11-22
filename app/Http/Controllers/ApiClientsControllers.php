<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiClientsControllers extends Controller
{
    public function getAllClients() {
        $clients = DB::table('clients')
            ->where('statut','=',1)
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

    public function getData() {


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
            ->where('catalogue_produits.created_at','=',$date_jour)->get();

        foreach ($chiffres_affaires_jour as $prod){

            $vendu_produits = DB::table('factures')
                ->join('ventes','ventes.code_facture','=','factures.code_facture')
                ->where('ventes.code_produit','=',$prod->code_produit)
                ->where('factures.date_facture','<',$date_jour)->sum('ventes.quantite_acheter');

            $conso_prod = (int)$prod->quantite_produit + (int)$vendu_produits;
            $quantite_vendu = DB::table('ventes')->where('code_produit','=',$prod->code_produit)->sum('quantite_acheter');

            $e = array(
                'code_produit'=>$prod->code_produit,
                'libelle_produit'=>$prod->libelle_produit,
                'quantite_produit'=>$conso_prod,
                'quantite_vendu'=>$quantite_vendu
            );

            $valeur["element"][] = $e;

        }

        /*
        $chiffres_affaire = DB::table('produits')
            ->join('catalogue_produits','catalogue_produits.code_produit','=','produits.code_produit')
            ->selectRaw('produits.code_produit,produits.libelle_produit,catalogue_produits.quantite_produit,
            sum(ventes.quantite_acheter) as quantite_vendu,(catalogue_produits.quantite_produit * prix_produit) as a_vendre
            ,sum(ventes.total_payer) as payer')
            ->leftJoin('ventes','produits.code_produit','=','ventes.code_produit')
            ->groupByRaw('produits.code_produit,produits.libelle_produit,catalogue_produits.quantite_produit,catalogue_produits.prix_produit')->get();
*/

        $ventes_realiser = DB::table('factures')->select(DB::raw('sum(montant_total_factures) as montant_total'))->first();
        $ventes_realiser_ttc = DB::table('factures')->select(DB::raw('sum(montant_total_factures_ttc) as montant_total'))->first();

        $ventes_a_realiser = 0;
        $ventes_a_realiser_ttc = 0;

        $produits_jour = DB::table('catalogue_produits')
            ->where('created_at','=',$date_jour)->get();

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

        return response()->json(array(
            'clients'=>$clients,
            'produits'=>$produits,
            'chiffres_affaire'=>$valeur["element"],
            'ventes_realiser'=>$ventes_realiser,
            'ventes_a_realiser' =>$ventes_a_realiser,
            'ventes_realiser_ttc'=>$ventes_realiser_ttc,
            'ventes_a_realiser_ttc' =>$ventes_a_realiser_ttc,
            'top_five_clients'=>$top_five_clients,
            'top_fives_produits'=>$top_fives_produits
        ), 201);


    }

    public function initialiser($date_initiator){

        $voir_element_avant = DB::table('catalogue_produits')
            ->where('created_at','<',$date_initiator)
            ->orderByDesc('created_at')->get();

        foreach ($voir_element_avant as $ajouter_prod){

            DB::table('catalogue_produits')->insert(array(
                'code_produit'=>$ajouter_prod->code_produit,
                'quantite_produit'=>(int)$ajouter_prod->quantite_produit,
                'prix_produit'=>(float)$ajouter_prod->prix_produit,
                'prix_produit_ttc'=>(float)$ajouter_prod->prix_produit_ttc,
                'created_at'=>$date_initiator
            ));
        }
    }


}
