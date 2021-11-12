<template>
    <div class="container-fluid p-3">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <b-breadcrumb>
                <b-breadcrumb-item>Gestion de stock</b-breadcrumb-item>
                <b-breadcrumb-item href="#bar">Bon de commande</b-breadcrumb-item>
            </b-breadcrumb>

        </div>

        <div class="text-center p-5" v-if="Loading === false">
            <b-spinner variant="primary" style="width: 5rem; height: 5rem;" label="Large Spinner"></b-spinner>
            <b-spinner variant="primary" type="grow" style="width: 5rem; height: 5rem;" label="Spinning"></b-spinner>
        </div>
        <div v-else>
            <div id="essaie">
                <div class="invoice-box">
                    <table>
                        <tr class="top">
                            <td colspan="5">
                                <table>
                                    <tr>
                                        <td class="title">

                                            <img src="http://syge.univ-fhb.edu.ci/donne/logo.bmp" style="width:100%; max-width:150px;">

                                        </td>
                                        <td class="font-weight-bold text-primary">
                                            BON DE COMMANDE N°: {{informationPaiement.code_commande}}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr class="information">
                            <td colspan="5">
                                <table>
                                    <tr>
                                        <td class="font-weight-bold text-primary text-uppercase">
                                            OBF - S.A - Capital 10.000.000 Fcfa<br> 06 BP 1044 Abidjan 06 Côte d'Ivoire<br>  Tél : (225)27.76.300.400 / 07.07.64.64.48
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr class="heading">
                            <td colspan="5" class="text-uppercase text-danger font-weight-bold">Information Client</td>
                        </tr>

                        <tr class="details">
                            <td colspan="3">{{informationPaiement.nom}} {{informationPaiement.prenoms}}</td>
                            <td colspan="2">Téléphone : {{informationPaiement.telephone}}</td>
                        </tr>

                        <tr class="heading">
                            <td>CODE PRODUIT</td>
                            <td>LIBELLE PRODUIT</td>
                            <td>PRIX UNITAIRE</td>
                            <td class="text-uppercase">Quantité</td>
                            <td>PRIX TOTAL</td>
                        </tr>

                        <tr class="item" v-for="articles in articlesPayer" :key="articles.id">
                            <td style="font-size: 16px" class="text-primary font-weight-bold">{{articles.code_produit}}</td>
                            <td style="font-size: 16px" class="text-primary font-weight-bold">{{articles.libelle_produit}}</td>
                            <td style="font-size: 16px" class="text-primary font-weight-bold">{{articles.prix_produit}}</td>
                            <td style="font-size: 16px" class="text-primary font-weight-bold">{{articles.quantite_acheter}}</td>
                            <td style="font-size: 16px" class="text-primary font-weight-bold">{{new Intl.NumberFormat().format(articles.total_payer)}}</td>
                        </tr>
                        <tr class="total">
                            <td colspan="4"></td>
                            <td style="font-size: 16px" class="text-success font-weight-bold text-uppercase">Total : {{new Intl.NumberFormat().format(informationPaiement.montant_total)}} FCFA</td>
                        </tr>
                    </table>
                </div>
            </div>
            <br>
            <div align="right">
                <b-button variant="danger" @click="retour">
                    RETOUR
                </b-button>
                <b-button variant="primary" @click="imprimer(informationPaiement.code_commande)">
                    IMPRIMER LE BON DE COMMANDE
                </b-button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "facture",
    data() {
        return {
            Loading : false,
            articlesPayer : [],
            informationPaiement:[],
            code_article : ''
        }
    },
    computed : {

    },
    methods : {
        async chargerDonne(code_demande){
            this.Loading = false
            let api_data = 'http://127.0.0.1:8000/api/commandes/'+code_commande
            await this.axios.get(api_data).then(response=>{
                let statut = response.status
                if (statut === 201){
                    this.informationPaiement = response.data.factures
                    this.articlesPayer = response.data.element
                }
            }).catch((err) => {
                console.log(err)
            })
            this.Loading = true
        },
        retour(){
            this.$router.push({ name: 'dashboard'})
        },
        async imprimer(code_commande){
            let api_data = 'http://127.0.0.1:8000/api/imprimer_demande/'+code_commande
            await this.axios.get(api_data, {
                responseType: 'blob',
                Accept: 'application/pdf',
            }) .then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data], {type: 'application/pdf'}));
                const link = document.createElement('a');
                console.log(link);
                link.href = url;
                link.setAttribute('download', 'bon_demande_'+code_commande+'.pdf'); //or any other extension
                document.body.appendChild(link);
                link.click();
            }).catch((error) => {
                    console.log(error);
                }).finally(() => {
                    this.loading = false;
                });

        },
    },

    created() {
        this.code_commande = this.$route.params.code_commande
        this.chargerDonne(this.code_commande)
    }
}
</script>

<style scoped lang="scss">

.invoice-box table {
    width: 100%;
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
