<template>
    <div class="d-flex align-items-center">
        <div class="col-6 fw-bold text-nowrap text-center" :class="'font-'+size">{{ amount }} {{ symbol }}</div> 
        <div class="col-2 mx-2" v-if="arrow > 0">
            <svg width="13" :height="size" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66509 16.8571C6.19273 16.8571 5.80981 16.4742 5.80981 16.0018L5.80981 1.98106C5.80981 1.50871 6.19273 1.1258 6.66509 1.1258C7.13744 1.1258 7.52036 1.50871 7.52036 1.98106L7.52036 16.0018C7.52036 16.4742 7.13744 16.8571 6.66509 16.8571Z" fill="#42A71E"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.1573 7.47208C11.8233 7.80608 11.2817 7.80608 10.9477 7.47208L6.10958 2.63394C5.77558 2.29994 5.77558 1.75841 6.10958 1.42441C6.44359 1.0904 6.98512 1.0904 7.31912 1.42441L12.1573 6.26254C12.4913 6.59655 12.4913 7.13807 12.1573 7.47208Z" fill="#42A71E"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.19876 7.4368C0.864758 7.10279 0.864758 6.56127 1.19876 6.22726L6.0369 1.38913C6.3709 1.05513 6.91243 1.05513 7.24643 1.38913C7.58043 1.72313 7.58043 2.26466 7.24643 2.59866L2.4083 7.4368C2.07429 7.7708 1.53276 7.7708 1.19876 7.4368Z" fill="#42A71E"/>
            </svg>
        </div> 
        <div class="mx-2 col-2" v-else>
            <svg width="13" :height="size" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66509 16.8571C6.19273 16.8571 5.80981 16.4742 5.80981 16.0018L5.80981 1.98106C5.80981 1.50871 6.19273 1.1258 6.66509 1.1258C7.13744 1.1258 7.52036 1.50871 7.52036 1.98106L7.52036 16.0018C7.52036 16.4742 7.13744 16.8571 6.66509 16.8571Z" fill="#EB5757"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.1573 7.47208C11.8233 7.80608 11.2817 7.80608 10.9477 7.47208L6.10958 2.63394C5.77558 2.29994 5.77558 1.75841 6.10958 1.42441C6.44359 1.0904 6.98512 1.0904 7.31912 1.42441L12.1573 6.26254C12.4913 6.59655 12.4913 7.13807 12.1573 7.47208Z" fill="#EB5757"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.19876 7.4368C0.864758 7.10279 0.864758 6.56127 1.19876 6.22726L6.0369 1.38913C6.3709 1.05513 6.91243 1.05513 7.24643 1.38913C7.58043 1.72313 7.58043 2.26466 7.24643 2.59866L2.4083 7.4368C2.07429 7.7708 1.53276 7.7708 1.19876 7.4368Z" fill="#EB5757"/>
            </svg>
        </div> 
        <div class="text-nowrap text-center" :class="'font-'+size">
            <em>{{ percent }} %</em>
        </div>
    </div>
</template>
<script>
import { ref } from 'vue';

export default {
    props:{
        arrow: {
            type: Number,
            default: 1
        },
        amount:{
            type: String,
            default: ''
        },
        symbol: {
            type: String,
            default: '€'
        },
        percent:{
            type: Number,
            default: ''
        },
        size: {
            type: Number,
            default: 20
        }
    },
    setup(props){
        const arrow = ref(props.arrow);
        const amount = ref(props.amount);
        const percent = ref(props.percent);
        const size = ref(props.size);
        return {
            arrow,
            amount,
            percent,
            size
        }
    }
}
</script>
<style lang="scss" scoped>

</style>