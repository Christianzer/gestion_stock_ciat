<template>
    <b-modal ref="my-modal" :hide-footer="true" :title="title">
        <form @submit.prevent="save">
            <div class="row">
                <div class="col-md-5">
                    <b-form-group
                        label="Matricule"
                    >
                        <b-form-input v-model="formData.matricule"></b-form-input>
                    </b-form-group>
                </div>
                <div class="col-md-7">
                    <b-form-group
                        label="Nom">
                        <b-form-input v-model="formData.nom"></b-form-input>
                    </b-form-group>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <b-form-group
                        label="Prénom"
                    >
                        <b-form-input v-model="formData.prenoms"></b-form-input>
                    </b-form-group>
                </div>
                <div class="col-md-5">
                    <b-form-group
                        label="Téléphone">
                        <b-form-input v-model="formData.telephone"></b-form-input>
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
            apidata : 'http://127.0.0.1:8000/api/clients',
            selected : null,
            title:"Mise à jour clients",
            formData: {
                id:null,
                matricule: "",
                nom: "",
                prenoms:'',
                telephone:''
            }
        }
    },
    methods: {
        showModal() {
            if (this.editMode === true) {
                this.selected = this.selectedTA.id
                this.formData.matricule = this.selectedTA.matricule
                this.formData.nom = this.selectedTA.nom
                this.formData.prenoms = this.selectedTA.prenoms
                this.formData.telephone = this.selectedTA.telephone
            } else {
                this.formData.matricule = ''
                this.formData.nom = ''
                this.formData.prenoms = ''
                this.formData.telephone = ''
            }
            this.$refs['my-modal'].show()
        },
        closeModal() {
            this.$refs['my-modal'].hide()
        },
        async save(){
            var data = {
                matricule : this.formData.matricule,
                nom : this.formData.nom,
                prenoms : this.formData.prenoms,
                telephone : this.formData.telephone
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
