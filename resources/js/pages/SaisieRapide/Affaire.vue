<template>
    <div class="position-relative">
        <div class="search-select">
            <div class="search-select-input">
                <input v-model="query" type="text" @keyup.prevent="submit">
            </div>
            <div class="search-select-icon cursor-pointer" @click="showSearchResult = !showSearchResult"></div>
        </div>
        <div class="search-result bg-white" v-if="showSearchResult">
            <ul class="m-0 p-0">
                <li class="item p-2" v-for="(order, index) in orderList" :class="{'active': order.id == selectedOrderId}" :key="index" @click="selectOrder($event, order.id, index)">
                    <div class="d-flex">
                        <div class="col-3 fw-bold">{{ order.id }}</div>
                        <div class="col-3 fw-bold text-nowrap">{{ order.name }}</div>
                        <div class="col-3 fw-bold text-nowrap">{{ order.raisonsocial }}</div>
                        <div class="col-3 fw-bold text-nowrap">{{ order.firstname }} {{ order.lastname }}</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import { ref, watch} from 'vue';
export default({
    name: "Affaire",
    emit: ['update:modelValue'],
    setup(props,{ emit }){
        const query = ref('');
        const showSearchResult = ref(false);
        const selectedOrderId = ref(null);
        // watch(()=>userId.value, (cur_val, pre_val)=>{
        //     emit('update:modelValue', userId.value);
        // })
        const timeout =ref('');
        const orderList = ref([]);
        const selectOrder = (event, orderId, index)=>{
            selectedOrderId.value = orderId;
            document.querySelectorAll('.search-result ul li').forEach((item, itemIndex)=>{
                item.classList.remove('active');
                if(itemIndex != index){
                }
                else{
                    item.classList.add('active');
                }
            })
            emit('update:modelValue', orderId);
            showSearchResult.value = false;
        }
        const submit = (e) =>{
            clearTimeout(timeout.value);
            timeout.value = setTimeout(function() {
                axios.post('/search-saisie-order', { search: query.value }).then((res)=>{
                    orderList.value = res.data;
                    showSearchResult.value = true;
                }).catch((error)=>{
                    console.log(error);
                })
            }
            , 300)
        }

        return {
            query,
            showSearchResult,
            orderList,
            selectedOrderId,
            selectOrder,
            submit,
        }
    },
})
</script>

<style scoped>
/* width */
::-webkit-scrollbar {
  width: 9px;
}
/* Track */
::-webkit-scrollbar-track {
  background: #E0E0E0; 
}
/* Handle */
::-webkit-scrollbar-thumb {
  background: #47454B; 
  border-radius: 6px;
}
input{
    width: 100%;    
    border: none;
    height: 100%;
}
input:focus{
    outline: none;
    box-shadow: none;
}
.search-result{
    position: absolute;
    width: 250%;
    max-height: 200px;
    overflow-y: auto;
}
.search-select{
    width: 100%;
    height: 40px;    
    border: 0.5px solid #C3C3C3;
    border-radius: 5px;
    display: flex;
}
.search-select-input{
    width: calc(100% - 36px);
    text-indent: 16px;
}
.search-select-icon{
    width: 36px;
    height: 100%;
    display: flex;
    align-items: center;
    position: relative;    
}
.search-select-icon::before{
    content: " ";
    height: 3px;
    display: block;
    width: 13px;
    background: #868686;
    border-radius: 10px;
    transform: rotate(40deg);
    right: 22px;
    position: absolute;
}
.search-select-icon::after{
    content: " ";
    height: 3px;
    display: block;
    width: 13px;
    background: #868686;
    border-radius: 10px;
    transform: rotate(-40deg);
    position: absolute;
    right: 13px;
}
ul{
    list-style: none;
}
.item{
    cursor: pointer;
}
.item .col-3{
    text-overflow: ellipsis;
    overflow: hidden;
}
.item.active{
    border: solid 1px black;
}
.item:nth-child(2n+1){
    background: #F8F8F8;
}
</style>