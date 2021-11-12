<template>
    <div class="container-fluid p-3">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <b-breadcrumb>
                <b-breadcrumb-item>Gestion de stock</b-breadcrumb-item>
                <b-breadcrumb-item>Tableau de bord</b-breadcrumb-item>
            </b-breadcrumb>
        </div>

        <div class="row">
            <template v-if="isLoading === false">
                <div class="col-md-12">
                    <div class="text-center">
                        <b-spinner style="width: 3rem; height: 3rem;" label="Large Spinner"></b-spinner>
                        <b-spinner style="width: 3rem; height: 3rem;" label="Large Spinner" type="grow"></b-spinner>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success-perso shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs-perso font-weight-bold text-primary text-uppercase mb-1">NOMBRE DE PRODUIT</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{total_produits}}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-comments fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success-perso shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs-perso font-weight-bold text-primary text-uppercase mb-1">NOMBRE DE CLIENT</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{total_clients}}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-comments fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </template>
        </div>
    </div>
</template>

<script>
export default {
    name: "index",
    data(){
        return {
            isLoading : false,
            total_clients : 0,
            total_produits : 0,
        }
    },
    methods:{
        async fetchdata(){
            this.isLoading = false
            let api_data = 'http://127.0.0.1:8000/api/dash'
            await this.axios.get(api_data).then((response) => {
                let statut = response.status
                if (statut === 201) {
                    let donne = response.data
                    this.total_clients = donne[0]['nbre_clients']
                    this.total_produits = donne[1]['id_produit']
                }
            }).catch((err) => {
                console.log(err)
            })
            this.isLoading = true
        }
    },
    created() {
        localStorage.removeItem('produits')
        localStorage.removeItem('clients')
        localStorage.removeItem('articles_prod')
        this.fetchdata()
    }
}
</script>

<style scoped>
.border-left-success-perso {
    border-left: 0.50rem solid #1cc88a !important;
}
.text-xs-perso {
    font-size: .8rem;
}

</style>
