import {DEVIS_DETAIL_LOAD, DEVIS_DETAIL_GET, DEVIS_DETAIL_SET, DEVIS_DETAIL_UPDATE_ORDER_STATE, DEVIS_DETAIL_SET_ORDER_STATE, DEVIS_DETAIL_GET_FACTURATION, DEVIS_DETAIL_SET_FACTURATION, DEVIS_DETAIL_NEW_FACTURATION, DEVIS_DETAIL_LOAD_FACTURATION, DEVIS_DETAIL_CREATE_FACTURATION, DEVIS_DETAIL_UPDATE_FACTURATION, DEVIS_DETAIL_REMOVE_FACTURATION, DEVIS_DETAIL_UNSET_FACTURATION, DEVIS_DETAIL_LOAD_ORDER_DOCUMENTS, DEVIS_DETAIL_SET_ORDER_DOCUMENTS, DEVIS_DETAIL_GET_ORDER_DOCUMENTS, DEVIS_DETAIL_UNSET_ORDER_DOCUMENT, DEVIS_DETAIL_UPLOAD_DOCUMENT, DEVIS_DETAIL_REMOVE_DOCUMENT, DEVIS_DETAIL_GET_DOCUMENT_URL} from '../types/types'

export const devisdetail= {
    namespaced:true,
    state: {
        order:{},
        facturation:[],
        order_documents:[],
    },
    mutations: {
        [DEVIS_DETAIL_SET]:(state,order)=>state.order=order,
        [DEVIS_DETAIL_SET_ORDER_STATE]:(state,order_state_id)=>state.order.order_state_id=order_state_id,
        [DEVIS_DETAIL_SET_FACTURATION]:(state,facturation)=>state.facturation=facturation,
        [DEVIS_DETAIL_UPDATE_FACTURATION]:(state,params)=>{
        
          let item=state.facturation.filter(obj=>obj[params.id]==params.idValue);
          console.log(item);
          item[0][params.fieldName]=params.newValue;
        },
        [DEVIS_DETAIL_UNSET_FACTURATION]:(state,order_invoice_id)=>{
        
          state.facturation=state.facturation.filter(obj=>obj.id!=order_invoice_id);
       
        },
        [DEVIS_DETAIL_SET_ORDER_DOCUMENTS]:(state,documents)=>{
          state.order_documents=documents;
        },
        [DEVIS_DETAIL_UNSET_ORDER_DOCUMENT]:(state,id)=>{
          console.log(id);
          state.order_documents=state.order_documents.filter(obj=>obj.id!=id);
        }
    },
    actions: {
        [DEVIS_DETAIL_LOAD]:async({commit},order_id)=>{
            return axios.post(`/get-order-detail`,{order_id:order_id}).then((response)=>{
                commit(DEVIS_DETAIL_SET,response.data);
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });
        },
        [DEVIS_DETAIL_LOAD_FACTURATION]:async({commit,state})=>{
            return axios.post(`/get-order-detail-facturation`,{order_id:state.order.id}).then((response)=>{
                commit(DEVIS_DETAIL_SET_FACTURATION,response.data);
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });
        },

        [DEVIS_DETAIL_UPDATE_ORDER_STATE]:async({commit,state},order_state_id)=>{
            return axios.post(`/set-order-state`,{order_id:state.order.id,order_state_id:order_state_id}).then((response)=>{
                commit(DEVIS_DETAIL_SET_ORDER_STATE,order_state_id);
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });
        },
        [DEVIS_DETAIL_NEW_FACTURATION]:async({commit,state},params)=>{
            params.order_id=state.order.id;
            return axios.post(`/new-order-invoice`,params).then((response)=>{
            
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });
        },
        [DEVIS_DETAIL_CREATE_FACTURATION]:async({commit,state},facture)=>{

          return axios.post(`/validate-order-invoice`,facture).then((response)=>{
              commit(`${DEVIS_DETAIL_UPDATE_FACTURATION}`,{id:'id',idValue:facture.id,fieldName:'facturer',newValue:1});
              commit(`${DEVIS_DETAIL_UPDATE_FACTURATION}`,{id:'id',idValue:facture.id,fieldName:'invoice_type_name',newValue:'FACTURE'});
              return  Promise.resolve(response);
                    
            }).catch((error)=>{
              return  Promise.resolve(error);
            });
      },
      [DEVIS_DETAIL_REMOVE_FACTURATION]:async({commit,state},facture)=>{

        return axios.post(`/remove-order-invoice`,facture).then((response)=>{
            commit(`${DEVIS_DETAIL_UNSET_FACTURATION}`,facture.id);

            return  Promise.resolve(response);
                  
          }).catch((error)=>{
            return  Promise.resolve(error);
          });
    },
      
    [DEVIS_DETAIL_LOAD_ORDER_DOCUMENTS]:async({commit,state},order_id)=>{

      return axios.post(`/load-order-documents`,{order_id:order_id}).then((response)=>{
          commit(`${DEVIS_DETAIL_SET_ORDER_DOCUMENTS}`,response.data);

          return  Promise.resolve(response);
                
        }).catch((error)=>{
          return  Promise.resolve(error);
        });
  },
  [DEVIS_DETAIL_UPLOAD_DOCUMENT]:async({commit,state},formData)=>{
    formData.append('order_id',state.order.id);
    return axios.post(`/upload-order-document`,formData,{
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }).then((response)=>{

       return dispatch(`${DEVIS_DETAIL_LOAD_ORDER_DOCUMENTS}`,state.order.id).then(resp2=>{
          return  Promise.resolve(response);
        }).finally(resp2=>{
          return  Promise.resolve(response);
        });

     
              
      }).catch((error)=>{
        return  Promise.resolve(error);
      });
},
[DEVIS_DETAIL_REMOVE_DOCUMENT]:async({commit,state},document_id)=>{

  return axios.post(`/remove-order-document`,{order_document_id:document_id}).then((response)=>{
      commit(`${DEVIS_DETAIL_UNSET_ORDER_DOCUMENT}`,document_id);

      return  Promise.resolve(response);
            
    }).catch((error)=>{
      return  Promise.resolve(error);
    });
},
[DEVIS_DETAIL_GET_DOCUMENT_URL]:async({commit,state},document_id)=>{
  console.log(document_id);
  return axios.post(`/get-order-document-url`,{order_document_id:document_id}).then((response)=>{
      window.location=`${response.data.document_url}`;

      return  Promise.resolve(response);
            
    }).catch((error)=>{
      return  Promise.resolve(error);
    });
},
    },
    getters: {
        [DEVIS_DETAIL_GET]: state => state.order,
        [DEVIS_DETAIL_GET_FACTURATION]:state=>state.facturation,
        [DEVIS_DETAIL_GET_ORDER_DOCUMENTS]:state=>state.order_documents,
    }
}