<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        html {
            font-size: 20px;
        }
        .center_page {
            padding: 350px 0;
        }
table, th, td {
  border: 3px solid black;
  border-radius: 10px;
}
    </style>
</head>
<body>
<div class="center_page">
    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-6">
            <table class="table">

                <tr>
                    <th class="text-uppercase text-left" style="width:40%">DATE</th>
                    <th class="text-uppercase text-left">REFERENCE CLIENT</th>
                </tr>


                <tr>
                    <td class="text-uppercase text-left font-weight-bold">{{$date_jour}}</td>
                    <td class="text-uppercase text-left font-weight-bold">
                        {{$valeur['factures']->nom}} {{$valeur['factures']->prenoms}}<br>
                        @isset($valeur['factures']->compte_contr)
                            {{$valeur['factures']->compte_contr}}<br>
                        @endisset
                        @isset($valeur['factures']->mail)
                            {{$valeur['factures']->mail}}<br>
                        @endisset
                        @isset($valeur['factures']->telephone)
                            {{$valeur['factures']->telephone}} / {{$valeur['factures']->contact}}
                        @endisset

                    </td>
                </tr>

            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table">

                <tr>
                    <th class="text-uppercase font-weight-bold">désignation</th>
                    <th class="text-uppercase font-weight-bold">quantité</th>
                    <th class="text-uppercase font-weight-bold">prix unitaire ht</th>
                    <th class="text-uppercase font-weight-bold">total ht</th>
                </tr>
                  @foreach($valeur['element'] as $produit)
                    <tr>
                        <td class="font-weight-bold text-uppercase">{{$produit->libelle_produit}}</td>
                        <td class="font-weight-bold text-right">{{$produit->quantite_acheter}}</td>
                        <td class="font-weight-bold text-right">{{number_format($produit->prix_vente,'0','.',' ')}} FCFA</td>
                        <td class="font-weight-bold text-right">{{number_format($produit->total_payer,'0','.',' ')}} FCFA</td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="3" class="text-uppercase font-weight-bold text-right">Montant total ht</td>
                    <td  class="text-uppercase font-weight-bold text-right">{{number_format($valeur['factures']->montant_total_factures,'0','.',' ')}} FCFA</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-uppercase font-weight-bold text-right">TVA 18%</td>
                    <td class="text-uppercase font-weight-bold text-right">{{number_format(floor(($valeur['factures']->montant_total_factures*18)/100),'0','.',' ')}} FCFA</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-uppercase font-weight-bold text-right">Montant total TTC</td>
                    <td class="text-uppercase font-weight-bold text-right">{{number_format(floor($valeur['factures']->montant_total_factures_ttc),'0','.',' ')}} FCFA</td>
                </tr>

                @php $i=1; @endphp
                @foreach($versements_data as $versement)
                    <tr>
                        <td colspan="3" class="text-uppercase font-weight-bold text-right">Versement {{$i}}</td>
                        <td class="text-uppercase font-weight-bold text-right">{{number_format(floor($versement->montant_verser),'0','.',' ')}} FCFA</td>
                    </tr>
                    @php $i++;@endphp
                @endforeach

                <tr>
                    <td colspan="3" class="text-uppercase font-weight-bold text-right">Total Versement</td>
                    <td class="text-uppercase font-weight-bold text-right">{{number_format(floor($versement),'0','.',' ')}} FCFA</td>
                </tr>

                <tr>
                    <td colspan="3" class="text-uppercase font-weight-bold text-right">Reste à payer</td>
                    <td class="text-uppercase font-weight-bold text-right">{{number_format(floor($valeur['factures']->montant_total_factures_ttc - $versement),'0','.',' ')}} FCFA</td>
                </tr>



            </table>
        </div>
    </div>
</div>
</body>
</html>
