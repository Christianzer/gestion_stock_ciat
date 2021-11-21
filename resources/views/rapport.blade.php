<?php
function formatage($valeur) {
    return number_format($valeur,'0','.',' ');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        .center_page {
            padding: 150px 0;
        }
    </style>
</head>
<body>
<div class="invoice-box center_page">
    <h2 class="text-center text-uppercase text-primary">{{$title}}</h2>
    <br>
    <table class="table table-bordered" style="font-size: 12px;">
        <thead>
        <tr class="text-center">
            <th colspan="3">Informations produits</th>
            <!--
             <th colspan="3">Stocks initials</th>
             -->
            <th colspan="3">Commandes</th>
            <th colspan="3">Ventes</th>
            <!--
               <th colspan="3">Stocks restants</th>
             -->

        </tr>
        </thead>
        <tbody>
        <tr class="text-left">
            <th>libelle produit</th>
            <!--
             <th>nbr prod</th>
            <th>mont ht FCFA</th>
            <th>mont ttc FCFA</th>
             -->
            <th>prix ht FCFA</th>
            <th>prix ttc FCFA</th>
            <th>nbr prod</th>
            <th>mont ht FCFA</th>
            <th>mont ttc FCFA</th>
            <th>nbr prod</th>
            <th>mont ht FCFA</th>
            <th>mont ttc FCFA</th>
            <!--
              <th>nbr prod</th>
            <th>mont ht FCFA</th>
            <th>mont ttc FCFA</th>
             -->

        </tr>
        @foreach($valeur['element'] as $produits)
            <tr>
                <td class="text-left">{{$produits['libelle_produit']}}</td>
                <td class="text-right">{{formatage($produits['prix_produit'])}}</td>
                <td class="text-right">{{formatage($produits['prix_produit_ttc'])}}</td>
                <!--
                   <td class="text-right">{{formatage($produits['quantite_produit'])}}</td>
                <td class="text-right">{{formatage($produits['stock_ht'])}}</td>
                <td class="text-right">{{formatage($produits['stock_ttc'])}}</td>
                 -->

                <td class="text-right">{{formatage($produits['quantiter_commander'])}}</td>
                <td class="text-right">{{formatage($produits['commander_ht'])}}</td>
                <td class="text-right">{{formatage($produits['commander_ttc'])}}</td>
                <td class="text-right">{{formatage($produits['quantite_vendu'])}}</td>
                <td class="text-right">{{formatage($produits['ventes_ht'])}}</td>
                <td class="text-right">{{formatage($produits['ventes_ttc'])}}</td>
                <!--
                   <td class="text-right">{{formatage($produits['produits_restants'])}}</td>
                <td class="text-right">{{formatage($produits['produits_restants_ht'])}}</td>
                <td class="text-right">{{formatage($produits['produits_restants_ttc'])}}</td>
                 -->

            </tr>
        @endforeach
        <tr>
            <th class="text-uppercase text-right">Total FCFA</th>
            <td class="text-right">{{formatage($totaux_valeurs['prix_produit_total'])}}</td>
            <td class="text-right">{{formatage($totaux_valeurs['prix_produit_ttc_total'])}}</td>
            <!--
                 <td class="text-right">{{formatage($totaux_valeurs['quantite_produit_total'])}}</td>
            <td class="text-right">{{formatage($totaux_valeurs['stock_ht_total'])}}</td>
            <td class="text-right">{{formatage($totaux_valeurs['stock_ttc_total'])}}</td>
             -->

            <td class="text-right">{{formatage($totaux_valeurs['quantite_commander_total'])}}</td>
            <td class="text-right">{{formatage($totaux_valeurs['commander_ht_total'])}}</td>
            <td class="text-right">{{formatage($totaux_valeurs['commander_ttc_total'])}}</td>
            <td class="text-right">{{formatage($totaux_valeurs['quantite_vendu_total'])}}</td>
            <td class="text-right">{{formatage($totaux_valeurs['ventes_ht_total'])}}</td>
            <td class="text-right">{{formatage($totaux_valeurs['ventes_ttc_total'])}}</td>
            <!--
            <td class="text-right">{{formatage($totaux_valeurs['produits_restants_total'])}}</td>
            <td class="text-right">{{formatage($totaux_valeurs['produits_restants_ht_total'])}}</td>
            <td class="text-right">{{formatage($totaux_valeurs['produits_restants_ttc_total'])}}</td>
             -->

        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
