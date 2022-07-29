import axios from 'axios';
import {
  COMMANDE_DETAIL_LOAD, 
  COMMANDE_DETAIL_GET,
  COMMANDE_DETAIL_SET, 
  COMMANDE_DETAIL_UPDATE_ORDER_STATE, 
  COMMANDE_DETAIL_SET_ORDER_STATE, 
  COMMANDE_DETAIL_GET_FACTURATION, 
  COMMANDE_DETAIL_SET_FACTURATION, 
  COMMANDE_DETAIL_NEW_FACTURATION, 
  COMMANDE_DETAIL_LOAD_FACTURATION, 
  COMMANDE_DETAIL_CREATE_FACTURATION, 
  COMMANDE_DETAIL_UPDATE_FACTURATION, 
  COMMANDE_DETAIL_REMOVE_FACTURATION, 
  COMMANDE_DETAIL_UNSET_FACTURATION, 
  COMMANDE_DETAIL_LOAD_ORDER_DOCUMENTS, 
  COMMANDE_DETAIL_SET_ORDER_DOCUMENTS, 
  COMMANDE_DETAIL_GET_ORDER_DOCUMENTS, 
  COMMANDE_DETAIL_UNSET_ORDER_DOCUMENT, 
  COMMANDE_DETAIL_UPLOAD_DOCUMENT, 
  COMMANDE_DETAIL_REMOVE_DOCUMENT, 
  COMMANDE_DETAIL_GET_DOCUMENT_URL,
  COMMANDE_DETAIL_LOAD_POINTAGE,
  COMMANDE_DETAIL_SET_POINTAGE,
  GET_PERSONNEL_LIST,
  SAVE_PERSONNEL_LIST,
  GET_POINTAGE_TYPES,
  SAVE_POINTAGE_TYPES,
  COMMANDE_CREATE_POINTAGE,
  UPDATE_POINTAGE
} from '../types/types'

export const commandeDetails = {

    namespaced:true,

    state: {
        order: {},
        facturation: [],
        order_documents: [],
        pointage: [],
        personnelList: [],
        pointageTypes: []
    },

    getters: {
        [COMMANDE_DETAIL_GET]: state => state.order,
        [COMMANDE_DETAIL_GET_FACTURATION]:state=>state.facturation,
        [COMMANDE_DETAIL_GET_ORDER_DOCUMENTS]:state=>state.order_documents,
        pointage: state => state.pointage,
        personnelList: state => state.personnelList,
        pointageTypes: state => state.pointageTypes,
    },

    mutations: {

        [COMMANDE_DETAIL_SET]:(state,order)=>state.order=order,

        [COMMANDE_DETAIL_SET_ORDER_STATE]:(state,order_state_id)=>state.order.order_state_id=order_state_id,

        [COMMANDE_DETAIL_SET_FACTURATION]:(state,facturation)=>state.facturation=facturation,

        [COMMANDE_DETAIL_UPDATE_FACTURATION]:(state,params)=>{
        
          let item=state.facturation.filter(obj=>obj[params.id]==params.idValue);
          console.log(item);
          item[0][params.fieldName]=params.newValue;

        },

        [COMMANDE_DETAIL_UNSET_FACTURATION]:(state,order_invoice_id)=>{
        
          state.facturation=state.facturation.filter(obj => obj.id != order_invoice_id);
       
        },

        [COMMANDE_DETAIL_SET_ORDER_DOCUMENTS]:(state,documents) => {
          state.order_documents=documents;
        },

        [COMMANDE_DETAIL_UNSET_ORDER_DOCUMENT]:(state,id)=>{
          state.order_documents=state.order_documents.filter(obj=>obj.id!=id);
        },

        [COMMANDE_DETAIL_SET_POINTAGE](state, data) {
          state.pointage = data
        },

        [SAVE_PERSONNEL_LIST](state, data) {
          state.personnelList = data
        },

        [SAVE_POINTAGE_TYPES](state, types) {
          state.pointageTypes = types
        },

        [UPDATE_POINTAGE](state, data) {
          state.pointage.push(data)
        }

    },

    actions: {

        async [COMMANDE_CREATE_POINTAGE]({ commit }, { pointage, affiliate_id, order_id, user_id }) {
          
          try {

            const { data } = await axios.post(`/create-pointage/${order_id}`, {
              ...pointage,
              affiliate_id,
              user_id,
            })

            commit(UPDATE_POINTAGE, data)

          }

          catch(e) {
            throw e
          }

        },


        async [GET_PERSONNEL_LIST]({ commit, state }, affiliate_id) {

          try {
            
            if(state.personnelList.length) return

            const { data } = await axios.get('/get-personnel-list', {
              params: {
                affiliate_id
              }
            })
            commit(SAVE_PERSONNEL_LIST, data)
          }

          catch(e) {
              throw e
          }

        },

        async [GET_POINTAGE_TYPES]({ commit, state }) {

          try {
            
            if(state.pointageTypes.length) return

            const { data } = await axios.get('/get-pointage-types')
            commit(SAVE_POINTAGE_TYPES, data)
          }

          catch(e) {
              throw e
          }

        },

        [COMMANDE_DETAIL_LOAD]:async({ commit }, order_id)=>{
            return axios.post(`/get-commande-details/${order_id}`).then((response)=>{
                commit(COMMANDE_DETAIL_SET, response.data)
                return Promise.resolve(response)
                      
              }).catch((error)=>{
                return Promise.resolve(error);
              })
        },

        [COMMANDE_DETAIL_LOAD_FACTURATION]:async({commit,state})=>{
            return axios.post(`/get-order-detail-facturation`,{order_id:state.order.id}).then((response)=>{
                commit(COMMANDE_DETAIL_SET_FACTURATION,response.data);
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });
        },

        [COMMANDE_DETAIL_LOAD_POINTAGE]:async({commit,state})=>{
            return axios.post(`/get-order-detail-pointage/${state.order.id}`).then((response) => {
                commit(COMMANDE_DETAIL_SET_POINTAGE, response.data)
                return  Promise.resolve(response)
                      
              }).catch((error)=>{
                return  Promise.resolve(error)
              });
        },

        [COMMANDE_DETAIL_UPDATE_ORDER_STATE]:async({commit,state},order_state_id)=>{
            return axios.post(`/set-order-state`,{order_id:state.order.id,order_state_id:order_state_id}).then((response)=>{
                commit(COMMANDE_DETAIL_SET_ORDER_STATE,order_state_id);
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });
        },

        [COMMANDE_DETAIL_NEW_FACTURATION]:async({commit,state},params)=>{
            params.order_id=state.order.id;
            return axios.post(`/new-order-invoice`,params).then((response)=>{
            
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });
        },

        [COMMANDE_DETAIL_CREATE_FACTURATION]:async({commit,state},facture)=>{

            return axios.post(`/validate-order-invoice`,facture).then((response)=>{
                commit(`${COMMANDE_DETAIL_UPDATE_FACTURATION}`,{id:'id',idValue:facture.id,fieldName:'facturer',newValue:1});
                commit(`${COMMANDE_DETAIL_UPDATE_FACTURATION}`,{id:'id',idValue:facture.id,fieldName:'invoice_type_name',newValue:'FACTURE'});
                commit(`${COMMANDE_DETAIL_UPDATE_FACTURATION}`,{id:'id',idValue:facture.id,fieldName:'invoice_id',newValue:response.data.invoice.id});
                commit(`${COMMANDE_DETAIL_UPDATE_FACTURATION}`,{id:'id',idValue:facture.id,fieldName:'ref',newValue:response.data.invoice.ref});
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });
        },

        [COMMANDE_DETAIL_REMOVE_FACTURATION]:async({commit,state},facture)=>{

            return axios.post(`/remove-order-invoice`,facture).then((response)=>{
                commit(`${COMMANDE_DETAIL_UNSET_FACTURATION}`,facture.id);

                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });
        },
      
        [COMMANDE_DETAIL_LOAD_ORDER_DOCUMENTS]:async({commit,state},order_id)=>{

            return axios.post(`/load-order-documents`,{order_id:order_id}).then((response)=>{
                commit(`${COMMANDE_DETAIL_SET_ORDER_DOCUMENTS}`,response.data);

                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });
        },

        [COMMANDE_DETAIL_UPLOAD_DOCUMENT]:async({commit,state},formData)=>{
          formData.append('order_id',state.order.id);
          return axios.post(`/upload-order-document`,formData,{
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }).then((response)=>{

            return dispatch(`${COMMANDE_DETAIL_LOAD_ORDER_DOCUMENTS}`,state.order.id).then(resp2=>{
                return  Promise.resolve(response);
              }).finally(resp2=>{
                return  Promise.resolve(response);
              });

          
                    
            }).catch((error)=>{
              return  Promise.resolve(error);
            });
      },

      [COMMANDE_DETAIL_REMOVE_DOCUMENT]:async({commit,state},document_id)=>{

        return axios.post(`/remove-order-document`,{order_document_id:document_id}).then((response)=>{
            commit(`${COMMANDE_DETAIL_UNSET_ORDER_DOCUMENT}`,document_id);

            return  Promise.resolve(response);
                  
          }).catch((error)=>{
            return  Promise.resolve(error);
          });
      },

      [COMMANDE_DETAIL_GET_DOCUMENT_URL]:async({commit,state},document_id)=>{
        console.log(document_id);
        return axios.post(`/get-order-document-url`,{order_document_id:document_id}).then((response)=>{
            window.location=`${response.data.document_url}`;

            return  Promise.resolve(response);
                  
          }).catch((error)=>{
            return  Promise.resolve(error);
          });
      },

    },

    
}