<template>
    <input type="text" ref="googleSearch" :placeholder="placeholder" v-model="address.street" class="form-control">
</template>
<script>
import { onMounted, ref } from 'vue';

export default {
    name: 'GoogleAdress',
    props: {
        address: {
            type: String,
            default: '',
        },
        index: {
            type: Number,
            default: 0,
        },
        placeholder: {
            type: String,
            default: ''
        }
    },
    emit:['updateAddressInfo' ],
    setup( props, { emit }){
        const address = ref({
            street: '',
            lat: '',
            lon: '',
            postcode: '',
            city: '',
            index: 0,
        });
        const placeholder = ref('');
        address.value.street = props.address;
        address.value.index = props.index;
        placeholder.value = props.placeholder;
        const googleSearch = ref(null);
        // google address autocomplete for delivery address
        const setAddress = ( address_components )=>{
            address.value.street = [
                ( address_components[0] && address_components[0].short_name ) || "",
                ( address_components[1] && address_components[1].short_name) || "",
                ( address_components[2] && address_components[2].short_name) || "",
            ].join(" ");
            address_components.forEach(component => {
                
                const type = component.types[0];
                if( type == "postal_code"){
                    address.value.postcode = component.long_name
                }
                if( type == "locality"){
                    address.value.city = component.long_name
                }
            });
            emit('updateAddressInfo', address.value);            
        }

        onMounted(()=>{
            setTimeout(() => {
                const addr = new google.maps.places.Autocomplete(googleSearch.value, 
                    { 
                        componentRestrictions: { country: "fr" },
                        fields: ["address_components", "geometry"],
                    }
                );
                addr.addListener("place_changed", () => {
                    const place = addr.getPlace();
                    address.value.lat = place.geometry.location.lat();
                    address.value.lon = place.geometry.location.lng();
                    setAddress(place.address_components);
                });
            }, 100);            
        })
        return {
            googleSearch,
            placeholder,
            address
        }
    }
}
</script>
<style lang="scss" scoped>

</style>>