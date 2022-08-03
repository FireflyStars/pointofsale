<template>
    <div class="d-flex align-items-center">
        <div class="col-6 fw-bold text-nowrap text-center" :class="'font-'+fontSize">{{ amount }} {{ symbol }}</div> 
        <div class="col-2 mx-2" v-if="arrow == 1">
            <svg width="13" :height="fontSize" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66509 16.8571C6.19273 16.8571 5.80981 16.4742 5.80981 16.0018L5.80981 1.98106C5.80981 1.50871 6.19273 1.1258 6.66509 1.1258C7.13744 1.1258 7.52036 1.50871 7.52036 1.98106L7.52036 16.0018C7.52036 16.4742 7.13744 16.8571 6.66509 16.8571Z" fill="#42A71E"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.1573 7.47208C11.8233 7.80608 11.2817 7.80608 10.9477 7.47208L6.10958 2.63394C5.77558 2.29994 5.77558 1.75841 6.10958 1.42441C6.44359 1.0904 6.98512 1.0904 7.31912 1.42441L12.1573 6.26254C12.4913 6.59655 12.4913 7.13807 12.1573 7.47208Z" fill="#42A71E"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.19876 7.4368C0.864758 7.10279 0.864758 6.56127 1.19876 6.22726L6.0369 1.38913C6.3709 1.05513 6.91243 1.05513 7.24643 1.38913C7.58043 1.72313 7.58043 2.26466 7.24643 2.59866L2.4083 7.4368C2.07429 7.7708 1.53276 7.7708 1.19876 7.4368Z" fill="#42A71E"/>
            </svg>
        </div> 
        <div class="mx-2 col-2" v-else-if="arrow == -1">
            <svg width="13" :height="fontSize" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.33516 0.142822C6.80751 0.142822 7.19043 0.52574 7.19043 0.998092L7.19043 15.0189C7.19043 15.4912 6.80751 15.8741 6.33516 15.8741C5.86281 15.8741 5.47989 15.4912 5.47989 15.0189L5.47989 0.998092C5.47989 0.52574 5.86281 0.142822 6.33516 0.142822Z" fill="#FF0000" fill-opacity="0.7"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.842987 9.52786C1.17699 9.19386 1.71852 9.19386 2.05252 9.52786L6.89066 14.366C7.22466 14.7 7.22466 15.2415 6.89066 15.5755C6.55666 15.9095 6.01513 15.9095 5.68112 15.5755L0.842987 10.7374C0.508983 10.4034 0.508983 9.86187 0.842987 9.52786Z" fill="#FF0000" fill-opacity="0.7"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.8015 9.5632C12.1355 9.89721 12.1355 10.4387 11.8015 10.7727L6.96335 15.6109C6.62934 15.9449 6.08782 15.9449 5.75381 15.6109C5.41981 15.2769 5.41981 14.7353 5.75381 14.4013L10.5919 9.5632C10.926 9.2292 11.4675 9.2292 11.8015 9.5632Z" fill="#FF0000" fill-opacity="0.7"/>
            </svg>
        </div> 
        <div class="mx-2 col-2" v-else>
        </div> 
        <div class="text-nowrap text-center" :class="'font-'+fontSize">
            <em>{{ percent }}</em>
        </div>
    </div>
</template>
<script>
import { ref } from 'vue';
export default {
    props:{
        amount:{
            type: Number,
            default: ''
        },
        pastAmount:{
            type: Number,
            default: ''
        },
        symbol: {
            type: String,
            default: '€'
        },
        fontSize: {
            type: Number,
            default: 18
        }
    },
    setup(props){
        const amount = ref(props.amount);
        const pastAmount = ref(props.pastAmount);
        const fontSize = ref(props.fontSize);
        const symbol = ref(props.symbol);
        const percent = ref('');
        const arrow = ref(1);
        if(amount > pastAmount && pastAmount != 0){
            percent.value = ((amount/pastAmount)*100).toFixed(0)+"%";
            arrow.value = 1;
        }
        else if(amount == pastAmount){
            percent.value = '0%'
            arrow.value = 0
        }
        else if(amount < pastAmount){
            percent.value = ((amount/pastAmount)*100).toFixed(0)+"%";
            arrow.value = -1;
        }
        else if(pastAmount == 0){
            percent.value = '--'
            arrow.value = 0;
        }
        return {
            arrow,
            amount,
            percent,
            fontSize,
            symbol
        }
    }
}
</script>
<style lang="scss" scoped>

</style>