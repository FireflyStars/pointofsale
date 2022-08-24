import { 
    SELECT_USER,
    LOAD_USER_MESSAGE,
    GET_SELECTED_USERINFO,
    GET_USERS,
    GET_USER_MESSAGE,
    SET_USER_MESSAGE,
 } from '../types/types';
 import axios from 'axios';

export const chat = {
    namespaced: true,
    state: {
        messages:[],
        selectedUserInfo: {},
        userList: [
            {
                id: 1,
                name: 'William Wright',
                imgUrl: '../../images/user-img.jpg',
                lastMessage: 'Hey, Marshall! How are you? Can you please change the color theme of the website to pink and purple?',
                lastMessageTime: '08:45 PM',
                unReadCnt: 3,
                isTyping: false,
                online: false,
            },
            {
                id: 2,
                name: 'Ollie Chandler',
                imgUrl: '../../images/user-img2.jpg',
                lastMessage: 'Hey, Marshall! How are you? Can you please change the color theme of the website to pink and purple?',
                lastMessageTime: '08:45 PM',
                unReadCnt: 0,
                isTyping: true,
                online: true,
            },
        ],
    },
    mutations: {
        [SELECT_USER]: (state, payload)=>{
            state.selectedUserInfo = state.userList.find((user)=>{ return user.id == payload });
        },
        [SET_USER_MESSAGE]: (state, payload)=>{
            state.messages[payload.userId] = payload.mesages;
        },
    },
    actions:{
        [SELECT_USER]: ( { commit, state }, payload )=>{
            // select the user
            commit(SELECT_USER, payload);
        },
        [LOAD_USER_MESSAGE]: ( { commit }, payload )=>{
            axios.post('/get-user-messages', { userId: payload }).then((res)=>{
                commit(SET_USER_MESSAGE, { userId: payload, mesages: res.data});
            }).catch((error)=>{

            }).finally(()=>{

            })
            commit(SELECT_USER, payload)
        },
    },
    getters:{
        [GET_SELECTED_USERINFO] : (state)=>{
            return state.selectedUserInfo;
        },
        [GET_USERS] : (state)=>{
            return state.userList;
        },
        [GET_USER_MESSAGE] : (state)=>{
            return state.messages[state.selectedUserInfo.id] ?? [];
        }
    },
    
};