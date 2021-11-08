import {listes_clients} from "../../api/clients_api";

const state = {
    clients : [],
    loader_data : false
};

const getters = {
    all_clients : state =>{
        return state.clients
    },

    loader_data : state =>{
      return state.loader_data
    },
};

const actions = {
    async fetchclients ({commit}){
        const response = await listes_clients();
        if (response.status === 200){
            commit('SET_CLIENTS',response.data)
            commit('SET_IS_LOADING',true)
        }

    }
};

const mutations = {
    SET_CLIENTS (state, data) {
        state.clients = data
    },

    SET_IS_LOADING (state, data) {
        state.loader_data = data
    }

};

export default {
    state,
    getters,
    actions,
    mutations
};
