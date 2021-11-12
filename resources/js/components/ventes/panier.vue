<template>
    <div class="container-fluid p-3">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <b-breadcrumb>
                <b-breadcrumb-item>Gestion de stock</b-breadcrumb-item>
                <b-breadcrumb-item href="#bar">Bon de commande</b-breadcrumb-item>
            </b-breadcrumb>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3 text-uppercase font-weight-bold">
                Produit
            </div>
            <div class="card-body">
                <table class="tftable table-responsive" border="1">
                    <tr>
                        <th>Code Produit</th>
                        <th>Libelle Produit</th>
                        <th>Quantité disponible</th>
                        <th>Prix</th>
                        <th>Quantité achété</th>
                        <th>Total</th>
                    </tr>
                    <tr v-for="selected in produit" :key="selected.id_article">
                        <td style="width: 8%"><h6>{{selected.code_produit}}</h6></td>
                        <td style="width: 15%;"><h6>{{selected.libelle_produit}}</h6></td>
                        <td style="width: 5%"><h5 class="text-center">{{selected.quantite_produit - selected.quantite_acheter}}</h5></td>
                        <td style="width: 15%"><h5 class="text-center">{{new Intl.NumberFormat().format(selected.prix_produit)}}</h5></td>
                        <td class="text-center" width="29%">
                            <!-- <input type="number" min="1" :max="selected.quantite_produit"  v-model="selected.quantite_acheter">
                             -->
                            <b-row>
                                <b-col cols="3" class="text-center">
                                    <b-button variant="outline-danger" @click="diminuer(selected.id_article)" :disabled="selected.quantite_acheter <= 1">
                                        -
                                    </b-button>
                                </b-col>
                                <b-col cols="6" class="text-center">
                                    <h4>{{selected.quantite_acheter}}</h4>
                                </b-col>
                                <b-col cols="3" class="text-center">
                                    <b-button variant="outline-primary" @click="augmenter(selected.id_article)" :disabled="selected.quantite_disponible === 1">
                                        +
                                    </b-button>
                                </b-col>
                            </b-row>
                            <!--
                             <b-row>
                            <b-col cols="3" class="text-center">
                              <b-button variant="outline-danger" @click="diminuer(selected.id_article)" :disabled="selected.quantite_acheter <= 1">
                                -
                              </b-button>
                            </b-col>
                            <b-col cols="6" class="text-center">
                              <h4>{{selected.quantite_acheter}}</h4>
                            </b-col>
                            <b-col cols="3" class="text-center">
                              <b-button variant="outline-primary" @click="augmenter(selected.id_article)" :disabled="selected.quantite_disponible === 1">
                                +
                              </b-button>
                            </b-col>
                          </b-row>
                             -->
                        </td>
                        <td style="width: 20%"><h4 class="text-primary">{{new Intl.NumberFormat().format(selected.quantite_acheter * selected.prix_produit)}} FCFA</h4></td>
                        <!--

                        <td style="width: 8%"><button class="btn btn-danger" @click="supprimerProduits(selected.id_article)"><b-icon icon="trash-fill" aria-hidden="true"></b-icon>
                        </button></td>
                         -->
                    </tr>
                </table>
                <br>
                <div class="text-right">
                    <h2 class="text-uppercase text-right text-danger font-weight-bolder" v-if="produit.length > 0"> TOTAL : {{ new Intl.NumberFormat().format(total) }} FCFA</h2>
                </div>
                <b-row class="mt-4 text-left">
                    <b-col>
                        <b-button variant="success" class="mr-1" @click="" v-if="total > 0">Valider le bon de commande</b-button>
                    </b-col>
                    <b-col></b-col>
                    <!--
                      <b-col cols="3"></b-col>
                    <b-col class="text-right">
                        <b-button variant="danger" @click="vider" class="mr-1">Vider le panier</b-button>
                    </b-col>
                     -->

                </b-row>
            </div>
        </div>
        <!-- Facture -->
        <!--
          <div class="card shadow mb-4" v-if="afficherPaiement === true">
            <div class="card-header py-3 text-uppercase font-weight-bold">
               Bon de commande
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <b-form-input :value="information" disabled></b-form-input>
                    </div>
                    <div class="col-md-4">
                        <b-form-input disabled :value="data_clients.telephone"></b-form-input>
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
         -->
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
            save:false,
            next:false,
            prec : false,
            tabIndex: 0,
            produit : [],
            created : "",
            information : "",
            data_clients : JSON.parse(localStorage.getItem('clients')),
            montant_deposer:0,
            afficherPaiement : false,
        }
    },

    methods: {
        supprimerProduits(id){
            console.log(id)
            for (let index = 0;index < this.produit.length;index++){
                if (this.produit[index].id_article === id){
                    this.produit.splice(index,1);
                    localStorage.setItem('produits',JSON.stringify(this.produit))
                }
            }
        },
        async achat(){
            var data = {
                produits : this.produit,
                montant_total : this.total,
                somme_verse:this.montant_deposer,
                somme_rendu:this.monnaie,
                clients:this.data_clients.id
            }
            let api = 'http://127.0.0.1:8000/api/ventes'
            await this.axios.post(api,data).then(response=>{
                let statut = response.status
                if (statut === 201){
                    localStorage.removeItem('produits')
                    localStorage.removeItem('clients')
                    localStorage.removeItem('articles_prod')
                    let code_facture = response.data
                    this.$router.push({ name: 'facture', params: { code_facture: code_facture}})
                }
            }).catch((err) => {
                console.log(err)
            })

        },
        augmenter(id){
            for (let index = 0;index < this.produit.length;index++) {
                if (this.produit[index].id_article === id) {
                    this.produit[index].quantite_acheter++
                    this.produit[index].quantite_disponible--
                    localStorage.setItem('produits',JSON.stringify(this.produit))
                }
            }
        },
        diminuer(id){
            for (let index = 0;index < this.produit.length;index++) {
                if (this.produit[index].id_article === id) {
                    this.produit[index].quantite_acheter--
                    this.produit[index].quantite_disponible++
                    localStorage.setItem('produits',JSON.stringify(this.produit))
                }
            }
        },
        vider(){
            this.afficherPaiement = false
            this.produit = []
            for (const key in this.produit){
                this.produit[key].consulter = false
            }
            localStorage.setItem('produits',JSON.stringify(this.produit))
        },
        async inserercommande(){
            var data = {
                produits : this.produit,
                montant_total : this.total,
                clients:this.data_clients.id
            }
            let api = 'http://127.0.0.1:8000/api/commandes'
            await this.axios.post(api,data).then(response=>{
                let statut = response.status
                if (statut === 201){
                    localStorage.removeItem('produits')
                    localStorage.removeItem('clients')
                    localStorage.removeItem('articles_prod')
                    //let code_facture = response.data
                    //this.$router.push({ name: 'facture', params: { code_facture: code_facture}})
                    let code_commandes = response.data
                    this.$router.push({ name: 'commande', params: { code_commande: code_commandes}})
                }
            }).catch((err) => {
                console.log(err)
            })

        },

    },
    computed : {
        total(){
            let t = 0;
            for (let index = 0; index < this.produit.length ; index ++){
                t += this.produit[index].prix_produit * this.produit[index].quantite_acheter
            }
            return t
        },
        monnaie(){
            let val = 0;
            val = this.total - this.montant_deposer
            return Math.abs(val)
        },
    },
    created() {
        if (JSON.parse(localStorage.getItem('produits'))){
            this.produit = JSON.parse(localStorage.getItem('produits'))
        }else {
            this.produit = []
        }
        this.information = this.data_clients.nom+" "+this.data_clients.prenoms
    }
}
</script>

<style scoped>
.tftable {font-size:12px;color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;}
.tftable th {font-size:12px;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
.tftable tr {background-color:#ffffff;}
.tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;}
</style>
