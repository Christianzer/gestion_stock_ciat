import Vuex from 'vuex';
import Vue from 'vue';
import clients_store from "./modules/clients_store";
import produits_store from "./modules/produits_store";
// Load vuex
Vue.use(Vuex);

// Create store
export default new Vuex.Store({
    modules: {
        clients_store,
        produits_store
    }
});
