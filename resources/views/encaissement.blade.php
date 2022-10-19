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
        <tr class="text-left">
            <th>Date versement</th>
            <th>Code versement</th>
            <th>Code facture</th>
            <th>Client</th>
            <th>Moyen Paiement</th>
            <th>Montant Encaissé FCFA</th>
        </tr>
        </thead>
        <tbody>
        @foreach($information as $produits)
            <tr>
                <td class="text-left">{{date("d-m-Y", strtotime($produits->date_versement))}}</td>
                <td class="text-left">{{$produits->code_versement}}</td>
                <td class="text-left">{{$produits->code_facture}}</td>
                <td class="text-left">{{$produits->nom}} {{$produits->prenoms}}</td>
                <td class="text-left">
                    @if($produits->type_paiement == 1)
                        Espèce
                    @elseif($produits->type_paiement == 2)
                        Chèque
                    @elseif($produits->type_paiement == 3)
                        Mobile Monney
                    @endif
                </td>
                <td class="text-right">{{formatage($produits->montant_verser)}}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th class="text-uppercase text-right" colspan="5">Total FCFA</th>
            <td class="text-right">{{formatage($total)}}</td>
        </tr>
        </tfoot>


    </table>
</div>
</body>
</html>
