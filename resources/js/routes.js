import dash from './components/admin/index.vue'
import client from './components/clients/index.vue'
import stocks from './components/stocks/index.vue'
import ventes from './components/ventes/index.vue'
import panier from "./components/ventes/panier";
import ventes_users from './components/ventes/ventes.vue'
import facture from "./components/ventes/facture";
import commande from "./components/ventes/commande";
import listes_commandes from "./components/commande/listes_commandes";
import factures_ventes from "./components/commande/factures_ventes";
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
    },
    {
        name: 'listes_commandes',
        path:'/listes_commandes',
        component:listes_commandes
    },
    {
        name: 'commande',
        path:'/commande/:code_commande',
        component:commande
    },
    {
        name: 'factures_vente',
        path:'/factures_vente/:code_commande',
        component:factures_ventes
    }
];
