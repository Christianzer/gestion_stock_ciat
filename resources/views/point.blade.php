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
    <h2 class="text-center text-uppercase text-primary">
        @if($type == 1)
            POINTS LIVRAISONS
        @else
            POINTS FACTURES
        @endif
    </h2>
    <br>
    <table class="table table-bordered" style="font-size: 12px;">
        <thead>
        <tr class="text-left">
            @if($type == 1)
                <th>Code Livraison</th>
            @else
                <th>Code Facture</th>
            @endif

            <th>Clients</th>
            <th>Montant Total HT</th>
            <th>Montant Total TTC</th>
        </tr>
        </thead>
        <tbody>
        @foreach($commandes as $produits)
            <tr>
                @if($type == 1)
                    <td class="text-left">{{$produits->code_commande}}</td>
                @else
                    <td class="text-left">{{$produits->code_facture}}</td>
                @endif

                <td class="text-left">{{$produits->nom}} {{$produits->prenoms}}</td>
                <td class="text-right">{{formatage($produits->montant_total)}}</td>
                <td class="text-right">{{formatage($produits->montant_total_ttc)}}</td>
            </tr>
        @endforeach
        </tbody>

    </table>
</div>
</body>
</html>
