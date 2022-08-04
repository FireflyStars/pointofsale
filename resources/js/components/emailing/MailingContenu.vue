<template>


     <div class="container-fluid h-100 bg-color">
                <main-header />

                <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100" >

                    <side-bar />

                    <div class="col main-view container">
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <page-title icon="emailing" name="PLATEFORME MARKETING" class="almarai_extrabold_normal_normal"/>
                            <base-button
                                prepend
                                class="btn btn-newrdv body_medium"
                                kind="warning"
                                title="Liste campagne"
                                style="border: 0;
                                border-radius: 10px; 
                                font-size: 12px !important; 
                                margin-right: 4rem; 
                                background: #FF0000;"
                                @click.prevent="$router.push({
                                    name: 'marketing-list'
                                })"
                            >
                                <icon name="clipboard" />
                            </base-button>
                        </div>

                         <transition
        enter-active-class="animate__animated animate__fadeIn"
        leave-active-class="animate__animated animate__fadeOut"
    >
        <div class="container"  v-if="showcontainer">
     
            <div>
                <h3 class="margin-align">
                    PLATEFORME MARKETING > {{ my_name }} > Cible > Contenu
                </h3>
            </div>

            <div class="row flex_wrap bg-panel p-4">
                <div class="row flex_wrap col-lg-10">
                    <div class="card shadow-sm mb-4">
                        <div class="img_container">
                            <img
                                src="../../images/prestation.png"
                                class="card-img-top body linear-gradient"
                                alt=""
                            />
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-uppercase card-title">
                                LETTRE D’ACCOMPAGNEMENT
                            </h5>
                            <div>
                                <p class="card-text" style="color: orangered">
                                    Personnalisable
                                </p>

                                <p class="card-text cardtext2">
                                    <!-- Format A4 - Impression recto - 80g/m² -->
                                    {{textlettre}}
                                </p>
                                <p>
                                    <button
                                        class="button type"
                                        v-on:click="personnaliser('lettre')"
                                    >
                                        PERSONNALISER
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm mb-4">
                        <div class="img_container">
                            <img
                                src="../../images/prestation.png"
                                class="card-img-top body linear-gradient"
                                alt=""
                            />
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-uppercase card-title">
                                FLYER
                            </h5>
                            <div>
                                <p class="card-text" style="color: orangered">
                                    Personnalisable
                                </p>

                                <p class="card-text cardtext2">
                                    <!-- Format A4 - Impression recto/verso - 180g/m² -->
                                    {{textflyer}}
                                </p>
                                <p>
                                    <button
                                        class="button type"
                                        v-on:click="personnaliserflyer('flayer')"
                                    >
                                        PERSONNALISER
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm mb-4">
                        <div class="img_container">
                            <img
                                src="../../images/prestation.png"
                                class="card-img-top body linear-gradient"
                                alt=""
                            />
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-uppercase card-title">
                                DÉPLIANT
                            </h5>
                            <div>
                                <p class="card-text" style="color: orangered">
                                    Non Personnalisable
                                </p>

                                <p class="card-text cardtext2">
                                    <!-- Format fermé A5 - Impression recto/verso - 3
                                    volets - 120g/m² -->
                                    {{textedepliant}}
                                    
                                    
                                </p>
                                <p>
                                    <button
                                        class="button type"
                                        v-on:click="voirdepliant"
                                    >
                                        VOIR
                                    </button>
                                </p>
                            </div> 
                        </div>
                    </div>
                    <div class="card shadow-sm mb-4">
                        <div class="img_container">
                            <img
                                src="../../images/prestation.png"
                                class="card-img-top body linear-gradient"
                                alt=""
                            />
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-uppercase card-title">
                                ENVELOPPE PORTEUSE
                            </h5>
                            <div>
                                <p class="card-text" style="color: orangered">
                                    Non Personnalisable
                                </p>

                                <p class="card-text cardtext2">
                                    <!-- Format 162x229 - Impression recto - 3 volets
                                    - 80g/m²  -->
                                    {{texteenveloppe}}
                                </p>
                                <p>
                                    <button
                                        class="button type"
                                       v-on:click="voirenveloppe"
                                    >
                                        VOIR
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h5 class="paragraph">
                        OUTRE CES ÉLÉMENTS, VOTRE CAMPAGNE MAILING POSTAL
                        COMPREND :
                    </h5>
                    <p class="p-text">
                        L’impression et le façonnage La mise sous pli
                        L’affranchissement Le dépôt Poste
                        
                    </p>
                    <div>

                        <button
                            class="button type"
                            :class="{ 'cursor-not-allowed': loading }"
                            v-on:click="validateAndSendEmail"
                            :disabled="loading"
                        >
                            <span>VALIDER</span>
                        </button>

                    </div>
                </div>

            </div>

                

        </div>
        
            </transition>
                    </div>
                </div>
     </div>

</template>

<script>

import { ref, onMounted, nextTick } from "vue";
import SideBar from "../layout/SideBar";
import Main from "../Main.vue";
import useCompanies from "../../composables/companies";
import { useRoute } from 'vue-router';
import { useStore } from 'vuex'
import {
    DISPLAY_LOADER,
    HIDE_LOADER,
    LOADER_MODULE, 
    TOASTER_GET_ALL,
    TOASTER_MESSAGE,
    TOASTER_MODULE,
    TOASTER_REMOVE_TOAST
} from '../../store/types/types'

export default {

    components: {
        Main,
        SideBar,
    },

    props: {
        cible_id: {
            type: String,
        },
        type: {
            type: String,
        },
        show: {
            type: String,
        },
    },

    setup(props) {

        const my_name = localStorage.getItem("category");
        const textedepliant = ref('');
        const texteenveloppe = ref('');
        const textlettre = ref('');
        const textflyer = ref ('');
        const filedepliant = ref('')
        const fileEnvelop = ref('')
        const loading = ref(false)
         
        const store = useStore()
        const route = useRoute()

        const {
            courrier,
            courrier_lettre,
            courrier_autre,
            check_name,
            getCourrier,
        } = useCompanies();



        const validateAndSendEmail = async () => {
            if(loading.value) return
            try {
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Loading...']);
                loading.value = true
                await axios.post(`/validate-and-send-email/${props.cible_id}`)
                loading.value = false
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'success',
                    message: 'Envoi de votre campagne COURRIER au service Marketing',
                    ttl: 5,
                });
            }
            catch(e) {
                loading.value = false
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Something went wrong',
                    ttl: 5,
                });
                throw e
            }
            finally {
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            }
        }


   const showcontainer = ref(false);
        onMounted(() => {
            nextTick(() => {

                showcontainer.value = true;
            });
        });
        // onMounted(getCourrier);
        onMounted(() => {

//--
            axios.get("/fields/" + route.params.cible_id).then(function (response) {
                    
                    textedepliant.value=response.data.campagneCategory.textedepliant;
                    texteenveloppe.value=response.data.campagneCategory.texteenveloppe;
                    textlettre.value=response.data.campagneCategory.textlettre;
                    textflyer.value=response.data.campagneCategory.texteflyer;
                    filedepliant.value = response.data.campagneCategory.fileDepliantFull
                    fileEnvelop.value = response.data.campagneCategory.fileEnvelopFull
                
            
                }).catch(function (error) {
                
                
                }).finally(function(){

            });
            
            
            nextTick(() => {
                showcontainer.value = true;
            });
//---



            
            //     let response = axios.get("/getCourrier").then(function (response) {
            //         console.log(response);
            //         courrier.value = response.data.data;
            //         courrier_autre.value = response.data.data2;
            //         courrier_lettre.value = response.data.data1;
            //         console.log("courrier", courrier);
            //         console.log("courrier_autre", courrier_autre);
            //         console.log("courrier_lettre", courrier_lettre);
            //         for (let i = 0; i < courrier.value.length; i++) {
            //             check_name.value.push(response.data.data[i].name);
            //             // console.log(check_name.value.push(response.data.data[i].name));
            //         }
            //     });
        });

        const voirdepliant=()=>{
            if(filedepliant.value?.fullpath) window.open(filedepliant.value?.fullpath)
        }

        const voirenveloppe=()=>{
            if(fileEnvelop.value?.fullpath) window.open(fileEnvelop.value?.fullpath)
        }

        return {
            filedepliant,
            fileEnvelop,
            loading,
            validateAndSendEmail,
            showcontainer,
            check_name,
            courrier,
            courrier_lettre,
            courrier_autre,
            textedepliant,
            texteenveloppe,
            textflyer,
            textlettre,
            my_name,
            voirdepliant,
            voirenveloppe
        };
    },
    methods: {
        // getData() {
        //     axios.get("/getCourrier").then((response) => {
        //         this.singleData = response.data.data;
        //         console.log(this.singleData);
        //     });
        // },
        
        personnaliserflyer(e){
                this.$router.push({
                name: "personnaliserflyer",
                params: {
                    cible_id: `${this.$route.params.cible_id}`,
                    type: this.$route.params.type,
                    show: e,
                },
            });
        },
        personnaliser(e) {
            console.log(e);
            this.$router.push({
                name: "mailingInterface",
                params: {
                    cible_id: `${this.$route.params.cible_id}`,
                    type: this.$route.params.type,
                    show: e,
                },
            });
        },
        goToHome() {
            this.$router.push("/emailing");
        },
    },
};
</script>

<style scoped>
.margin-align {
    margin-bottom: 40px;
    font-size: 17px;
    font-weight: bold;
}
.margin-ajustement {
    margin-top: 39px;
}
.ajustement {
    display: flex;
}

.paragraph {
    padding: 0px;
    font-size: 12px;
    text-align: center;
    padding-right: 8px;
    line-height: 1.3;
    text-transform: uppercase;
    margin-top: 101px;
}
.p-text {
    padding: 0px;
    font-size: 11.6px;
    text-align: center;
    padding-right: 8px;
    line-height: 1.3;
    text-transform: uppercase;
    margin-top: 45px;
    color: orangered;
}
.container {
    padding-bottom: 100px;
}
.color {
    color: orangered;
}
.margin {
    margin-bottom: 40px;
    margin-top: 35px;
}
.card-title {
    text-align: center;
    font-size: unset;
    font-family: revert;
    cursor: pointer;
    min-height: 36px;
}
.card-title:hover {
    color: orangered;
}
.card-text {
    font-size: 13px;
    margin-top: 15px;
    text-align: center;
    line-height: 13.5px;
}
.card {
    width: 213px;
    padding: 0;
    margin: 7px;
    border: 1px solid rgb(0 0 0 / 34%);
    border-radius: 0;
}
.flex_wrap {
    display: flex;
    flex-wrap: wrap;
}
.card-body {
    padding: 12px 20px 15px;
    font-size: 15px;
}
.card-img-top.body {
    width: 100px;
    height: 166px;
    box-shadow: -46px 0px 23px -10px rgb(97 96 96 / 50%);
}
.img_container {
    text-align: center;
    background: #e6e6e6;
}
.card a {
    color: inherit;
    text-decoration: none;
}
.button {
    background-color: white;
    border: 1px solid #ff4500;
    color: orangered;
    cursor: pointer;
    width: 117px;
    height: 100%;
    margin-left: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
}
.type {
    font-size: x-small;
    font-weight: bold;
}
.link {
    cursor: pointer;
    text-decoration: none;
    color:orange;
}
.link:hover {
    color: orangered;
}
.cardtext2 {
 min-height: 80px;
}
</style>
