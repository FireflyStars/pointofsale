<template>
    <div class="d-flex">
        <div class="col-9 bg-white">
            <table class="table table-borderless table-hover m-0">
                <thead>
                    <tr>
                        <th class="text-nowrap number">No POINTAGE</th>
                        <th class="client">CLIENT</th>
                        <th class="affaire">AFFAIRE</th>
                        <th class="personnel">PERSONNEL</th>
                        <th class="numberh">H.TRAVAILLE</th>
                        <th class="numberhtransport">H.TRAJET</th>
                        <th class="type">TYPE</th>
                        <th class="total">TOTAL</th>
                        <th class="action"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td valign="middle"></td>
                        <td valign="middle"><Affaire v-model="pointage.orderId"></Affaire></td>
                        <td valign="middle">
                            <select-box v-model="pointage.userId" :options="users" :name="'users'" :label="''"></select-box>
                        </td>
                        <td valign="middle"><input type="text" v-model="pointage.numberh" class="form-control"></td>
                        <td valign="middle"><input type="text" v-model="pointage.numberhtransport" class="form-control"> </td>
                        <td valign="middle">
                            <select-box v-model="pointage.typeId" :options="types" :name="'users'" :label="''"></select-box>
                        </td>
                        <td valign="middle"><span @click="AddPointage" class="d-flex align-items-center lcdtOrange cursor-pointer"><span class="plus-icon me-2"></span> AJOUTER</span></td>
                        <td></td>
                    </tr>
                    <tr v-for="(item, index) in pointages" :key="index" class="border-top">
                        <td valign="middle">{{ item.id }}</td>
                        <td valign="middle">{{ item.raisonsocila }}</td>
                        <td valign="middle">
                            <div class="d-flex">
                                <div class="col-3">{{ item.orderId }}</div>
                                <div>{{ item.orderName }}</div>
                            </div>
                            <!-- <div>{{ item.raisonsocila }}</div> -->
                        </td>
                        <td valign="middle">{{ item.userName }}</td>
                        <td valign="middle">{{ item.numberh }} Hr</td>
                        <td valign="middle">{{ item.numberhtransport }} Hr</td>
                        <td valign="middle">{{ item.type }}</td>
                        <td valign="middle" :class="{'green-text': item.numberh + item.numberhtransport == 8.75}">{{ item.numberh + item.numberhtransport }} Hr</td>
                        <td valign="middle">
                            <svg class="cursor-pointer" @click="deletePointage(item.id)" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 6H22V8H20V21C20 21.2652 19.8946 21.5196 19.7071 21.7071C19.5196 21.8946 19.2652 22 19 22H5C4.73478 22 4.48043 21.8946 4.29289 21.7071C4.10536 21.5196 4 21.2652 4 21V8H2V6H7V3C7 2.73478 7.10536 2.48043 7.29289 2.29289C7.48043 2.10536 7.73478 2 8 2H16C16.2652 2 16.5196 2.10536 16.7071 2.29289C16.8946 2.48043 17 2.73478 17 3V6ZM18 8H6V20H18V8ZM13.414 14L15.182 15.768L13.768 17.182L12 15.414L10.232 17.182L8.818 15.768L10.586 14L8.818 12.232L10.232 10.818L12 12.586L13.768 10.818L15.182 12.232L13.414 14ZM9 4V6H15V4H9Z" fill="black"/>
                            </svg>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-3 p-2">
            <div class="total-panel bg-white"></div>
        </div>
    </div>
</template>
<script>
import SelectBox from '../../components/miscellaneous/SelectBox.vue';
import Affaire from './Affaire.vue';

import { ref, onMounted, watchEffect, watch } from 'vue';
import { useStore } from 'vuex';
import {
    TOASTER_MODULE,
    TOASTER_MESSAGE,
    LOADER_MODULE,
    HIDE_LOADER,
    DISPLAY_LOADER,
GET_POINTAGE_LIST,
} from '../../store/types/types';
import Swal from 'sweetalert2';
import axios from 'axios';
export default{
    name: 'Pointage',
    props:{
        date: String,
    },
    components:{
        SelectBox,
        Affaire
    },
    setup(props, { emit }){
        const store = useStore();
        const pointage = ref({
            orderId: 0,
            typeId: 0,
            userId: 0,
            numberh: '',
            numberhtransport: '',
            datepointage: props.date,
        })
        const pointages = ref([]);
        watch(()=>props.date, (cur_val, pre_val)=>{
            pointage.value.datepointage = cur_val;
            getPointages(cur_val);
        })
        const users = ref([]);
        const types = ref([]);
        onMounted(()=>{
            axios.post('/get-saisie-rapide-info').then((res)=>{
                users.value = res.data.users;
                types.value = res.data.types;
                pointage.value.typeId = 1;
            }).catch((error)=>{
                console.log(error);
            }).finally(()=>{

            })
        })
        const AddPointage = ()=>{
            var error = false;
            if(pointage.value.orderId == 0){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Please Select Affaire',
                    ttl: 5,
                });                       
                error = true;
            }
            if(pointage.value.userId == 0){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Please Select Personnel',
                    ttl: 5,
                });                       
                error = true;
            }
            if(pointage.value.numberh == ''){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Please enter H.TRAVAILLE',
                    ttl: 5,
                });                       
                error = true;
            }
            if(pointage.value.numberhtransport == ''){
                store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`, {
                    type: 'danger',
                    message: 'Please enter H.TRAJET',
                    ttl: 5,
                });                       
                error = true;
            }
            if(! error){
                store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'Enregistrer le pointage...']);
                axios.post('/create-pointage', pointage.value).then(()=>{
                    pointages.value = res.data;
                }).catch((error)=>{
                    console.log(error);
                }).finally(()=>{
                    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
                });
            }
        }
        const deletePointage = (id)=>{
            Swal.fire({
                title: 'Etes-vous sûr?',
                text: "Vous ne pourrez pas revenir en arrière !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#42A71E',
                cancelButtonColor: 'var(--lcdtOrange)',
                cancelButtonText: 'Annuler',
                confirmButtonText: `Oui, s'il vous plaît.`
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/delete-pointage', { pointageId : id }).then((res)=>{
                        pointages.value = pointage.value.filter((item)=>{
                            return item.id != id;
                        });
                    }).catch((error)=>{
                        console.log(error);
                    });
                }
            });            
        }
        const getPointages = (date)=>{
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'loading pointages...']);
            axios.post('/get-pointages', { datepointage: date }).then((res)=>{
                pointages.value = res.data;
            }).catch((error)=>{
                console.log(error);
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            })
        }
        return {
            pointage,
            pointages,
            users,
            types,
            AddPointage,
            deletePointage
        }
    }
}
</script>
<style scoped>
table {
    font-size: 14px;
}
table thead th{
    color: #868686;
}
.number{
    width: 10%;
}
.client,
.numberh，
.numberhtransport,
.type{
    width: 15%;
}
.affaire{
    width: 20%;
}
input{
    height: 40px;
    width: 100%;
    background: #EEEEEE;
}
input:focus{
    outline: none;
    box-shadow: none;
}
.border-top{
    border-top: solid 1px #E5E5E5;
}
</style>