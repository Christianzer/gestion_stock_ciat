<template>
    <div class="container-fluid p-3">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <b-breadcrumb>
                <b-breadcrumb-item>Gestion de stock</b-breadcrumb-item>
                <b-breadcrumb-item>Ventes</b-breadcrumb-item>
            </b-breadcrumb>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                Listes des clients
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
                        responsive="xl"
                        :items="all_clients"
                        :fields="fields"
                        :filter="filter"
                    >

                        <template v-slot:cell(actions)="row">
                            <b-button
                                size="sm"
                                variant="outline-primary"
                                @click="bon_commande(row.item)"
                            >
                                Faire une commande
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

        <Form ref="modal"></Form>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
export default {
    name: "index",
    data(){
        return {
            filter :"",
            currentPage: 1,
            isLoading : false,
            perPage: 10,
            totalRows: null,
            selectedCode: null,
            fields : [
                {
                    key:'matricule',
                    sortable:true,
                },
                {
                    key:'nom',
                    sortable:true,
                },
                {
                    key:'prenoms',
                    sortable:true,
                },
                {
                    key:'telephone',
                    sortable:true,
                },
                {
                    key: 'actions'
                },
            ]
        }
    },
    created() {
        localStorage.removeItem('produits')
        localStorage.removeItem('clients')
        localStorage.removeItem('articles_prod')
        this.fetchclients()

    },
    methods: {
        ...mapActions(["fetchclients"]),
        async bon_commande(dataPat) {
            localStorage.setItem('clients',JSON.stringify(dataPat))
            this.$router.push({name:'ventes_users'})
        }
    },
    computed : mapGetters(["all_clients"]),
}
</script>
