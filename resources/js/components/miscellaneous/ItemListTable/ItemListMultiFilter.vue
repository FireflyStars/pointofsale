<template>
    <item-list-multi-select 
                                        :id="id"
                                       title=""
                                        width="116px"
                                        tagBackground="var(--lcdtOrange)"
                                        tagColor="white"
                                        :numtag="1"
                                        classes="almarai_700_normal"
                                        :styles="{}" 
                                        dropdownClasses="almarai_700_normal"
                                        :dropdownStyles="{ width: '300px',maxHeight:'350px',overflowY:'scroll',zIndex:10001 }"
                                        :options="loadedoptions"
                                        transformOrigin="top center"
                                        @onUpdate="onupdate"
                                        :selectedOptions="value"
                                    />
                            
                     
</template>

<script >

import {computed, onMounted, ref} from 'vue';
import { useStore } from 'vuex';
import { ITEM_LIST_GET_FILTER_OPTIONS, ITEM_LIST_LOAD_FILTER_OPTIONS, ITEM_LIST_MODULE, ITEM_LIST_SET_FILTER_OPTIONS } from '../../../store/types/types';
import ItemListMultiSelect from './ItemListMultiSelect.vue';
export default {
    name: "ItemListMultiFilter",
    components:{
        ItemListMultiSelect
    },

    props:{
                id:{
                    type: String,
                    required: true
                },
                col:{
                    type:Object,
                    required:true
                },
                filteroptions:{
                    type:[Array,String],
                    required:true
                },
                identifier:{
                    type:String,
                    required:true
                },
                value:{
                    type:Array,
                    default:[]
                }
    },
    emits: ['onMultiFiltered'],
    setup(props,context){
        const options = ref([]);
        const store=useStore();
        onMounted(()=>{

            if(typeof props.filteroptions==='object')
            store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_SET_FILTER_OPTIONS}`,{data:props.filteroptions,id:props.id});
            // for (const i in props.filteroptions){
            //     let opt=props.filteroptions[i];
            //     opt.check=false;
            //     options.value.push(opt);
            // }
              if(typeof props.filteroptions==='string'){
                store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_LOAD_FILTER_OPTIONS}`,{url:props.filteroptions,id:props.id});
              }
        });

       const loadedoptions=computed(()=>{
           let opt=[];
           if(typeof store.getters[`${ITEM_LIST_MODULE}${ITEM_LIST_GET_FILTER_OPTIONS}`][props.identifier]!="undefined")
           if(typeof store.getters[`${ITEM_LIST_MODULE}${ITEM_LIST_GET_FILTER_OPTIONS}`][props.identifier][props.id]!="undefined")
           opt=store.getters[`${ITEM_LIST_MODULE}${ITEM_LIST_GET_FILTER_OPTIONS}`][props.identifier][props.id];
            return opt;
       });
        const onupdate=(options)=>{
            let values=[];
            for(const i in options){
                if(options[i].check==true)
                values.push(options[i].id);
            }
             context.emit('onMultiFiltered',{name:props.id,col:props.col,values:values});
         
        }
        return {
            onupdate,
          loadedoptions
        }
    }
}
</script>

<style lang="scss" scoped>

</style>