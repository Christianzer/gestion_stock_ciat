<template>
    <div class="container-fluid p-3">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <b-breadcrumb>
                <b-breadcrumb-item>Gestion de stock</b-breadcrumb-item>
                <b-breadcrumb-item>Commandes</b-breadcrumb-item>
            </b-breadcrumb>
            <b-button variant="danger" :to="{name : 'panier'}">
                VOIR LA COMMANDE
            </b-button>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3 text-uppercase font-weight-bold">
                Produits
            </div>
            <div class="card-body">
                <b-col md="3" align="right">
                    <b-form-input type="search" id="filterInput" v-model="filter" placeholder="Rechercher....."></b-form-input>
                </b-col>
                <br>
                <b-table
                    head-variant="light"
                    bordered
                    hover
                    :items="all_produits"
                    :fields="fields"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                >
                    <template v-slot:cell(consulter)="row">
                        <b-button
                            size="sm"
                            variant="primary"
                            class="mr-1"
                            v-if="!row.item.consulter && row.item.quantite_produit > 0"
                            :disabled="row.item.consulter"
                            @click="row.item.consulter = true, ajouterPanier(JSON.parse(JSON.stringify(row.item)))"
                        >
                            Ajouter au bon de commande
                        </b-button>
                        <b-button
                            size="sm"
                            variant="warning"
                            class="mr-1"
                            v-if="row.item.consulter"
                            :disabled="row.item.consulter"
                        >
                            Deja Ajouté au bon de commande
                        </b-button>
                        <b-button
                            size="sm"
                            variant="danger"
                            class="mr-1"
                            v-if="row.item.quantite_produit === 0"
                            :disabled="row.item.quantite_produit === 0"
                        >
                            Stock Epuisé
                        </b-button>
                    </template>
                </b-table>
                <b-pagination
                    :total-rows="totalRows"
                    :per-page="perPage"
                    v-model="currentPage"
                    class="my-0 pagination-sm"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
export default {
    name: "index",
    data() {
        return {
            nbre_article:0,
            isLoading: false,
            currentPage: 1,
            perPage: 10,
            totalRows: null,
            selectedCode: null,
            filter :"",
            selected : false,
            data_article : JSON.parse(localStorage.getItem('articles_prod')),
            fields: [
                {
                    key: 'code_produit',
                    sortable: true
                },
                {
                    key: 'libelle_produit',
                    sortable: true
                },
                {
                    key: 'quantite_produit',
                    label:'Quantite Disponible',
                    sortable: true
                },
                {
                    key: 'prix_produit',
                    sortable: true
                },
                {
                    key: 'consulter',
                    label: 'Action',
                    sortable: true
                }
            ]
        }
    },
    methods: {
        ...mapActions(["fetchProduits"]),
        async ajouterPanier(dataArticle){
            let article = []
            if (JSON.parse(localStorage.getItem('produits'))){
                article = JSON.parse(localStorage.getItem('produits'))
            }else {
                article = []
            }
            article.push(dataArticle)
            localStorage.setItem('produits',JSON.stringify(article))
        }
    },
    created() {
        this.fetchProduits()
        this.totalRows = this.all_produits.length
        var fruits = []

        if (JSON.parse(localStorage.getItem('produits'))){
            fruits = JSON.parse(localStorage.getItem('produits'))
        }else {
            fruits = []
        }

        for (let index = 0;index < fruits.length;index++){
            for (let index2 = 0;index2 < this.data_article.length;index2++){
                if (fruits[index].id_article === this.data_article[index2].id_article){
                    this.data_article[index2].consulter = true
                }
            }
        }
    },
    computed : mapGetters(["all_produits"])
}
</script>

<style scoped>

</style>
