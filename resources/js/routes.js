import dash from './components/admin/index.vue'
import client from './components/clients/index.vue'
import stocks from './components/stocks/index.vue'
import ventes from './components/ventes/index.vue'
import panier from "./components/ventes/panier";
import ventes_users from './components/ventes/ventes.vue'
import facture from "./components/ventes/facture";
export const routes = [
    {
        name:'dashboard',
        path:'/',
        component:dash
    },
    {
        name: 'clients',
        path:'/clients',
        component:client
    },
    {
        name: 'stocks',
        path:'/stocks',
        component:stocks
    },
    {
        name: 'ventes',
        path:'/ventes',
        component:ventes
    },
    {
        name: 'panier',
        path:'/panier',
        component:panier
    },
    {
        name: 'ventes_users',
        path:'/ventes_users',
        component:ventes_users
    },
    {
        name: 'facture',
        path:'/facture/:code_facture',
        component:facture
    }
];
