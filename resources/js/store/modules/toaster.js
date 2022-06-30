import {
    TOASTER_MESSAGE,
    TOASTER_GET_ALL,
    TOASTER_ADD_TOAST, TOASTER_REMOVE_TOAST, TOASTER_CLEAR_TOASTS
} from '../types/types';

export const toaster= {
    namespaced:true,

    state:{
        toast:[/*{
            message:"test success",
            ttl:5,//seconds
            type:'success'//success,danger
        },*/
        ],
        auto_id:0
    },
    mutations:{
        [TOASTER_ADD_TOAST]: (state, payload) => {
           // state.toast.unshift(payload);
            state.toast.push(payload);
            state.auto_id=payload.id;
            },
        [TOASTER_REMOVE_TOAST]:(state,payload)=>{
            state.toast=state.toast.filter((item)=>item.id!=payload);
        },
        [TOASTER_CLEAR_TOASTS]:(state)=>{
            state.toast=[];
        },
    },
    actions:{
        [TOASTER_MESSAGE]:({commit,state}, payload)=>{
            payload.id=state.auto_id+1;
            commit(TOASTER_ADD_TOAST,payload);
            if(payload.ttl>0)
            setTimeout(()=>{commit(TOASTER_REMOVE_TOAST,payload.id)},(payload.ttl*1000));
        },
    },
    getters:{
        [TOASTER_GET_ALL]:state=> state.toast,
    }
}