<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="http://syge.univ-fhb.edu.ci/donne/style_simple.css" media="all" />
</head>
<body>
<header class="clearfix">
    <div id="logo">
        <img src="http://syge.univ-fhb.edu.ci/donne/logo.bmp">
    </div>
</header>
<main>
    <div id="details" class="clearfix">
        <div id="client">
            <div class="name">CLIENT : {{$valeur['factures']->nom}} {{$valeur['factures']->prenoms}}</div>
            <div class="name">CONTACT :{{$valeur['factures']->telephone}}</div>
        </div>
        <div id="invoice">
            <div class="name">BON DE LIVRAISON N° {{$valeur['factures']->code_commande}}</div>
            <div class="name">DATE LIVRAISON : {{$date_jour}}</div>
        </div>
    </div>
    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="total">CODE ARTICLE</th>
            <th class="desc">LIBELLE ARTICLE</th>
            <th class="unit">PRIX UNITAIRE</th>
            <th class="qty" style="text-transform: uppercase;">Quantitée</th>
            <th class="total">TOTAL</th>
        </tr>
        </thead>
        <tbody>
        @foreach($valeur['element'] as $produit)
            <tr>
                <td class="total">{{$produit->code_produit}}</td>
                <td class="desc">{{$produit->libelle_produit}}</td>
                <td class="unit">{{number_format($produit->prix_produit,'0','.',' ')}} FCFA</td>
                <td class="qty">{{$produit->quantite_acheter}}</td>
                <td class="total">{{number_format($produit->total_payer,'0','.',' ')}} FCFA</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2"></td>
            <td colspan="2">TOTAL </td>
            <td>{{number_format($valeur['factures']->montant_total,'0','.',' ')}} FCFA</td>
        </tr>
        </tfoot>
    </table>
    <div id="thanks">Merci !</div>
</main>
<footer>
    OBF - S.A - Capital 10.000.000 Fcfa - Abidjan-Cocody-Bonoumin, imm. CIAT - RC : CI-ABJ-03-2021-B14-00027
    , CC : 2109932-Y
    Regime :  Réel Simplifié, 06 BP 1044 Abidjan 06 Côte d'Ivoire - Tél : (225)27.76.300.400 / 07.07.64.64.48
</footer>
</body>
</html>
