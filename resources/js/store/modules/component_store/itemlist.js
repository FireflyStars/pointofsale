import { ITEM_LIST_FILTER, ITEM_LIST_GET_COLUMN_FILTERS, ITEM_LIST_GET_CURRENT, ITEM_LIST_GET_LISTS, ITEM_LIST_GET_TABLES, ITEM_LIST_LOAD_MORE, ITEM_LIST_SET_PAGINATION, ITEM_LIST_SELECT_CURRENT, ITEM_LIST_SET_CURRENT, ITEM_LIST_SET_LIST, ITEM_LIST_SET_TABLEDEF, ITEM_LIST_TABLEDEF, ITEM_LIST_TABLE_RELOAD, ITEM_LIST_UPDATE_FILTER, ITEM_LIST_GET_IDENTIFIER, ITEM_LIST_MULTI_CHECK, ITEM_LIST_MULTI_UNCHECK, ITEM_LIST_RESET_MULTI_CHECK, ITEM_LIST_MULTI_CHECK_LISTS, ITEM_LIST_SET_TABLE, ITEM_LIST_SORT, ITEM_LIST_SET_SORT, ITEM_LIST_GET_SORT } from "../../types/types";

export const itemlist= {
    namespaced:true,
    state: {
        current_table_identifier:'',
        tables:{},
        lists:{},
        column_filters:{},
        filters:{},
        paginations:{},
        sortby:{},
        currentchecked:{},
        multichecked:{},
    },
    mutations: {
      [ITEM_LIST_UPDATE_FILTER]:(state,params)=>{
            if(typeof state.column_filters[state.current_table_identifier]=="undefined")
            state.column_filters[state.current_table_identifier]=[];

            let filter=state.column_filters[state.current_table_identifier].filter(obj=>obj.id==params.filter.id)
       
            if(filter.length===0)
            state.column_filters[state.current_table_identifier].push(params.filter);

            if(params.filter.word=='')
            state.column_filters[state.current_table_identifier]=state.column_filters[state.current_table_identifier].filter(obj=>obj.id!=params.filter.id);
      },
      [ITEM_LIST_SET_TABLE]:(state,table)=>{
    
        state.tables[ state.current_table_identifier]=table;
  
      }, 
      [ITEM_LIST_SET_TABLEDEF]:(state,table)=>{
          state.current_table_identifier=table.table_def.identifier;
          state.tables[ state.current_table_identifier]=table;
          state.paginations[state.current_table_identifier]=0;
        },
      [ITEM_LIST_SET_LIST]:(state,params)=>{
          if(params.fullreload){
            state.lists[state.current_table_identifier]=params.lists;
          }else{
            state.lists[state.current_table_identifier]=state.lists[state.current_table_identifier].concat(params.lists);
          }
      },
      [ITEM_LIST_SET_CURRENT]:(state,params)=>{
            state.currentchecked[state.current_table_identifier]=params.current;
      },  
      [ITEM_LIST_SET_PAGINATION]:(state,val)=>{
            state.paginations[state.current_table_identifier]=val;
      },
      [ITEM_LIST_MULTI_CHECK]:(state,id)=>{
            if(typeof state.multichecked[state.current_table_identifier]=="undefined")
            state.multichecked[state.current_table_identifier]=[];

            state.multichecked[state.current_table_identifier].push(id);   
      },
      [ITEM_LIST_MULTI_UNCHECK]:(state,id)=>{
            state.multichecked[state.current_table_identifier]=state.multichecked[state.current_table_identifier].filter(chkid=>chkid!=id);   
      },
      [ITEM_LIST_RESET_MULTI_CHECK]:(state)=>{
        state.multichecked[state.current_table_identifier]=[];
      },
      [ITEM_LIST_SET_SORT]:(state,col)=>{
        if(typeof state.sortby[state.current_table_identifier]=="undefined")
        state.sortby[state.current_table_identifier]=[];

        let c=state.sortby[state.current_table_identifier].filter(obj=>obj.id==col.id);

        if(c.length==0){
            state.sortby[state.current_table_identifier].push({id:col.id,orderby:'asc'});
        }else if (c[0].orderby=='asc'){
            c[0].orderby='desc';
        }else if (c[0].orderby=='desc'){
            
            state.sortby[state.current_table_identifier]=state.sortby[state.current_table_identifier].filter(obj=>obj.id!=col.id);
        }

       

      }
    },
    actions: {
        [ITEM_LIST_SELECT_CURRENT]:({commit,state,dispatch},params)=>{
            commit(`${ITEM_LIST_SET_CURRENT}`,params);

            let bodytag=document.getElementsByTagName( 'body' )[0]
            if(params.current==''){
                bodytag.className='';
            }else{
                bodytag.classList.add('hide-overflowY');
            }
        },
        [ITEM_LIST_TABLEDEF]:({commit,state,dispatch},table)=>{
            commit(`${ITEM_LIST_SET_TABLEDEF}`,table);

            /*dispatch(`${table.table_def.store.MODULE}${table.table_def.store.INIT}`,{
                skip:state.paginations[state.current_table_identifier],
                take:state.tables[state.current_table_identifier].table_def.max_per_page
            },{root: true}).then((response)=>{
                commit(`${ITEM_LIST_SET_LIST}`,{identifier:state.current_table_identifier,lists:response.data,fullreload:true});
            });*/
            return dispatch(`${ITEM_LIST_TABLE_RELOAD}`,{fullreload:true});

        },
        [ITEM_LIST_TABLE_RELOAD]:async({commit,state,dispatch},params)=>{

            if(params.fullreload)
            commit(`${ITEM_LIST_SET_PAGINATION}`,0);//reset skip to 0
            return dispatch(`${state.tables[state.current_table_identifier].table_def.store.MODULE}${state.tables[state.current_table_identifier].table_def.store.INIT}`,

            {
              column_filters:state.column_filters[state.current_table_identifier],
              skip:state.paginations[state.current_table_identifier],
              take:state.tables[state.current_table_identifier].table_def.max_per_page
            },{root: true}).then((response)=>{
       
                    commit(`${ITEM_LIST_SET_LIST}`,{identifier:state.current_table_identifier,lists:response.data,fullreload:params.fullreload});
                    return Promise.resolve(response);
             
                
            }).finally(()=>{
               
            });

        },
        [ITEM_LIST_FILTER]:({commit,state,dispatch},params)=>{
            
            params.col.word=params.word;
            const filter= params.col;
            commit(`${ITEM_LIST_UPDATE_FILTER}`,{filter:filter,identifier:state.current_table_identifier});
        },
        [ITEM_LIST_LOAD_MORE]:({commit,state,dispatch})=>{
            commit(`${ITEM_LIST_SET_PAGINATION}`,state.paginations[state.current_table_identifier]+state.tables[state.current_table_identifier].table_def.max_per_page);
            return dispatch(`${ITEM_LIST_TABLE_RELOAD}`,{fullreload:false}).then(response=>{

            }).finally(()=>{
            
                    window.scrollTo({ left: 0, top: document.body.scrollHeight, behavior: "smooth" });
            });
        },
        [ITEM_LIST_SORT]:({commit,state,dispatch},col)=>{
            commit(`${ITEM_LIST_SET_SORT}`,col)
        }
    },
    getters: {
        [ITEM_LIST_GET_TABLES]:state=>state.tables,
        [ITEM_LIST_GET_LISTS]:state=>state.lists,
        [ITEM_LIST_GET_COLUMN_FILTERS]:state=>state.column_filters,
        [ITEM_LIST_GET_CURRENT]:state=>state.currentchecked,
        [ITEM_LIST_GET_IDENTIFIER]:state=>state.current_table_identifier,
        [ITEM_LIST_MULTI_CHECK_LISTS]:state=>state.multichecked,
        [ITEM_LIST_GET_SORT]:state=>state.sortby,
    }
}