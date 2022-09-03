<template>
   <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
       <div class="ractions d-flex justify-content-between gap-3"> 
        <icon name="edit" width="20" height="20" @click="iconClicked('edit')"/>
        <icon name="trash-x" width="24" height="24" @click="iconClicked('delete')"/>
          <icon name="copy" width="24" height="24" @click="iconClicked('duplicate')"/>
       </div>
   </transition>

</template>

<script>
    import {computed, onMounted, ref,watch} from 'vue';
    import ICON from '../Icon.vue';

    export default {
        name: "ItemListRowActions",
        props:{
            item_id:{
                required:true,
                type:Number
            },
   
            edit:{
                type:Boolean,
                default:false
            },
            delete:{
                type:Boolean,
                default:false
            },
            duplicate:{
                type:Boolean,
                default:false
            }
        },
        components:{ICON},
        
        emits: ['onRowEditClick','onRowDeleteClick','onRowDuplicateClick'],    
        setup(props,context){

            const iconClicked=(name)=>{
                if(name=='edit')
                context.emit('onRowEditClick',props.id);
                if(name=='delete')
                context.emit('onRowDeleteClick',props.id);
                if(name=='duplicate')
                context.emit('onRowDuplicateClick',props.id);
            }

            return {
                iconClicked
            }
        }
    }
</script>

<style scoped>

  
</style>
