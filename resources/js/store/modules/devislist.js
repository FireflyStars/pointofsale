import {  DEVISLIST_LOAD_TAB, DEVISLIST_SET_LIST, DEVIS_LIST_MODULE, GET_DEVIS_LIST_DEF } from "../types/types";

export const devislist= {
    namespaced:true,
    state: {
      
        table_def: {
            store:{
              MODULE:DEVIS_LIST_MODULE,//required
              INIT:DEVISLIST_LOAD_TAB,//required
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
              group_item:'devis',
              group_items:'devis',
              footer_item:'ITEM',
              footer_items:'ITEMS',
              no_batch_action:"Aucune action par lot n'est disponible.",
            },
            highlight_row:{
                  where:[
                    {col:'id',value:10}, //conditions to higlight rows
                    {col:'nbheure',value:6}
                  ], 
                  backgroundColor:'#f7c5af',
                  color:'#fd3b35'
                }
            ,
            item_route_name:"DevisDetail",// the route to trigger when a line is click 
            max_per_page:10,//required          
            identifier:"devislist_all",//required
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
                 id:"id",
                 display_name:"No DEVIS",
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
    mutations: {
      [DEVISLIST_SET_LIST]:(state,list)=>{
        state.list=list;
      }
    },
    actions: {
      [DEVISLIST_LOAD_TAB]:async({commit,state,dispatch},params)=>{
 
        params.myparam=1
        return axios.post(`/get-devis-list`,params).then((response)=>{
          return  Promise.resolve(response);
                
        }).catch((error)=>{
          return  Promise.resolve(error);
        });
    },
    },
    getters: {
        [GET_DEVIS_LIST_DEF]: state => state.table_def,
    }
}