<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiProduitsControllers extends Controller
{
    //
    public function getProduits(){
        $valeur = array();
        $valeur["element"] = array();
        $produits = DB::table('produits')->select('*')->get();
        foreach($produits as $produit){
            $element = DB::table('ventes')
                ->select(DB::raw('SUM(quantite_acheter) as vendu'))
                ->where('code_produit','=',$produit->code_produit)->first();
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

        $code_produit = $request->input('code_produit');
        $libelle_produit = $request->input('libelle_produit');
        $quantite_produit = $request->input('quantite_produit');
        $prix_produit = $request->input('prix_produit');
        $prix_produit_ttc = $request->input('prix_produit_ttc');

        $data = array(
            'code_produit'=>$code_produit,
            'libelle_produit'=>$libelle_produit,
            'quantite_produit'=>$quantite_produit,
            'prix_produit'=>$prix_produit,
            'prix_produit_ttc'=>$prix_produit_ttc
        );

        $clients = DB::table('produits')->insert($data);

        if ($clients){
            return response()->json($clients, 201);
        }

        else{
            return response()->json( null,400);

        }

    }


    public function updateProduits(Request $request,$id) {
        $code_produit = $request->input('code_produit');
        $libelle_produit = $request->input('libelle_produit');
        $quantite_produit = $request->input('quantite_produit');
        $prix_produit = $request->input('prix_produit');
        $prix_produit_ttc = $request->input('prix_produit_ttc');

        $data = array(
            'code_produit'=>$code_produit,
            'libelle_produit'=>$libelle_produit,
            'quantite_produit'=>$quantite_produit,
            'prix_produit'=>$prix_produit,
            'prix_produit_ttc'=>$prix_produit_ttc
        );

        $clients = DB::table('produits')->where('id','=',$id)
            ->update($data);

        if ($clients){
            return response()->json($clients, 201);
        }

        else{
            return response()->json( null,400);

        }

    }

    public function deleteProduits ($id) {
        $delete = DB::table('produits')->where('id','=',$id)->delete();
        if ($delete){
            return response()->json($delete, 201);
        }
        else{
            return response()->json( null,400);

        }
    }
}
