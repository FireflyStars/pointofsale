<template>
    <Teleport to="body">
        <div class="search-layer d-flex align-items-center justify-content-center position-fixed" v-if="showModal">
            <transition name="list" appear>
                <div class="search-panel m-auto bg-white">
                    <div class="search-header d-flex align-items-center justify-content-center position-relative almarai-extrabold font-22">
                        <svg @click="closeModal" class="close-icon cursor-pointer" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.78812 5.2973C6.3976 4.90481 5.76444 4.90481 5.37392 5.2973C4.98339 5.6898 4.98339 6.32616 5.37392 6.71865L10.5883 11.9594L5.29289 17.2816C4.90237 17.6741 4.90237 18.3105 5.29289 18.703C5.68341 19.0955 6.31657 19.0955 6.7071 18.703L12.0025 13.3808L17.293 18.6979C17.6835 19.0904 18.3166 19.0904 18.7072 18.6979C19.0977 18.3054 19.0977 17.6691 18.7072 17.2766L13.4167 11.9594L18.6261 6.7237C19.0167 6.33121 19.0167 5.69485 18.6261 5.30235C18.2356 4.90986 17.6025 4.90986 17.2119 5.30235L12.0025 10.5381L6.78812 5.2973Z" fill="black"/>
                        </svg>
                    </div>
                    <div class="search-body rounded">
                        <div class="page-section">
                            <div class="d-flex">
                                <div class="col-4 d-flex align-items-center">
                                    <h3 class="m-0 mulish-extrabold font-22">ADRESSE</h3>
                                </div>
                                <div class="col-8 d-flex px-2">
                                    <div class="col-5">
                                        <select-box v-model="address.addressType" :options="addressTypes" :label="'TYPE ADRESSE'" :name="'addressType'"></select-box>
                                    </div>
                                    <div class="col-7"></div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-6 d-flex">
                                    <div class="col-7">
                                        <div class="form-group">
                                            <label for="">PRENOM / NOM BATIMENT</label>
                                            <input type="text" v-model="address.firstName" placeholder="FirstName" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-5 ps-3">
                                        <div class="form-group">
                                            <label for="">NOM</label>
                                            <input type="text" v-model="address.lastName" placeholder="Name" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 ps-3">
                                    <div class="form-group">
                                        <label for="">CENTRE DISTRIBUTEUR</label>
                                        <input type="text" placeholder="Adresse3" v-model="address.address3" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">LIBELLE ADRESSE</label>
                                        <input type="text" v-model="address.address1" placeholder="Address1" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6 ps-3 d-flex">
                                    <div class="col-8">
                                        <div class="form-group">
                                            <label for="">CODE POSTAL</label>
                                            <input type="text" ref="googleAddressInput" v-model="address.postcode" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-4 ps-3">
                                        <div class="form-group">
                                            <label for="">VILLE</label>
                                            <input type="text" v-model="address.city" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-3">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">LIBELLE SPECIFIQUE</label>
                                        <input type="text" v-model="address.address2" placeholder="Address2" class="form-control">
                                    </div>
                                </div>                                
                                <div class="col-6 ps-3 d-flex">
                                    <div class="address-map">
                                        <GoogleMap v-model:latitude="address.latitude" v-model:longitude="address.longitude"></GoogleMap>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                        <div class="btns mt-4 d-flex justify-content-between">
                            <button class="custom-btn btn-cancel" @click="closeModal">Annuler</button>
                            <button class="custom-btn btn-ok" @click="addNewAddress">AJOUTER ADRESSE</button>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </Teleport>
</template>
<script>

import {nextTick, onMounted, ref} from 'vue';
import axios from 'axios';
import SelectBox from '../../components/miscellaneous/SelectBox';
import GoogleMap from '../../components/miscellaneous/GoogleMap';
import {     
  DISPLAY_LOADER,
  HIDE_LOADER,
  LOADER_MODULE 
  } from '../../store/types/types';
import { useStore } from 'vuex';
  
export default {
    name: 'AddressModal',
    props: {
        // modelValue: Object
    },
    emits: ['addedNewAddress'],
    components:{
        SelectBox,
        GoogleMap
    },
    setup(props, { emit }){
        const store = useStore();
        const googleAddressInput = ref(null);
        const addressTypes = ref([]);
        const address = ref(
            {
                id: '',
                customerID: '',
                addressType: '',
                firstName: '',
                lastName: '',
                address1: '',
                address2: '',
                address3: '',
                postcode: '',
                city: '',
                latitude: 48.85560142492883,
                longitude: 2.3491914978706396,
            }
        );
        onMounted(()=>{
            axios.post('/get-list-info-for-customer').then((res)=>{
                addressTypes.value    = res.data.addressTypes;
            }).catch((errors)=>{
                console.log(errors);
            }).finally(()=>{

            })    
        })
        // google address autocomplete for delivery address
        const setAddress = ( address_components )=>{
            address_components.forEach(component => {
                const type = component.types[0];
                if(type == "postal_code"){
                    address.value.postcode = component.long_name
                }else if(type == "locality"){
                    address.value.city = component.long_name
                }else if(type == "street_number"){
                    form.value.address1 = component.long_name
                }
            });
        }        
        const closeModal = ()=>{
            showModal.value = !showModal.value;
        }
        const showModal = ref(false);
        const openModal = (id)=>{
            setTimeout(() => {
                const addr = new google.maps.places.Autocomplete(googleAddressInput.value);
                addr.addListener("place_changed", () => {
                    const place = addr.getPlace();
                    address.value.lat = place.geometry.location.lat();
                    address.value.lon = place.geometry.location.lng();
                    setAddress(place.address_components);
                });
            }, 100);
            address.value.customerID = id;
            showModal.value = !showModal.value;
        }  
        const addNewAddress = ()=>{
            // loading customer addresses
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, 'creating customer addresses..']);
            axios.post('/add-customer-address', address.value).then((res)=>{
                emit('addedNewAddress', {
                    id: res.data.id,
                    name: address.value.firstName + " " + address.value.lastName,
                    address1: address.value.address1,
                    address2: address.value.address2,
                    addressType: res.data.addressType,
                    postcode: address.value.postcode,
                    city: address.value.city,
                    lat: address.value.city,
                    lon: address.value.city,
                });
                showModal.value = false;                
            }).catch((error)=>{
                console.log(error);
            }).finally(()=>{
                store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
            })            
        }
        
        return {
            showModal,
            address,
            googleAddressInput,
            addressTypes,
            closeModal,
            openModal,
            addNewAddress,
        }
    }

}
</script>
<style lang="scss" scoped>
.list-enter-from{
    opacity: 0;
    transform: scale(0.6);
}
.list-enter-to{
    opacity: 1;
    transform: scale(1);
}
.list-enter-active{
    transition: all 1s ease;
}

.list-leave-from{
    opacity: 1;
    transform: scale(1);
}
.list-leave-to{
    opacity: 0;
    transform: scale(0.6);
}
.list-leave-active{
    transition: all 1s ease;
    position: absolute;
    width: 100%;
}
.list-move{
    transition:all 0.9s ease;
}
.address-map{
    min-width: 270px;
    min-height: 170px;
}
.search-layer{
    width: 100%;
    height: 100%;
    top: 0;
    z-index: 11;
    background: rgba(0, 0, 0, 0.3);
    .search-panel{
        width: 1020px;
        height: 477px;
        padding: 15px 80px;
        .search-header{
            .close-icon{
                position: absolute;
                top: 0;
                right: 0;
            }
        }
        .btns{
            .custom-btn{
                height: 40px;
                font-family: 'Almarai Bold';
                font-style: normal;
                font-weight: 700;
                font-size: 16px;
                line-height: 140%;
                border-radius: 4px;
                text-align: center;
                border: 1px solid #47454B;
                cursor: pointer;
            }
            .btn-cancel{
                color: rgba(0, 0, 0, 0.2);
            }
            .btn-ok{
                background: #A1FA9F;
                color: #3E9A4D;
            }
        }
    }
}
</style>