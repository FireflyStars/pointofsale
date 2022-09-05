<template>
   <transition enter-active-class="animate__animated animate__fadeIn" leave-active-class="animate__animated animate__fadeOut">
       <div class="ractions d-flex justify-content-between gap-3" @click.stop=""> 
        <span title="Éditer" v-if="edit">
        <icon name="edit" width="20" height="20" @click.stop="iconClicked('edit')"/>
        </span>
        <span title="Supprimer" class="pr-0" v-if="delete">
        <icon name="trash-x" width="24" height="24" @click.stop="iconClicked('delete')"/>
        </span>
        <span title="Dupliquer" v-if="duplicate">
          <icon name="duplicate" width="20" height="20" @click.stop="iconClicked('duplicate')"/>
        </span>
       </div>
   </transition>

</template>

<script>
    import {computed, onMounted, ref,watch} from 'vue';
    import ICON from '../Icon.vue';

    export default {
        name: "ItemListRowActions",
        props:{
            item:{
                required:true,
                type:Object
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
                context.emit('onRowEditClick',props.item);
                if(name=='delete')
                context.emit('onRowDeleteClick',props.item);
                if(name=='duplicate')
                context.emit('onRowDuplicateClick',props.item);
            }

            return {
                iconClicked
            }
        }
    }
</script>

<style scoped>
.ractions span{
    background: white;
    padding: 2px 4px;
    border-radius: 3px;
    transition: background ease-in-out 0.2s;
}
 .ractions span.pr-0{
    padding-right: 0;
 }
  .ractions span:hover{
    background: var(--lcdtOrange);
  }
</style>
