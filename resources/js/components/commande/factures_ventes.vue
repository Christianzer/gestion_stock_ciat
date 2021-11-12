<template>
    <div class="container-fluid p-3">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <b-breadcrumb>
                <b-breadcrumb-item>Gestion de stock</b-breadcrumb-item>
                <b-breadcrumb-item href="#bar">Etablir facture</b-breadcrumb-item>
            </b-breadcrumb>
        </div>
        <template v-if="isLoading === false">
            <div class="text-center">
                <b-spinner style="width: 3rem; height: 3rem;" label="Large Spinner"></b-spinner>
                <b-spinner style="width: 3rem; height: 3rem;" label="Large Spinner" type="grow"></b-spinner>
            </div>
        </template>
        <template v-else>
            <div class="card shadow mb-4">
                <div class="card-header py-3 text-uppercase font-weight-bold">
                    Produit
                </div>
                <div class="card-body">
                    <table class="tftable table-responsive" border="1">
                        <tr>
                            <th>Code Produit</th>
                            <th>Libelle Produit</th>
                            <th>Quantité demandé</th>
                            <th>Prix unitaire</th>
                            <th>Total</th>
                        </tr>
                        <tr v-for="selected in produit" :key="selected.id_commandes">
                            <td style="width: 8%"><h6>{{selected.code_produit}}</h6></td>
                            <td style="width: 15%;"><h6>{{selected.libelle_produit}}</h6></td>
                            <td style="width: 5%"><h5 class="text-center">{{selected.quantite_acheter}}</h5></td>
                            <td style="width: 15%"><h5 class="text-center">{{new Intl.NumberFormat().format(selected.prix_produit)}}</h5></td>
                            <td style="width: 20%"><h4 class="text-primary">{{new Intl.NumberFormat().format(selected.total_payer)}} FCFA</h4></td>
                        </tr>
                    </table>
                    <br>
                    <div class="text-right">
                        <h2 class="text-uppercase text-right text-danger font-weight-bolder" v-if="produit.length > 0"> TOTAL : {{ new Intl.NumberFormat().format(total) }} FCFA</h2>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 text-uppercase font-weight-bold">
                    Facture
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <b-form-input :value="information" disabled></b-form-input>
                        </div>
                        <div class="col-md-4">
                            <b-form-input disabled :value="telephone"></b-form-input>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4">
                            <b-form-group
                                label-cols-sm="4"
                                label-cols-lg="5"
                                content-cols-sm
                                content-cols-lg="7"
                                label="Montant à payer"
                                label-for="input-horizontal"
                            >
                                <b-form-input v-model="total" disabled></b-form-input>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group
                                label-cols-sm="4"
                                label-cols-lg="5"
                                content-cols-sm
                                content-cols-lg="7"
                                label="Montant versé"
                                label-for="input-horizontal"
                            >
                                <b-form-input type="number" v-model="montant_deposer" min="0"></b-form-input>
                            </b-form-group>
                        </div>
                        <div class="col-md-4">
                            <b-form-group
                                label-cols-sm="4"
                                label-cols-lg="5"
                                content-cols-sm
                                content-cols-lg="7"
                                label="Montant à rendre"
                                label-for="input-horizontal"
                            >
                                <b-form-input type="number" v-model="monnaie" disabled></b-form-input>
                            </b-form-group>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <b-button variant="danger mr-3" type="button" disabled v-if="montant_deposer < total">Payer {{new Intl.NumberFormat().format(montant_deposer)}} FCFA</b-button>
                        <b-button variant="primary mr-3" type="button" v-else @click="Loading = true,achat()">Payer {{new Intl.NumberFormat().format(montant_deposer)}} FCFA</b-button>
                    </div>
                </div>
            </div>
        </template>
        <b-overlay :show="Loading" no-wrap>
        </b-overlay>

    </div>
</template>

<script>

export default {
    name: "panier",
    data() {
        return {
            Loading : false,
            isLoading:false,
            produit : [],
            id_clients:"",
            total: 0,
            created : "",
            telephone: "",
            information : "",
            code_commande:"",
            montant_deposer:0,
            afficherPaiement : false,
        }
    },

    methods: {
        async chargerData(code_demande){
            this.isLoading = false
            let api_data = 'http://127.0.0.1:8000/api/remplir_facture/'+code_demande
            await this.axios.get(api_data).then(response=>{
                let statut = response.status
                if (statut === 201){
                    this.produit = response.data.element
                    this.information =  response.data.factures.nom+' '+response.data.factures.prenoms
                    this.id_clients = response.data.factures.matricule_clients
                    this.telephone = response.data.factures.telephone
                    this.total = response.data.factures.montant_total
                }
            }).catch((err) => {
                console.log(err)
            })
            this.isLoading = true
        },
        async achat(){
            this. Loading = false
            var data = {
                produits : this.produit,
                montant_total : this.total,
                somme_verse:this.montant_deposer,
                somme_rendu:this.monnaie,
                clients:this.id_clients,
                code_commande:this.code_commande,
            }
            let api = 'http://127.0.0.1:8000/api/ventes'
            await this.axios.post(api,data).then(response=>{
                let statut = response.status
                if (statut === 201){
                    let code_facture = response.data
                    this.$router.push({ name: 'facture', params: { code_facture: code_facture}})
                }
            }).catch((err) => {
                console.log(err)
            })
            this. Loading = true
        },


    },
    computed : {

        monnaie(){
            let val = 0;
            val = this.total - this.montant_deposer
            return Math.abs(val)
        },
    },
    created() {
        localStorage.removeItem('produits')
        localStorage.removeItem('clients')
        localStorage.removeItem('articles_prod')
        this.code_commande = this.$route.params.code_commande
        this.chargerData(this.code_commande)
    }
}
</script>

<style scoped>
.tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
.tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
.tftable tr {background-color:#ffffff;}
.tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
</style>
