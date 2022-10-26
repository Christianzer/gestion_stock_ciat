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
<div class="invoice-box">
    <table>
        <tr class="top">
            <td colspan="3">
                <table>
                    <tr>
                        <td class="title">

                            <img src="http://gstockob-api.sodenci.com/logo_obf.png" style="width:100%; max-width:150px;">

                        </td>
                        <td class="font-weight-bold text-uppercase text-primary">
                            N° réçu: {{$info['code_versement']}}
                        </td>
                        <td class="font-weight-bold text-primary text-uppercase">
                            DATE réçu : {{$date_jour}}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="3">
                <table>
                    <tr>
                        <td class="font-weight-bold text-primary text-uppercase">
                            OBF - S.A - Capital 10.000.000 Fcfa<br> 06 BP 1044 Abidjan 06 Côte d'Ivoire<br>  Tél : (225)27.24.325.178 / 07.07.64.64.48 / 05.76.30.04.00
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="height: 50px"></tr>

        <tr class="heading">
            <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">CLIENTS </td>
            <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
            <td class="text-uppercase font-weight-bold text-dark text-left">{{$info['clients']}}</td>
        </tr>

        @if(isset($info['telephone']))
            <tr class="heading">
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">CONTACT 1 </td>
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
                <td class="text-uppercase font-weight-bold text-dark text-left">{{$info['telephone']}}</td>
            </tr>
        @endif
        @if(isset($info['contact']))
            <tr class="heading">
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">CONTACT 2</td>
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
                <td class="text-uppercase font-weight-bold text-dark text-left">{{$info['contact']}}</td>
            </tr>
        @endif
        @if(isset($info['factures']))
            <tr class="heading">
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">FACTURE</td>
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
                <td class="text-uppercase font-weight-bold text-dark text-left">{{$info['factures']}}</td>
            </tr>
        @endif

        @if($info['type_paiement'] == 1)
            <tr class="heading">
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">MOYEN DE PAIEMENT</td>
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
                <td class="text-uppercase font-weight-bold text-dark text-left">ESPèce</td>
            </tr>
        @endif
        @if($info['type_paiement'] == 2)
            <tr class="heading">
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">MOYEN DE PAIEMENT</td>
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
                <td class="text-uppercase font-weight-bold text-dark text-left">Chèque</td>
            </tr>
        @endif
        @if($info['type_paiement'] == 3)
            <tr class="heading">
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">MOYEN DE PAIEMENT</td>
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
                <td class="text-uppercase font-weight-bold text-dark text-left">mobile monney</td>
            </tr>
        @endif
        @if(isset($info['a_payer']))
            <tr class="heading">
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">à PAYER</td>
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
                <td class="text-uppercase font-weight-bold text-dark text-left">{{number_format($info['a_payer'],"0",","," ")}} FCFA</td>
            </tr>
        @endif
        @if(isset($info['montant_verser']))
            <tr class="heading">
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">payé</td>
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
                <td class="text-uppercase font-weight-bold text-dark text-left">{{number_format($info['montant_verser'],"0",","," ")}} FCFA</td>
            </tr>
        @endif
        @if(isset($info['reste_payer']))
            <tr class="heading">
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">rESTE à PAYé</td>
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
                <td class="text-uppercase font-weight-bold text-dark text-left">{{number_format($info['reste_payer'],"0",","," ")}} FCFA</td>
            </tr>
        @endif
        @if(!is_null($info['monnaie']))
            <tr class="heading">
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">MONNAIE </td>
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
                <td class="text-uppercase font-weight-bold text-dark text-left">{{$info['monnaie']}}</td>
            </tr>
        @endif
        @if(!is_null($info['paiement']->banque))
            <tr class="heading">
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">BANQUE </td>
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
                <td class="text-uppercase font-weight-bold text-dark text-left">{{$info['paiement']->banque}}</td>
            </tr>
        @endif
        @if(!is_null($info['paiement']->reseau))
            <tr class="heading">
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">OPERATEUR </td>
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
                <td class="text-uppercase font-weight-bold text-dark text-left">
                    @if($info['paiement']->reseau == 1)
                        ORANGE
                    @elseif($info['paiement']->reseau == 2)
                        MOOV
                    @elseif($info['paiement']->reseau == 3)
                        MTN
                    @endif
                </td>
            </tr>
        @endif
        @if(!is_null($info['paiement']->numero_telephone))
            <tr class="heading">
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">nUMéro téléphone </td>
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
                <td class="text-uppercase font-weight-bold text-dark text-left">{{$info['paiement']->numero_telephone}}</td>
            </tr>
        @endif
        @if(!is_null($info['paiement']->numero_cheque))
            <tr class="heading">
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 20%">nUMéro CHèque </td>
                <td class="text-uppercase font-weight-bold text-danger text-left" style="width: 1%">:</td>
                <td class="text-uppercase font-weight-bold text-dark text-left">{{$info['paiement']->numero_cheque}}</td>
            </tr>
        @endif



    </table>
</div>
<footer>
    OBF - S.A - Capital 10.000.000 Fcfa - Abidjan-Cocody-Bonoumin, imm. CIAT - RC : CI-ABJ-03-2021-B16-00027
    , CC : 2109932-Y
    Regime :  Réel Simplifié, 06 BP 1044 Abidjan 06 Côte d'Ivoire - Tél : (225)27.24.325.178 / 05.76.300.400 / 07.07.646.448
</footer>
</body>
</html>
