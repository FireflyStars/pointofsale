import { HTMLTEMPLATEHEADERLIST_LIST_DEF, HTMLTEMPLATEHEADERLIST_LOAD_TAB, HTMLTEMPLATEHEADERLIST_MODULE } from "../types/types";

export const htmltemplateheaderlist= {
  namespaced:true,
  state: {
    
      table_def: {
          store:{
            MODULE:HTMLTEMPLATEHEADERLIST_MODULE,//required
            INIT:HTMLTEMPLATEHEADERLIST_LOAD_TAB,//required
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
            group_item:'en-tête',
            group_items:'en-têtes',
            footer_item:'ITEM',
            footer_items:'ITEMS',
            no_batch_action:"Aucune action par lot n'est disponible.",
          },
          highlight_row:{
                where:[
                 // {col:'id',value:10}, //conditions to higlight rows
                //  {col:'nbheure',value:6}
                ], 
                backgroundColor:'#f7c5af',
                color:'#fd3b35'
              }
          ,
          item_route_name:"",// the route to trigger when a line is click 
          max_per_page:10,//required          
          identifier:"htmltemplateheader_all",//required
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
               display_name:"Id en-tête",
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
              id:"name",
              display_name:"Nom en-tête",
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
              id:"rowaction",
              display_name:"",
              type:"component",
              class:"",
              header_class:"",
              sort:false,
              filter:false,   
              allow_groupby:false,
              css:{
                flex:0.5
              },
            },
          
          ]

          

      },
      
  },
  mutations: {

  },
  actions: {
    [HTMLTEMPLATEHEADERLIST_LOAD_TAB]:async({commit,state,dispatch},params)=>{

      params.myparam=1
      return axios.post(`/get-htmltemplateheader-list`,params).then((response)=>{
        return  Promise.resolve(response);
              
      }).catch((error)=>{
        return  Promise.resolve(error);
      });
  },
  },
  getters: {
      [HTMLTEMPLATEHEADERLIST_LIST_DEF]: state => state.table_def,
  }
}