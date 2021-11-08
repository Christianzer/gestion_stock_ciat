<template>
    <b-modal ref="my-modal" :hide-footer="true" :title="title">
        <form @submit.prevent="save">
            <div class="row">
                <div class="col-md-5">
                    <b-form-group
                        label="Code produit"
                    >
                        <b-form-input v-model="formData.code_produit"></b-form-input>
                    </b-form-group>
                </div>
                <div class="col-md-7">
                    <b-form-group
                        label="Libelle Produit">
                        <b-form-input v-model="formData.libelle_produit"></b-form-input>
                    </b-form-group>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <b-form-group
                        label="Quantite produit"
                    >
                        <b-form-input type="number" min="0" v-model="formData.quantite_produit"></b-form-input>
                    </b-form-group>
                </div>
                <div class="col-md-6">
                    <b-form-group
                        label="Prix produit">
                        <b-form-input type="number" v-model="formData.prix_produit"></b-form-input>
                    </b-form-group>
                </div>
            </div>
            <div class="row justify-content-end">
                <b-button variant="primary mr-1" type="submit">enregistrer</b-button>
                <b-button variant="danger mr-1" @click="closeModal">fermer</b-button>
            </div>
        </form>
    </b-modal>

</template>

<script>
export default {
    name: "form",
    props: {
        selectedTA: {},
        editMode: Boolean
    },
    data() {
        return {
            apidata : 'http://127.0.0.1:8000/api/produits',
            selected : null,
            title:"Mise à jour produits",
            formData: {
                id:null,
                code_produit: "",
                libelle_produit: "",
                quantite_produit:0,
                prix_produit:0
            }
        }
    },
    methods: {
        showModal() {
            if (this.editMode === true) {
                this.selected = this.selectedTA.id_article
                this.formData.code_produit = this.selectedTA.code_produit
                this.formData.libelle_produit = this.selectedTA.libelle_produit
                this.formData.quantite_produit = this.selectedTA.quantite_produit
                this.formData.prix_produit = this.selectedTA.prix_produit
            } else {
                this.formData.code_produit = ''
                this.formData.libelle_produit = ''
                this.formData.quantite_produit = 0
                this.formData.prix_produit = 0
            }
            this.$refs['my-modal'].show()
        },
        closeModal() {
            this.$refs['my-modal'].hide()
        },
        async save(){
            var data = {
                code_produit : this.formData.code_produit,
                libelle_produit : this.formData.libelle_produit,
                quantite_produit : parseInt(this.formData.quantite_produit),
                prix_produit : parseFloat(this.formData.prix_produit)
            }
            if (this.editMode === true){
                await this.axios.put(this.apidata+'/'+this.selected,data)
                    .then(response=>{
                        let statut = response.status
                        if (statut === 201){
                            Fire.$emit('creationok');
                            this.closeModal()//custom events
                        }
                    }).catch((err) => {
                        console.log(err)
                    })
            }else {
                await this.axios.post(this.apidata,data)
                    .then(response=>{
                        let statut = response.status
                        if (statut === 201){
                            Fire.$emit('creationok');
                            this.closeModal()//custom events
                        }
                    }).catch((err) => {
                        console.log(err)
                    })

            }
        }
    }


}
</script>

<style scoped>

</style>
