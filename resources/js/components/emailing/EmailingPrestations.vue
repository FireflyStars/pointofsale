<template>


    <div class="container-fluid h-100 bg-color" >
                <main-header />

                <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap" style="z-index:100" >

                    <side-bar />

                    <div class="col main-view container">

                        <div class="d-flex justify-content-between align-items-center">
                            <page-title icon="emailing" name="PLATEFORME MARKETING" class="almarai_extrabold_normal_normal" />
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
        <div class="container" v-if="showcontainer">

            <div v-if="myvar == true">
                <h3 class="margin" v-for="data in campagnedata" :key="data.id">
                    <a @click="goToHome()" class="link">PLATEFORME MARKETING</a> >
                    {{ data.name }}
                </h3>
            </div>
            <div v-if="myvar2 == true && myvar == false">
                <h3 class="color margin" v-for="item in title" :key="item.id">
                    <a @click="goToHome()" class="link">PLATEFORME MARKETING</a> >
                    {{ item.name }}
                    
                </h3>
            </div>

                    <transition-group tag="div" class="row flex_wrap bg-panel p-4" name="list" appear>
                <div
                    class="card shadow-sm mb-4"
                    v-for="item in template"
                    :key="item.id"
                >
                
                    <div class="img_container"  @click="SubCategory(item.id, item.name)">
                        <img
                            :src="`/storage/${item.url}`"
                            class="card-img-top body linear-gradient" :class="{'niveau3img' :item && item.niveau == 3}"
                        />
                    </div>
                    <div
                        class="card-body d-flex flex-column" :class="{'niveau3cardbody' :item && item.niveau == 3}"
                        @click="SubCategory(item.id, item.name)"
                    >
                      
                        <h5 class="card-title text-uppercase card-title"  :class="{'niveau3cardtile' :item && item.niveau == 3}">
                            {{ item.name }} 

                        </h5>
                            <div>
                                <p class="card-text">{{ item.text }}</p>
                            </div>

                            <div class="voir-plus" @click="SubCategory(item.id, item.name)">
                                    voir
                            </div>

                    </div>
                </div>
                </transition-group>
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
import { useRoute } from 'vue-router'

export default {

    components: {
        Main,
        SideBar,
    },

    props: {
        id: {
            // This is the param that we are putting in our url
            type: String,
            default() {
                return 0;
            },
        },
        idc: {
            type: Number,
            default() {
                return 0;
            },
        },
    },
    setup(props, context) {
        
        const showcontainer = ref(false);

        const route = useRoute()

        onMounted(() => {
            nextTick(() => {
                showcontainer.value = true
                getTemplates(props.id, route.query.redirect)
            });
        });
        const {
            myvar,
            myvar2,
            mytitle,
            template,
            campagnedata,
            title,
            subdata,
            mylist,
            getTemplates,
            SubCategory,
        } = useCompanies();

        // onMounted(SubCategory(props.id));


        return {
            showcontainer,
            template,
            campagnedata,
            SubCategory,
            subdata,
            mylist,
            myvar,
            title,
            mytitle,
            myvar2,
        };
    },

    methods: {

        goToHome() {
            this.$router.push("/emailing");
        },

    }

};
</script>

<style scoped>
.margin-email {
    margin-top: 50px;
}
.margin-ajustement {
    margin-top: 53px;
}
.ajustement {
    display: flex;
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
    font-size: 17px;
    font-weight: bold;
}
.card-title {
    text-align: center;
    font-size: unset;
    font-family: revert;
    cursor: pointer;
    font-weight: bolder;
}
.card-title:hover {
    color: orangered;
}

.card-text {
    font-size: 13px;
    margin-top: 29px;
    text-align: center;
    line-height: 13.5px;
}
.card {
    width: 235px;
    padding: 0;
    margin: 9px;
    border-radius: 0;
    cursor: pointer;
    border:none;
}
.flex_wrap {
    display: flex;
    flex-wrap: wrap;
}
.card-body {
    padding: 12px 20px 0px;
    font-size: 15px;
    min-height: 201px;
    background-color: black;
    color: white;
    width: 100%;
}
.card-img-top.body {
    width: 100%;
    height: 326px;
}
.img_container {
    text-align: center;
    background: #e6e6e6;
}


.card a {
    color: inherit;
    text-decoration: none;
}
.link {
    cursor: pointer;
    text-decoration: none;
    color:orange;
}

.link:hover {
    color: orangered;
}

.voir-plus{
text-align: center;
background-color:#f26e26;
margin-top: 42px;
color: white;
width: 50%;
align-self: center;
margin-top: auto;
margin-bottom: 30px;

}


.voir-plus:hover{
    background-color: red;
}

.niveau3cardbody{
    background-color: white  !important;
    color: black !important;
}
.niveau3img{
    height: 218px!important
}
</style>
