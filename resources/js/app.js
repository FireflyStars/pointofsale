import {TOASTER_CLEAR_TOASTS, TOASTER_MESSAGE, TOASTER_MODULE} from "./store/types/types";

require('./bootstrap');

import axios from 'axios';
import { createApp } from 'vue';
import App from './components/App';
import VueClickAway from 'vue3-click-away';
import { GlobalComponents } from './services/Index';

import router from './router/router';
import store from './store/store';

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response.status == 509)//509 is unassigned so we use for custom error code reporting
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:error.response.data,ttl:8,type:'danger'});

        if (error.response.status !== 401&&error.response.status !== 419) return Promise.reject(error)
        sessionStorage.clear();
        store.commit(`${TOASTER_MODULE}${TOASTER_CLEAR_TOASTS}`);
        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'La session a expiré. Veuillez vous reconnecter.',ttl:8,type:'danger'});
        router.push({
            name:'Login',
        })
    }
)
window.addEventListener('offline',()=>{
    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'Pas de connexion internet.',ttl:8,type:'danger'});
});
window.addEventListener('online',()=>{
    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:'Connexion Internet rétablie.',ttl:8,type:'success'});
});
const app=createApp(App)
    .use(router)
    .use(store)
    .use(VueClickAway);
    GlobalComponents(app);

    app.mount('#app');