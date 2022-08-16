<template>
  <router-view>
    <transition enter-active-class="animate__animated animate__fadeIn">
      <div class="container-fluid h-100 bg-color" id="container">
        <main-header />
        <div class="row d-flex align-content-stretch align-items-stretch flex-row hmax main-view-wrap reports-page" style="z-index:100" >
            <side-bar />
            <div class="col main-view container">
                <h1 class="d-flex align-items-center m-0">
                  <span class="matrice-icon"></span>
                  <span class="ms-3 font-22 almarai_extrabold_normal_normal">MATRICE PERMIS / PERSONNEL</span>
                </h1>
                <transition name="list" appear>
                    <div class="cust-page-content client-detail m-auto pt-5">
                        <div class="page-section">
                            <div class="permis-block d-flex py-0">
                                <div class="user-name">&nbsp;</div>
                                <div class="permis-list d-flex">
                                    <div class="permis-date rounded-2 py-3 text-center" style="background: #D9D9D9" v-for="(item, index) in permis" :key="index">
                                        {{ item.name }}
                                    </div>
                                </div>
                            </div>
                            <div class="permis-block d-flex bg-white mb-2" v-for="(permisItem, index) in matrixData" :key="index">
                                <div class="user-name text-capitalize">{{ permisItem.name }}</div>
                                <div class="permis-list d-flex">
                                    <div class="permis-date rounded-pill text-center" 
                                        v-for="(item, permitIndex) in permis" 
                                        :key="permitIndex" 
                                        :style="{ 'background-color': permisItem.permis[item.id].bgColor, 'color': permisItem.permis[item.id].color }">
                                        {{ permisItem.permis[item.id].expDate }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
      </div>
    </transition>
  </router-view>
</template>
<script>
import { ref, onMounted } from 'vue';

import {     
  DISPLAY_LOADER,
  HIDE_LOADER,
  LOADER_MODULE,
  } from '../../store/types/types';
  
import axios from 'axios';
import { useStore } from 'vuex';

export default {
    setup() {
        const store     = useStore();
        const matrixData = ref([]);
        const permis = ref([]);
        onMounted(()=>{
            axios.post('/get-user-permis').then((res)=>{
                permis.value = res.data.permis
                matrixData.value = res.data.matrixData
            }).catch((error)=>{
                console.log(error);
            })
        })
        return {
            permis,
            matrixData
        }
  },
}
</script>
<style lang="scss" scoped>
.main-view{
    padding: 0;
    h1{
        padding: 60px 10px 0 0;
    }
    .permis-block{
        padding: 15px;
    }
    .user-name{
        min-width: 15rem;
    }
    .permis-date{
        min-width: 10rem;
        font-size: 12px;
        padding: 3px 10px;
        color: black;
        margin-left: .5rem;
    }
}
</style>