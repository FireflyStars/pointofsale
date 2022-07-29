<template>
   <item-list-date-picker v-model="from"  :col="col" :name="`from_${name}`" :disabledFromDate="disabledFromDate" :droppos="droppos" placeHolder="début" @changed="filterdate"></item-list-date-picker>
   <item-list-date-picker v-model="to" :col="col" :name="`to_${name}`" :disabledToDate="disabledToDate" :droppos="droppos" placeHolder="fin" @changed="filterdate"></item-list-date-picker>
</template>

<script>
import ItemListDatePicker from './ItemListDatePicker.vue';
import {ref, watch} from 'vue';

export default {
    name: "ItemListDateFilter",
    components:{
        ItemListDatePicker
    },

    props:{
                name:{
                    type: String,
                    required: true
                },
                col:{
                    type:Object,
                    required:true
                },
                value:{
                    type:Object,
                }
    },
    emits: ['onDateFiltered'],
    setup(props,context){
        const droppos=ref({top:"20px",right:'auto',bottom:'auto',left:'0',transformOrigin:'top center'});
        const disabledToDate=ref('');
        const disabledFromDate=ref('');
        const from=ref('');
        const to=ref('');
        const filterdate=(input)=>{
            if(input.name==`from_${props.name}`){
                disabledToDate.value=input.date;
            }
                  if(input.name==`to_${props.name}`){
                disabledFromDate.value=input.date;
            }
            context.emit('onDateFiltered',{name:props.name,col:props.col,date:{'from':disabledToDate.value,'to':disabledFromDate.value}});
        }

  
            from.value=props.value.From;
            disabledToDate.value=from.value;
            to.value=props.value.To;
            disabledFromDate.value=to.value;
        

        return {
            droppos,
            filterdate,
            disabledToDate,
            disabledFromDate,
            from,
            to
           
        }
    }
}
</script>

<style lang="scss" scoped>

</style>