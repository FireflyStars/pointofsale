<template>
    <div class="position-relative">
        <input v-model="query" type="text" @keyup.prevent="submit">
        <div class="search-result bg-white" v-if="showSearchResult">
            <ul class="m-0 p-0">
                <li class="item p-2" v-for="(order, index) in orderList" :key="index" @click="selectOrder(order.id)">
                    <div class="d-flex">
                        <div class="col-4 fw-bold">{{ order.id }}</div>
                        <div class="col-8 fw-bold">{{ order.name }}</div>
                    </div>
                    <div class="fw-bold">{{ order.raisonsocial }}</div>
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
        // watch(()=>userId.value, (cur_val, pre_val)=>{
        //     emit('update:modelValue', userId.value);
        // })
        const timeout =ref('');
        const orderList = ref([
            {
                id: 12456,
                raisonsocial: 'CLIENT XXXXXXXX',
                name: 'Batiment XXX',
            },
            {
                id: 12455,
                raisonsocial: 'CLIENT XXXXXXXX',
                name: 'Batiment XXX',
            },
        ]);
        const selectOrder = (orderId)=>{
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
            selectOrder,
            submit,
        }
    },
})
</script>

<style scoped>
input{
    width: 100%;
    height: 40px;
    border: 0.5px solid #C3C3C3;
    border-radius: 5px;
    text-indent: 10px;
}
input:focus{
    outline: none;
    box-shadow: none;
}
.search-result{
    position: absolute;
    width: 100%;
}
ul{
    list-style: none;
}
.item{
    background: #F8F8F8;
    border-radius: 5px;
    cursor: pointer;
}
.item+.item{
    margin-top: 10px;
}
</style>