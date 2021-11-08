import {listes_produits} from "../../api/ventes_api";

const state = {
    produits : [],
};

const getters = {
    all_produits : state =>{
        return state.produits
    },

};

const actions = {
    async fetchProduits ({commit}){
        const response = await listes_produits();
        if (response.status === 201){
            console.log(response.data.element)
            commit('SET_PRODUITS',response.data.element)
            localStorage.setItem('articles_prod',JSON.stringify(response.data.element))
        }

    }
};

const mutations = {
    SET_PRODUITS (state, data) {
        state.produits = data
    },

};

export default {
    state,
    getters,
    actions,
    mutations
};
