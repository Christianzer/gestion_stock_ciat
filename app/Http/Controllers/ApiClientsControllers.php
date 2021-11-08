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


    public function updateClient(Request $request,$id) {
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
        return response()->json(array($clients,$produits), 201);
    }


}
