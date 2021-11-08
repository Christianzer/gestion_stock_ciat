require('./bootstrap');
window.Vue = require('vue');
//Import Vue Filter
require('./filters');

//Import progressbar
require('./progressbar');

//Setup custom events
require('./customEvents');

//Import View Router
import VueRouter from 'vue-router'
Vue.use(VueRouter)

//Import axios
import VueAxios from "vue-axios"
import axios from "axios"
Vue.use(VueAxios, axios)
import store from './store';
//Import v-from
import { Form, HasError, AlertError } from 'vform'
window.Form = Form;
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

// Import Bootstrap an BootstrapVue CSS files (order is important)
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

window.bus = new Vue()
window.Event = new Vue()


//Import Sweetalert2
import Swal from 'sweetalert2'
window.Swal = Swal
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    onOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})
window.Toast = Toast


//Routes
import { routes } from './routes';


//Register Routes
const router = new VueRouter({
    routes,
    mode: 'hash',

})

const app = new Vue({
    el: '#app',
    router,
    store
});


