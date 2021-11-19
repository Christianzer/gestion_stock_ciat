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
        $clients = DB::table('clients')->where('statut','=',1)
            ->select(DB::raw('count(id) as nbre_clients'))->first();

        $produits = DB::table('produits')->select(DB::raw('count(id) as id_produit'))->first();

        $chiffres_affaire = DB::table('produits')
            ->selectRaw('produits.code_produit,produits.libelle_produit,produits.quantite_produit,
            sum(ventes.quantite_acheter) as quantite_vendu,(produits.quantite_produit * prix_produit) as a_vendre
            ,sum(ventes.total_payer) as payer')
            ->leftJoin('ventes','produits.code_produit','=','ventes.code_produit')
            ->groupByRaw('produits.code_produit,produits.libelle_produit,produits.quantite_produit,produits.prix_produit')->get();

        $ventes_realiser = DB::table('factures')->select(DB::raw('sum(montant_total) as montant_total'))->first();

        $ventes_a_realiser = DB::table('produits')->select(DB::raw('sum(prix_produit * quantite_produit) as montant'))->first();

        $top_five_clients = DB::table('ventes')
            ->selectRaw('clients.nom,clients.prenoms,clients.telephone,sum(ventes.quantite_acheter) as prod_acheter')
            ->join('factures','ventes.code_facture','=','factures.code_facture')
            ->join('clients','clients.id','=','factures.matricule_clients')
            ->groupByRaw('clients.id,clients.nom,clients.prenoms,clients.telephone')
            ->orderByDesc('prod_acheter')
            ->limit(5)
            ->get();

        $top_fives_produits = DB::table('produits')
            ->selectRaw('produits.code_produit,produits.libelle_produit,sum(ventes.quantite_acheter) as prod_acheter')
            ->join('ventes','ventes.code_produit','=','produits.code_produit')
            ->groupByRaw('produits.code_produit,produits.libelle_produit')
            ->orderByDesc('prod_acheter')
            ->limit(5)
            ->get();

        return response()->json(array(
            'clients'=>$clients,
            'produits'=>$produits,
            'ventes_realiser'=>$ventes_realiser,
            'ventes_a_realiser' =>$ventes_a_realiser,
            'chiffres_affaire' =>$chiffres_affaire,
            'top_five_clients'=>$top_five_clients,
            'top_fives_produits'=>$top_fives_produits
        ), 201);
    }


}
