<template>
    <div class="h-100 w-100 googleMapToSetLatLon" ref="googleMap">

    </div>
</template>
<script>
import { ref, onMounted } from 'vue';

export default {
    name: "GoogleMap",
    props: {
        latitude: '',
        longitude: '',
    },
    emits: ['update:latitude', 'update:longitude'],
    setup(props, { emit }){
        const googleMap = ref(null);
        let markers = [];
        const initMap = ()=>{
            const myLatlng = { lat: props.latitude, lng: props.longitude }; // Paris is set as center.
            const map = new google.maps.Map(googleMap.value, {
                zoom: 7,
                center: myLatlng,
            });
            addMarker(myLatlng, map);
            map.addListener("click", (mapsMouseEvent) => {
                removeMarkers(null);
                var latLng = mapsMouseEvent.latLng.toJSON();
                addMarker(latLng, map);
                emit('update:latitude', latLng.lat);
                emit('update:longitude', latLng.lng);
            });            
        }
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
            googleMap
        }
    }
}
</script>
<style lang="scss" scoped>

</style>