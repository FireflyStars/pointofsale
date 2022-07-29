import {  COMMANDE_LOAD_TAB, COMMANDE_SET_LIST, COMMANDE_LIST_MODULE, GET_COMMANDE_LIST_DEF } from "../types/types";

export const commande = {

    namespaced:true,

    state: {
      
        table_def: {
            store:{
              MODULE: COMMANDE_LIST_MODULE,
              INIT: COMMANDE_LOAD_TAB,
            },
          
            translations:{
              group_item: 'COMMANDE',
              group_items: 'COMMANDE',
              footer_item: 'ITEM',
              footer_items: 'ITEMS',
              no_batch_action: "Aucune action par lot n'est disponible.",
            },
            // highlight_row:{
            //       where: [
            //         { col: 'id', value: 10 }, //conditions to higlight rows
            //         { col: 'nbheure', value: 6 }
            //       ], 
            //       backgroundColor: '#f7c5af',
            //       color: '#fd3b35'
            //     }
            // ,
            item_route_name: "commande-details",// the route to trigger when a line is click 
            max_per_page: 15,//required          
            identifier: "COMMANDE_all",//required
            filter: true,// required boolean
            rearrange_columns: true,// required boolean
            columns_def: [
                {
                    id:"id",
                    display_name:"",
                    type:"checkbox",
                    class:"",
                    header_class:"",
                    sort:false,
                    filter:false,
                    css:{
                      flex:0.5
                    },
                  } , 
               {
                 id:"id",
                 display_name:"No COMMANDE",
                 type:"string",
                 class:"",
                 header_class:"",
                 sort:true,
                 filter:true,
                 table:'orders',
                 prefix:"",
                 suffix:"",
               },     
               
              {
                id:"customer",
                display_name:"CLIENT",
                type:"string",
                class:"",
                header_class:"",
                sort:true,
                filter:true,   
                having:true,
                prefix:"",
                suffix:"",
              },
              {
                id:"contact",
                display_name:"CONTACT",
                type:"html",
                class:"",
                header_class:"",
                sort:true,
                filter:true,   
                having:true,
                prefix:"",
                suffix:"",
                allow_groupby:true,
              },
              {
                id:"address",
                display_name:"CHANTIER",
                type:"html",
                class:"",
                header_class:"",
                sort:true,
                filter:true,   
                having:true,
                prefix:"",
                suffix:"",
              },
              {
                id:"created_at",
                display_name:"DATE CREATION",
                type:"date",
                format:"DD/MM/YY",
                class:"",
                header_class:"",
                sort:true,
                filter:true,   
                table:'orders',
                allow_groupby:true,
              },
              {
                id:"updated_at",
                display_name:"Mis à jour",
                type:"date",
                format:"DD/MM/YY",
                class:"",
                header_class:"",
                sort:true,
                filter:true,   
                table:'orders',
                allow_groupby:true,
              },
              {
                id:"responsable",
                display_name:"Responsable",
                type:"string",
                class:"",
                header_class:"",
                sort:true,
                filter:true,   
                having:true,
                filter_options:[
                  { id: 'John Doe', value: 'John Doe'},
                  { id: 'superadmin', value: 'superadmin'},
      
              ],
                prefix:"",
                suffix:"",
                allow_groupby:true,
              },
              {
                id:"order_state_id",
                display_name:"Statut",
                type:"component",
                class:"",
                header_class:"",
                sort:true,
                filter:true,   
                filter_options:'/get-order-states-formatted',
                allow_groupby:true,
              },
              {
                id:"action_co",
                display_name:"ACTION CO",
                type:"html",
                class:"",
                header_class:"",
                sort:true,
                filter:true,   
                having:true,
                prefix:"",
                suffix:"",

              },
              {
                id:"nbheure",
                display_name:"MO",
                type:"number",
                class:"justify-content-center",
                header_class:"",
                sort:true,
                filter:true, 
                having:true,
                prefix:"",
                suffix:" hr",
                group_total:true,  
                footer_total:true
              },
              {
                id:"total",
                display_name:"MONTANT",
                type:"price",
                class:"justify-content-center",
                header_class:"",
                sort:true,
                filter:true,   
                group_total:true,
                footer_total:true,
                prefix:"",
                suffix:"",  
 
              },
            ]

        },
        
    },

    getters: {

      commandeList: state => state.table_def,

    },

    mutations: {

      [COMMANDE_SET_LIST]:(state,list)=>{
        state.list = list
      }

    },

    actions: {

        [COMMANDE_LOAD_TAB]:async({ commit, state, dispatch }, params)=>{
  
          params.myparam = 1
          return axios.post(`/get-commande-list`, params).then((response)=>{
            return  Promise.resolve(response);
                  
          }).catch((error)=>{
            return  Promise.resolve(error);
          })
        }

    },
    
}