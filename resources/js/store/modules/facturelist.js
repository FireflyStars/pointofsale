import {  FACTURELIST_LOAD_TAB, FACTURELIST_SET_LIST, FACTURE_LIST_MODULE, GET_FACTURE_LIST_DEF } from "../types/types";

export const facturelist= {
    namespaced:true,
    state: {
      
        table_def: {
            store:{
              MODULE:FACTURE_LIST_MODULE,//required
              INIT:FACTURELIST_LOAD_TAB,//required
            },
            // batch_actions:{
            //     delete:{
            //         name:"Delete",
            //         route:"DeleteDevis",
            //         type:'button'
            //     },
            //     status:{
            //         type:"component"
            //     }

            // },
            translations:{
              group_item:'facture',
              group_items:'factures',
              footer_item:'ITEM',
              footer_items:'ITEMS',
              no_batch_action:"Aucune action par lot n'est disponible.",
            },
            highlight_row:{
                  where:[
                    {col:'invoice_type_id',value:3}, //conditions to higlight rows
                    //{col:'nbheure',value:6}
                  ], 
                  backgroundColor:'#f7c5af',
                  color:'#fd3b35'
                }
            ,
            item_route_name:"FactureDetail",// the route to trigger when a line is click 
            max_per_page:10,//required          
            identifier:"facturelist_all",//required
            filter:true,// required boolean
            rearrange_columns:true,// required boolean
            columns_def:[
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
                 id:"reference",
                 display_name:"N° FACTURE",
                 type:"string",
                 class:"",
                 header_class:"",
                 sort:true,
                 filter:true,
                 table:'invoices',
                 prefix:"",
                 suffix:"",
               },     
               {
                id:"order_id",
                display_name:"N° Commande",
                type:"string",
                class:"",
                header_class:"",
                sort:true,
                filter:true,
                table:'invoices',
                prefix:"",
                suffix:"",
                allow_groupby:true,
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
                display_name:"Contact",
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
                id:"dateecheance",
                display_name:"Date échéance",
                type:"date",
                format:"DD/MM/YY",
                class:"",
                header_class:"",
                sort:true,
                filter:true,   
                table:'invoices',
                allow_groupby:true,
              },
             
       
              {
                id:"invoice_state_id",
                display_name:"Statut",
                type:"component",
                class:"",
                header_class:"",
                sort:true,
                filter:true,   
                filter_options:'/get-invoice-states-formatted',
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
                id:"pourcentage",
                display_name:"Pourcentage",
                type:"number",
                class:"justify-content-center",
                header_class:"",
                sort:true,
                filter:true,   
                group_total:true,
                footer_total:true,
                prefix:"",
                suffix:"%",  
                tofixed:2,
 
              },
              {
                id:"montant",
                display_name:"Facturer",
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
              {
                id:"payer",
                display_name:"Payer",
                type:"price",
                class:"justify-content-center",
                header_class:"",
                sort:true,
                filter:true,   
                group_total:true,
                footer_total:true,
                prefix:"",
                suffix:"",  
                having:true,
 
              },
            ]

            

        },
        
    },
    mutations: {
      [FACTURELIST_SET_LIST]:(state,list)=>{
        state.list=list;
      }
    },
    actions: {
      [FACTURELIST_LOAD_TAB]:async({commit,state,dispatch},params)=>{
 
        params.myparam=1
        return axios.post(`/get-invoice-list`,params).then((response)=>{
          return  Promise.resolve(response);
                
        }).catch((error)=>{
          return  Promise.resolve(error);
        });
    },
    },
    getters: {
        [GET_FACTURE_LIST_DEF]: state => state.table_def,
    }
}