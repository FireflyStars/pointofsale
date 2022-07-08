<template>
    <div class="h-100 w-100 googleMapToSetLatLon" ref="googleMapRef">

    </div>
</template>
<script>
import { ref, onMounted, watch } from 'vue';

export default {
    name: "GoogleMap",
    props: {
        latitude: '',
        longitude: '',
    },
    emits: ['update:latitude', 'update:longitude'],
    setup(props, { emit }){
        const googleMapRef = ref(null);
        let markers = [];
        var googleMap;
        const initMap = ()=>{
            const myLatlng = { lat: props.latitude, lng: props.longitude }; // Paris is set as center.
            googleMap = new google.maps.Map(googleMapRef.value, {
                zoom: 15,
                center: myLatlng,
            });
            addMarker(myLatlng, googleMap);
            googleMap.addListener("click", (mapsMouseEvent) => {
                removeMarkers(null);
                var latLng = mapsMouseEvent.latLng.toJSON();
                addMarker(latLng, googleMap);
                emit('update:latitude', latLng.lat);
                emit('update:longitude', latLng.lng);
            });            
        }
        watch(() => props.latitude, (current_val, previous_val) => {
            const myLatlng = { lat: props.latitude, lng: props.longitude }; // Paris is set as center.
            removeMarkers(null);
            googleMap.setCenter(myLatlng);
            addMarker(myLatlng, googleMap);
        });        
        const addMarker = (position, map)=>{
            const marker = new google.maps.Marker({
                position,
                map,
                title: "Lat: " + position.lat + ", " + "Lng: " + position.lng
            });
            markers.push(marker);
        }
        const removeMarkers = (map)=>{
            for (let i = 0; i < markers.length; i++) {
                markers[i].setMap(map);
            }
        }
        onMounted(()=>{
            initMap();
        })
        return {
            googleMapRef
        }
    }
}
</script>
<style lang="scss" scoped>

</style>