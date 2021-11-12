<template>
    <div class="container-fluid p-3">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <b-breadcrumb>
                <b-breadcrumb-item>Gestion de stock</b-breadcrumb-item>
                <b-breadcrumb-item>Factures</b-breadcrumb-item>
            </b-breadcrumb>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                Listes des commandes
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
                        :items="all_commande"
                        :fields="fields"
                        :filter="filter"
                    >
                        <template v-slot:cell(information)="row">
                            {{row.item}}
                        </template>

                        <template v-slot:cell(actions)="row">
                            <b-button
                                size="sm"
                                variant="outline-primary"
                                @click="imprimer_commande(row.item.code_commande)"
                            >
                                Imprimer le bon de commande
                            </b-button>
                            <b-button
                                size="sm"
                                variant="outline-success"
                                @click="faire_facture(row.item.code_commande)"
                            >
                                Faire la facture
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

    </div>
</template>

<script>
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
            all_commande : [],
            fields : [
                {
                    key:'code_commande',
                    sortable:true,
                },
                {
                    key:'montant_total',
                    sortable:true,
                },
                {
                    key:'information',
                    label:'Clients',
                },
                {
                    key: 'actions'
                },
            ]
        }
    },
    created() {

    },
    methods: {
        async listes(){
            this.Loading = false
            let api_data = 'http://127.0.0.1:8000/api/listes_commandes'
            await this.axios.get(api_data).then(response=>{
                let statut = response.status
                if (statut === 201){
                    this.all_commande = response.data
                }
            }).catch((err) => {
                console.log(err)
            })
            this.Loading = true
        },
        async faire_facture(code_commande) {
            this.$router.push({ name: 'factures_vente', params: { code_commande: code_commande}})
        },
        async imprimer_commande(code_commande){
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
        }
    },

}
</script>
