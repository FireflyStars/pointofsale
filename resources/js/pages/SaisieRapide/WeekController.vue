<template>
    <div class="d-flex">
        <div class="d-flex week-controller">
            <div class="col-3 p-2 week-controller-item" @click="subWeek()">&lt;&lt;</div>
            <div class="col-3 p-2 week-controller-item d-flex justify-content-center" @click="subDay()">&lt;</div>
            <div class="col-3 p-2 week-controller-item d-flex justify-content-center" @click="addDay()">&gt;</div>
            <div class="col-3 p-2 week-controller-item" @click="addWeek()">&gt;&gt;</div>
        </div>
        <div class="ms-2 lcdtOrange fw-bold d-flex align-items-center">Semaine : {{ weekNumber }}</div>
    </div>
    <div class="d-flex days mt-3">
        <div class="day-item text-capitalize" v-for="(day, index) in days" :key="index" @click="selectDay(day)" :class="{ 'active': day.active }">{{day.day}} {{ day.date }}</div>
    </div>
</template>
<script>
    import moment from 'moment';
    import { ref, watch, onMounted } from 'vue';
    export default{
        name: 'WeekController',
        emits: ['DateSelected'],
        setup( props, { emit } ){
            var now = moment().locale('fr');
            const weekNumber = ref(now.week());
            const endOfWeek = ref(moment().locale('fr').endOf('week'));
            const selectedDate = ref(now.format('Y-MM-DD'));
            const days = ref([]);
            const getWeekDays = ()=>{
                days.value = [];
                weekNumber.value = endOfWeek.value.week();
                endOfWeek.value = endOfWeek.value.subtract(6, 'd')
                for (let index = 0; index <= 6; index++) {
                    if(index != 0){
                        var tmpDay = endOfWeek.value.add(1, 'd');
                    }else{
                        var tmpDay = endOfWeek.value;
                    }
                    days.value.push({
                        day: tmpDay.locale('fr').format('dddd'),
                        date: tmpDay.format('DD/MM'),
                        fullDate: tmpDay.format('Y-MM-DD'),
                        active: selectedDate.value == tmpDay.format('Y-MM-DD'),
                    });
                }

            }
            getWeekDays();
            onMounted(()=>{
                emit('DateSelected', now.format('Y-MM-DD'));
            })
            const selectDay = (day)=>{
                selectedDate.value = day.fullDate;
                emit('DateSelected', day.fullDate);
                days.value.forEach((item)=>{
                    if(item.day == day.day) {
                        item.active = true;
                    }else{
                        item.active = false;
                    }
                })
            }
            const subWeek = ()=>{
                endOfWeek.value = endOfWeek.value.startOf('week').subtract(1, 'd');
                getWeekDays();
            }
            const addWeek = ()=>{
                endOfWeek.value = endOfWeek.value.endOf('week').add(1, 'w');
                getWeekDays();
            }
            const addDay = ()=>{
                endOfWeek.value = endOfWeek.value.add(1, 'd');
                getWeekDays();
            }
            const subDay = ()=>{
                endOfWeek.value = endOfWeek.value.subtract(1, 'd');
                getWeekDays();
            }
            return{
                weekNumber,
                days,
                selectDay,
                subWeek,
                addWeek,
                addDay,
                subDay,
            }
        }
    }
</script>
<style scoped>
.week-controller{
    width: 160px;
    height: 40px;
    border-radius: 5px;
    overflow: hidden;
}
.week-controller-item{
    background: #e86f29;
    color: white;
    font-weight: bold;
    cursor: pointer;
    box-sizing: border-box;
}
.week-controller-item+.week-controller-item{
    border-left: solid 1px #E8581B;
}
.day-item{
    cursor: pointer;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    background: #EEEEEE;
    padding: 10px 20px;
    color: #47454B;
    font-size: 16px;
}
.day-item+.day-item{
    margin-left: 10px;
}
.day-item.active{
    background: white;
    color: #e86f29;
    font-weight: bold;
}
</style>