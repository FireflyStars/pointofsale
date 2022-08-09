<template>


        <div class="container-fluid h-100 bg-color">
                <main-header />

                <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100" >

                    <side-bar />

                    <div class="col main-view container">
                        <page-title icon="emailing" name="PLATEFORME MARKETING" class="almarai_extrabold_normal_normal"/>
                          <transition
        enter-active-class="animate__animated animate__fadeIn"
        leave-active-class="animate__animated animate__fadeOut"
    >
        <form class="space-y-6"  v-if="showcontainer">

            <div class="container">
                
                <div class="row" style="height: calc(0.75 * 297mm);">

                    <div class="col-lg-6">
             
                        <h3 class="margin">
                            <a @click="goToHome()" class="link">PLATEFORME MARKETING</a> >
                            <a @click="goToPrestation()" class="link">{{
                                my_name
                            }}</a>
                            > Cible > Contenu
                        </h3>

                        <div class="bg-panel p-4">
                             <div class="row mb-1">
                                    <div class="col-3 tname" >
                                        <b>Objet:</b>
                                    </div>
                                     <div class="col noedit">
                                        {{subject}}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-3 tname" >
                                        <b>Expéditeur:</b>
                                    </div>
                                     <div class="col noedit">
                                        {{sender}}
                                    </div>
                                </div>
                                 <div class="row mb-1"> 
                                    <div class="col-3 tname" >
                                        <b>Répondre à:</b>
                                    </div>
                                     <div class="col noedit">
                                        {{replyto}}
                                    </div>
                                </div>
                            <template v-for="item,index in fields" :key="index" >
                                <div class="row mb-1" v-if="index!='Email_agence'&&index!='Telephone_agence'&&item.active==1">
                                    <div class="col-3 tname" >
                                        <b>{{item.name}}:</b>
                                    </div>
                                     <div class="col noedit">
                                        {{item.value}}
                                    </div>
                                </div>
          
                            </template>
                               
                                    
                            <hr/>
                            <div class="row">
                                <div class="col group_input" v-if="typeof fields.Email_agence!='undefined'&&fields.Email_agence.active==1">
                                    <b>{{fields.Email_agence.name}}:</b> <input class="mt-2" type="text" v-model="email_agence"/>
                                </div>
                                <div class="col group_input"  v-if="typeof fields.Telephone_agence!='undefined'&&fields.Telephone_agence.active==1">
                                    <b>{{fields.Telephone_agence.name}}</b> <input class="mt-2" type="text" v-model="phone_agence"/>
                                </div> 
                            </div>
                                
                        </div>

                        <div class="row rigth">
                            <button
                                class="button-valider type"
                                :class="{ 'cursor-not-allowed': loading }"
                                type="button"
                                v-on:click="saveCompany"
                                :disabled="loading"
                            >
                                VALIDER
                                <Icon name="spinner" v-show="loading" style="font-size: 10px;" />
                            </button>
                        
                        </div>


                    </div>
                    
                    <div
                        v-if="showcontainer"
                        class="col-lg-6 apercu d-table position-relative"
                    >
                        <h6>APERÇU</h6>
                        <!-- transform: scale(0.75);left: 1.5rem;transform-origin: top left; -->
                        <div 
                            class="bg-panel" 
                            style="
                            position: absolute; 
                            min-width: 795px;
                            min-height: 1124px;
                            " 
                            :style="{ 
                                'background-image': `url(${imageurl})`,
                                'background-repeat': 'no-repeat',
                                'background-size': `contain`
                            }"
                        >
                            <!-- <img style="width: auto; height: auto;"
                                v-if="imageurl!=''"
                                :src="`/storage/${imageurl}`" 
                            /> -->
                            <template v-for="item,index in fields" :key="index">
                                <span 
                                    v-if="item.active==1" 
                                    v-bind:style="{color:item.color,fontSize:`${item.size}px`,fontFamily:item.font,top:`${item.y}px`,left:`${item.x}px`}"
                                >
                                    <template 
                                        v-if="index != 'Email_agence' && index != 'Telephone_agence'"
                                    >
                                        {{item.value}}
                                    </template>
                                    <template v-else-if="index == 'Email_agence'">
                                        {{email_agence}}
                                    </template>
                                    <template 
                                        v-else-if="index=='Telephone_agence'"
                                    >
                                        {{phone_agence}}
                                    </template>
                                </span>
                            </template>
                        
                        </div>
                    </div>

                </div>

                <!-- <div class="row">
                    <button
                        class="button-valider type"
                        :class="{ 'cursor-not-allowed': loading }"
                        type="button"
                        v-on:click="saveCompany"
                        :disabled="loading"
                    >
                        VALIDER
                        <Icon name="spinner" v-show="loading" style="font-size: 10px;" />
                    </button>
                   
                </div> -->

            </div>
        </form>
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
import { useRouter, RouterView, useRoute } from "vue-router";
import EmailingAudit from "../templates/EmailingAudit";
import { useStore } from "vuex";
import {
    DISPLAY_LOADER,
    HIDE_LOADER,
    LOADER_MODULE,
    TOASTER_GET_ALL,
    TOASTER_MESSAGE,
    TOASTER_MODULE,
    TOASTER_REMOVE_TOAST,
} from "../../store/types/types";

export default {
    components: {
        Main,
        SideBar,
        EmailingAudit,
    },
    mounted() {
        if (localStorage.email) {
            this.email = localStorage.email;
        }
        if (localStorage.phone) {
            this.phone = localStorage.phone;
        }
    },

    // data: () => ({
    //     pdfsrc: "../../images/EmailingAudit.pdf",
    // }),
    props: {
        cible_id: {
            type: String,
        },
        type: {
            type: String,
            default() {
                return "";
            },
        },
    },
    // props: ["email", "phone", "agence"],
    data: function () {
        return {
            company: "",
        };
    },
    setup(props) {


        const store = useStore();

        const my_name = localStorage.getItem("category");
   
        const loading = ref(false)
        const idtemplate = localStorage.getItem("imagetemplate");
        const email_agence=ref('');
        const phone_agence=ref('');
        const imageurl=ref('');
        const sender=ref('La compagnie des toits - ');
        const replyto=ref('La compagnie des toits - ');

        const fields=ref({});
        const subject = ref('');

        const router = useRouter();

        const {
            errors,
            company
    
        } = useCompanies();

        const route = useRoute();

        const saveFlyerPdf = async () => {
            try {
                loading.value = true
                await axios.post(`/save-flyer-pdf/${props.cible_id}`, {
                    input_coord: phone_agence.value,
                    input_email: email_agence.value
                })
                loading.value = false
            }
            catch(e) {
                loading.value = false
                throw e
            }
        }
 

        function saveCompany() {
            
            if(loading.value) return

            loading.value = true
            
            axios.post("/contentform/" + route.params.cible_id, {
                email: email_agence.value,
                phone: phone_agence.value
            })
            .then(({ data }) => {

                if(data.ok==1) {
                 if(route.params.type.toLowerCase()=='email'){   
                router.push({
                    name: "envoi",
                    params: {
                        cible_id: `${route.params.cible_id}`,
                        type: route.params.type,
                    },
                });}else{

                    router.go(-1)
                    loading.value = false
                }
                }

            }).catch(() => {
                loading.value = false

            }).finally(() => {
                loading.value = false
            })
       
        }
       

        const showcontainer = ref(false);
        onMounted(() => {
            
    
            axios.get("/fields/" + route.params.cible_id).then(function (response) {
                    fields.value=response.data.fields;
                    email_agence.value=response.data.fields.Email_agence.value;
                    phone_agence.value=response.data.fields.Telephone_agence.value;
                    imageurl.value=response.data.image_full;
                    subject.value=response.data.campagneCategory.objet;
                    sender.value+=`${response.data.fields.Nom_agence.value} - ${response.data.fields.Email_agence.value}`;
                    replyto.value+=`${response.data.fields.Nom_agence.value} - ${response.data.fields.Email_agence.value}`;
                }).catch(function (error) {
                
                
                }).finally(function(){

            });
            
            
            nextTick(() => {
                showcontainer.value = true;
            });
        });

        return {
            loading,
            showcontainer,
            errors,
            company,
            saveCompany,
            sender,
            replyto,
            my_name,
            subject,
            saveFlyerPdf,
            idtemplate,
            imageurl,
            fields,
            email_agence,
            phone_agence,
            route,
            
        };
    },

    methods: {
        
        goToHome() {
            this.$router.push("/emailing");
        },

        goToPrestation() {
            let id_category = localStorage.getItem("id_category");
            this.$router.push({
                name: "emailingprestations",
                params: {
                    id: `${id_category}`,
                },
            });
        },
    },
};
</script>

<style scoped>
.bg-panel span{
    position: absolute;
}
.position {
    position: absolute;
    font-size: 14px;
    font-weight: 600;
}

.margin-align {
    margin-bottom: 40px;
    margin-top: -34px;
    font-size: 17px;
    font-weight: bold;
}
.margin-ajustement {
    margin-top: 39px;
}
.ajustement {
    display: flex;
}

.container {
    padding-bottom: 100px;
}
img.card-img-top {
    height: 100%;
}

div.group_input {
    justify-content: space-between;
}
div.group_input input {
    width: 250px;
    background-color: #d3d3d3;
    border: none;
    height: 27px;
    font-size: small;
    padding: 0 10px;
}
div.group_input {
    margin: 7px 0;
    align-items: center;
}
.text_data {
    display: flex;
}
label.fix_width {
    width: 100px;
    font-size: 11px;
    font-weight: 700;
}
div.group_input span {
    margin-left: 15px;
    font-size: 10px;
}
label.fix_width_tiret {
    width: 45px;
    text-align: center;
    font-size: 11px;
    font-weight: 700;
}
label.fix_width_column {
    width: 65px;
    text-align: end;
    font-size: 11px;
    font-weight: 700;
    padding-right: 5px;
}
.color {
    color: orangered;
}
.margin {
    margin-bottom: 40px;
    margin-top: 35px;
    font-size: 17px;
    font-weight: bold;
}
.card-title {
    text-align: center;
    font-size: unset;
    font-family: revert;
}
.card-text {
    font-size: x-small;
    margin-top: 18%;
    text-align: center;
}
.card {
    width: 12rem;
}
.body {
    width: 100%;
    height: 100%;
}
hr {
    margin: 40px 0px;
}
.col-lg-7 {

}
.apercu h6 {
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 15px;
    text-align: center;
}
.apercu {
    height: 600px;
    overflow: overlay;
    width: 465px;
}
.last label {
    margin-right: 10px;
    margin-left: 15px;
}
.button-valider {
    background-color: orangered;
    color: white;
    border: 2px solid orangered;
    width: 125px;
    height: 27px;
}
.type {
    font-size: x-small;
    font-weight: bold;
}
.rigth {
    float: right;
    padding: 40px 40px 0 0;
    margin-bottom: 20px;
}
@media only screen and (min-width: 1400px) {
    .state {
        margin-left: -55px;
    }
    .text_data {
        display: block;
    }
}
.link {
    cursor: pointer;
    text-decoration: none;
    color:orange;
}
.link:hover {
    color: orangered;
}
b{
    font-weight: 600;
}
.noedit{
    background: #EEE;
color: #aaa;
}
.tname{
    align-self: center;
}
.extravalidbtn{
margin-left: 20px;
}
</style>
