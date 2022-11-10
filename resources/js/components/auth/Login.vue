<template>
    <div class="container-fluid login-form p-0 pt-5">
    <div class="row justify-content-center mt-5">
        <h1 class="col h1 p-0 tile_h1">Bienvenue</h1>
    </div>
    <div class="row justify-content-center mt-5">
        <div class="col p-0">

    <form>
        <div class="mb-5 ">
            <label for="exampleInputEmail1" class="form-label body_medium">Email</label>
            <input type="email" v-on:keyup.enter="authenticate"  class="form-control body_small" id="exampleInputEmail1" aria-describedby="emailHelp" v-model="email" placeholder="Saisissez votre e-mail">

        </div>
        <div class="mb-4 ">
            <label for="exampleInputPassword1" class="form-label body_medium">Mot de passe</label>
            <input type="password"  v-on:keyup.enter="authenticate" class="form-control body_small" id="exampleInputPassword1" v-model="password" placeholder="Saisissez votre mot de passe">
            <div class="form-text text-end"><button type="button" class="btn btn-link p-0 body_regular" @click="forgotpassword">Mot de passe oublié</button></div>
        </div>
        <button type="button" @click="authenticate" class="btn btn-dark mt-5 btn-login body_medium">Se connecter</button>
    </form>

        </div>
    </div>
    </div>






</template>

<script>

import {computed, ref} from 'vue';
    import { useRouter, useRoute } from 'vue-router'
    import axios from 'axios';
    import {useStore} from 'vuex';
import {
    DISPLAY_LOADER,
    HIDE_LOADER,
    LOADER_MODULE, TOASTER_GET_ALL,
    TOASTER_MESSAGE,
    TOASTER_MODULE,
    TOASTER_REMOVE_TOAST
} from '../../store/types/types'


    export default {
        name: "Login",
        components:{},
        setup(){
            const email= ref('');
            const password= ref('');
            const trans= ref(false);

            const router = useRouter();
            const route = useRoute();
            const store = useStore();

            function authenticate() {
                let err = false;
                let err_txt = [];
                let email_regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,8})+$/;
                let email_addr = email.value.replace(/ /g,'');

                if(email_addr==''){
                    err = true;
                    err_txt.push("Veuillez entrer l'adresse e-mail.");
                }else if(email_addr !='' && !email_regex.test(email_addr)){
                    err = true;
                    err_txt.push("L'adresse e-mail n'est pas valide.");
                }
                if(password.value.replace(/ /g,'')==''){
                    err = true;
                    err_txt.push("Veuillez entrer le mot de passe.");
                }

                if(err){
                    let i = 0;
                    for(i=0; i<err_txt.length; i++) {
                        store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                            type: 'danger',
                            message: err_txt[i],
                            ttl: 5,
                        });
                    }
                }else{
                    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Connexion en cours..']);


                    axios.post('/authenticate', {
                        email: email.value,
                        password: password.value
                    })
                        .then(function (response) {
                            if (response.data.user != null) {
                                window.sessionStorage.setItem('name', response.data.user.name);
                                window.sessionStorage.setItem('auth', window.btoa(email.value));
                                window.sessionStorage.setItem('roles', window.btoa(JSON.stringify(response.data.roles)));
                                window.sessionStorage.setItem('profile_permissions', window.btoa(JSON.stringify(response.data.profile_permissions)));
                                
                                window.localStorage.setItem('name', response.data.user.name);
                                window.localStorage.setItem('auth', window.btoa(email.value));
                                window.localStorage.setItem('roles', window.btoa(JSON.stringify(response.data.roles)));
                                window.localStorage.setItem('profile_permissions', window.btoa(JSON.stringify(response.data.profile_permissions)));
                                router.replace({
                                   name:'LandingPage',
                                   query: {
                                       ...route.query,
                                   },
                                })
                            } else {
                                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                                    type: 'danger',
                                    message: "User not found",
                                    ttl: 5,
                                });
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                        }).finally(() => {
                        store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                    });
                }
            }

            function forgotpassword() {
                router.push({
                    name:'ForgotPassword'
                })
            }

            return {
                trans,
                email,
                password,
                authenticate,
                forgotpassword
            }
        }
    }
</script>

<style scoped>
    .login-form{
        width: 375px;
    }
    .btn-login{
        width: 100%;
    }
</style>
