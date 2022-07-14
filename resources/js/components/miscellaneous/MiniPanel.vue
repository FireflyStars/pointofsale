<template>
<div class="minipanel">
  <div v-if="title!=''" class="row">
    <div class="col almarai_700_normal font-14 lcdtgrey d-flex align-items-center">{{title}}</div>
  </div>
  <slot></slot>
</div>
</template>

<script>
export default {
        name: "MiniPanel",
        props:{
          title:{
            type:String,
            required:false
          }
        },
        setup(){

        }
}
</script>

<style lang="scss" scoped>
.minipanel{
    background: #F8F8F8;
    display: block;
    margin-bottom:22px;
    padding:10px 20px;
    box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.25);

.lcdtgrey{
    color:var(--lcdtGrey);
}
}

</style>