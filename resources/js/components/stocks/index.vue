<template>
    <div class="container-fluid p-3">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <b-breadcrumb>
                <b-breadcrumb-item>Gestion de stock</b-breadcrumb-item>
                <b-breadcrumb-item>Stocks</b-breadcrumb-item>
            </b-breadcrumb>
        </div>
        <div class="text-right" >
            <b-button variant="primary" @click="openModal" >Créer un nouveau produit</b-button>
        </div>
        <br>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                Listes des produits
            </div>
            <div class="card-body">
                <template v-if="isLoading === false">
                    <div class="text-center">
                        <b-spinner style="width: 3rem; height: 3rem;" label="Large Spinner"></b-spinner>
                        <b-spinner style="width: 3rem; height: 3rem;" label="Large Spinner" type="grow"></b-spinner>
                    </div>
                </template>
                <template v-else>
                    <b-col md="3" align="right">
                        <b-form-input type="search" id="filterInput" v-model="filter" placeholder="Rechercher....."></b-form-input>
                    </b-col>
                    <br>
                    <b-table
                        head-variant="light"
                        bordered
                        hover
                        responsive="xl"
                        :items="all_produits"
                        :fields="fields"
                        :filter="filter"
                    >

                        <template v-slot:cell(actions)="row">
                            <b-button
                                size="sm"
                                variant="outline-primary"
                                @click="modifier(row.item)"
                            >
                                modifier
                            </b-button>

                            <b-button
                                size="sm"
                                variant="outline-danger"
                                class="mr-1"
                                @click="supprimer(row.item.id_article)"
                            >
                                supprimer
                            </b-button>
                        </template>
                    </b-table>
                    <b-pagination
                        :total-rows="totalRows"
                        :per-page="perPage"
                        v-model="currentPage"
                        class="my-0 pagination-sm"
                    />
                </template>
            </div>

        </div>

        <Form ref="modal"></Form>
    </div>
</template>

<script>
import Form from "./form";
import { mapGetters, mapActions } from 'vuex';
export default {
    name: "index",
    data(){
        return {
            filter :"",
            all_produits : [],
            currentPage: 1,
            isLoading : false,
            perPage: 10,
            totalRows: null,
            selectedCode: null,
            fields : [
                {
                    key:'code_produit',
                    sortable:true,
                },
                {
                    key:'libelle_produit',
                    sortable:true,
                },
                {
                    key:'quantite_produit',
                    label:'Quantite disponible',
                    sortable:true,
                },
                {
                    key:'prix_produit',
                    sortable:true,
                },
                {
                    key: 'actions'
                }
            ]
        }
    },
    components: {
        Form
    },
    created() {
        localStorage.removeItem('produits')
        localStorage.removeItem('clients')
        localStorage.removeItem('articles_prod')
        this.fetchdata()
        Fire.$on('creationok',()=>{
            this.fetchdata()
            //localStorage.removeItem('matricule')
        })
        //localStorage.removeItem('matricule')
    },
    methods: {
        openModal() {
            this.$refs.modal.editMode = false
            this.$refs.modal.showModal()
        },
        async modifier(dataPat) {
            this.$refs.modal.selectedTA = dataPat
            this.$refs.modal.editMode = true
            this.$refs.modal.showModal()
        },
        async fetchdata(){
            this.isLoading = false
            let urlapi = `http://127.0.0.1:8000/api/produits`
            await this.axios.get(urlapi).then((response) => {
                let statut = response.status
                if (statut === 201) {
                   this.all_produits = response.data.element
                }
            }).catch((err) => {
                console.log(err)
            })
            this.isLoading = true
        },
        async supprimer(code) {
            let urlapi = `http://127.0.0.1:8000/api/produits/${code}`
            await this.axios.delete(urlapi).then((response) => {
                let statut = response.status
                if (statut === 201) {
                    this.fetchdata()
                }
            }).catch((err) => {
                console.log(err)
            })
        },
    }
}
</script>

<style scoped>

</style>
