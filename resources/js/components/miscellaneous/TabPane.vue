<template>
    <div class="tp-tabs">
        <div v-for="(tab,index) in tabs" :key="index" class=" tp-tab" :class="classes(index)"  @click="show(index)">{{tab}}</div>
    </div>
    <div>
        <template v-for="(tab,index) in tabs" :key="index">
            <transition
                enter-active-class="animate__animated animate__fadeIn"
            >
                <div v-if="currenttab==index" class="tab-view">
                    <slot :name="index" ></slot>
                </div>
            </transition>
        </template>
    </div>
</template>

<script>
    import {ref} from 'vue';
    export default {
        name: "TabPane",
        props:['tabs','current','class'],
        setup(props){
           
            const currenttab=ref('');
            currenttab.value=props.current;
            function show(index){
                currenttab.value=index;
            }

            function classes(index){
              
               
                if(currenttab.value==index){
                    return `${props.class} active`;
                }
                return props.class;
            }
            return {
                classes,
                show,
                currenttab
            }
        }
    }
</script>

<style scoped>
    .tp-tabs{
        display: flex;
        padding-left: 5px;
    }
 .tp-tab{
     min-width: 150px;
     height: 40px;
     padding: 10px;
     text-align: center;
     line-height: 20px;
     background-color: #EEEEEE;
     margin-right: 8px;
     cursor: pointer;
     border-radius: 6px 6px 0px 0px;
     font-size: 16px;
 }
 .tp-tab.active{
      background-color: #FFF;
      font-weight: bold;
      color:var(--lcdtOrange);
      position: relative;
      bottom:2px;
  }
    .tab-view{
        min-height: 550px;
        align-items: center;justify-content: center;
        position: relative;
    }

</style>