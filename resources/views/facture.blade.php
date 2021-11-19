<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        footer {
            color: #777777;
            left: 0;
            right: 0;
            position: absolute;
            bottom: 0;
            text-transform: uppercase;
        }

        .invoice-box table {
            width: 100%;
        }

        .center_page {
            padding: 200px 0;
        }

        .invoice-box table tr td:nth-child(n + 2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 10px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 10px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 10px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.item input {
            padding-left: 5px;
        }

        .invoice-box table tr.item td:first-child input {
            margin-left: -5px;
            width: 100%;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .invoice-box input[type="number"] {
            width: 60px;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: Tahoma, "Helvetica Neue", "Helvetica", Helvetica, Arial,
            sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }

    </style>
</head>
<body>
<div class="invoice-box center_page">
    <table>
        <tr>
            <td colspan="5" class="font-weight-bold text-primary"> FACTURE N°: {{$valeur['factures']->code_facture}}</td>
        </tr>
        <tr>
            <td colspan="5" class="font-weight-bold text-primary"> DATE FACTURE : {{$date_jour}}</td>
        </tr>
        <tr style="height: 20px"></tr>
        <tr class="heading">
            <td colspan="5" class="text-uppercase text-danger font-weight-bold">Information Client</td>
        </tr>

        <tr class="details font-weight-bold">
            <td class="text-uppercase">{{$valeur['factures']->nom}} {{$valeur['factures']->prenoms}}</td>
            <td>CC: {{$valeur['factures']->compte_contr}}</td>
            <td>Mail: {{$valeur['factures']->mail}}</td>
            <td>Contact 1: {{$valeur['factures']->telephone}}</td>
            <td>Contact 2: {{$valeur['factures']->contact}}</td>
        </tr>

        <tr class="heading">
            <td>CODE PRODUIT</td>
            <td>LIBELLE PRODUIT</td>
            <td>PRIX UNITAIRE</td>
            <td class="text-uppercase">Quantité</td>
            <td>PRIX TOTAL</td>
        </tr>
        @foreach($valeur['element'] as $produit)
            <tr class="item">
                <td style="font-size: 16px" class="text-primary font-weight-bold">{{$produit->code_produit}}</td>
                <td style="font-size: 16px" class="text-primary font-weight-bold">{{$produit->libelle_produit}}</td>
                <td style="font-size: 16px" class="text-primary font-weight-bold">{{number_format($produit->prix_produit,'0','.',' ')}} FCFA</td>
                <td style="font-size: 16px" class="text-primary font-weight-bold">{{$produit->quantite_acheter}}</td>
                <td style="font-size: 16px" class="text-primary font-weight-bold">{{number_format($produit->total_payer,'0','.',' ')}} FCFA</td>
            </tr>
        @endforeach
        <tr class="total">
            <td colspan="4"></td>
            <td style="font-size: 16px" class="text-success font-weight-bold text-uppercase">Total : {{number_format($valeur['factures']->montant_total,'0','.',' ')}} FCFA</td>
        </tr>
        <tr class="total">
            <td colspan="4"></td>
            <td style="font-size: 16px" class="text-primary font-weight-bold text-uppercase">Verser : {{number_format($valeur['factures']->montant_verser,'0','.',' ')}} FCFA</td>
        </tr>
        <tr class="total">
            <td colspan="4"></td>
            <td style="font-size: 16px" class="text-danger font-weight-bold text-uppercase">Reste à verser : {{number_format($valeur['factures']->montant_rendu,'0','.',' ')}} FCFA</td>
        </tr>
    </table>
</div>
</body>
</html>
