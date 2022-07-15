<template>
    <item-list-quick-links/>
    <table-filter v-if="table_def.filter==true"
            :checkboxOptions="columnSelection"
            :selectOptions="listGroup"
            activeBackground="var(--lcdtOrange)"
            activeColor="white"
            transformOrigin="top"
            dropdownClasses=""
            :dropdownStyles="{}"
            classes=""
            styles="z-index:2;top:-62px;right:0;"
            @onChange="onTableFilerChange"
        ></table-filter>  
<div :id="table_def.identifier">
    <div class="list-header">
        <template v-for="item,index in table_def.columns_def" :key="index">
            <div v-if="item.visible==true" class="list-header-col almarai_700_normal" @drop="onDrop($event,item)" @dragover="onDragOver($event,item)"  @dragleave="onDragLeave($event,item)" @dragenter="onDragEnter($event,item)" @dragover.prevent  :draggable="isDraggable(item)" @dragstart="startDrag($event,item)" @dragend="endDrag($event,item)" :class="headerClasses(item,sortings)" :style="item.css">
                <span  @click.exact="sortby(item,false)" @click.shift="sortby(item,true)" >{{item.display_name.toLowerCase()}}</span>
                <check-box v-if="item.type=='checkbox'" name="checkall" id="checkall" :checked="lists.length==MULTI_CHECKED.length" @change="checkboxallclicked"/>
                <input  class="mulish_400_normal" v-if="(item.type=='string'||item.type=='html'||item.type=='price')&&item.filter&&typeof item.filter_options=='undefined'" type="text" @keyup.enter="filterColumn(item,$event.target.value)"/>
                <item-list-date-filter v-if="item.type=='date'" :col="item" :name="item.id" @onDateFiltered="filterdate" ></item-list-date-filter>
                <item-list-multi-filter v-if="typeof item.filter_options!='undefined'" :id="item.id" @onMultiFiltered="multifilter" :col="item" :filteroptions="item.filter_options" :identifier="table_def.identifier"/>
            </div>
        </template>
    </div>
    <transition-group tag="div" class="list"  name="list" appear>
        <template v-for="row,index in lists" :key="index">
           <div>
        <div class="list-row list-row-group" v-if="grouped_by!=''&&showGroupHeader(ifnull(row[grouped_by]))" @click="toggleGroupVisible(row[grouped_by])" >
             <template v-for="col,indexcol in table_def.columns_def" :key="`${indexcol}_${grouped_by}`">
                <div class="list-col almarai_700_normal" v-if="col.id==grouped_by && col.visible==true" :style="col.css" :class="colRowClasses(col,row)">
                    <span v-if="col.type=='date'">{{formatDate(row[col.id],'DD/MM/YY')}}</span>
                    <span v-else-if="col.type=='html'" v-html="`${col.prefix} ${ifnull(row[col.id])} ${col.suffix}`"></span>
                    <span v-else-if="col.type=='price'">{{`${col.prefix} ${formatPrice(row[col.id])} ${col.suffix}`}}</span>
                    <slot v-else-if="col.type=='component'" :name="col.id" :row="row"></slot>
                    <span v-else>{{`${col.prefix} ${ifnull(sanitise(row[col.id],col))} ${col.suffix}`}}</span>
                </div>
                <div  class="list-col almarai_700_normal" :style="col.css" :class="colRowClasses(col,row)" v-else-if="col.visible==true||indexcol==1">
                    <span v-if="indexcol==1">{{countGroupItem(grouped_by,row[grouped_by])}}</span>
                    <span v-else-if="col.group_total===true&&col.type=='price'">{{`${col.prefix} ${formatPrice(colTotal(row[grouped_by],col))} ${col.suffix}`}}</span>
                    <span v-else-if="col.group_total===true">{{`${col.prefix} ${colTotal(row[grouped_by],col)} ${col.suffix}`}}</span>
                    <span v-else>&nbsp;</span>
                   
                </div>
             </template>
        </div>
        
        <div class="list-row" @click.self="selectrow(row.id,index)" :style="styleRow(row)" :class="{current_sel:row.id==CURRENT_SELECTED}" v-if="isGroupVisible(row[grouped_by])!=false" >
             <template v-for="col,indexcol in table_def.columns_def" :key="indexcol">
                <div v-if="col.visible==true" class="list-col almarai_700_normal" :style="col.css" :class="colRowClasses(col,row)"  @click="selectrow(row.id,col.type=='checkbox'?'line_select':index)">
                    <span v-if="col.type=='date'">{{formatDate(row[col.id],'DD/MM/YY')}}</span>
                    <check-box v-else-if="col.type=='checkbox'" :name="col.id" :id="row[col.id]" @change="checkboxclicked" :checked="row.id==CURRENT_SELECTED||MULTI_CHECKED.includes(row.id)"/>
                    <span v-else-if="col.type=='html'" v-html="`${col.prefix} ${ifnull(row[col.id])} ${col.suffix}`"></span>
                    <span v-else-if="col.type=='price'">{{`${col.prefix} ${formatPrice(row[col.id])} ${col.suffix}`}}</span>
                    <slot v-else-if="col.type=='component'" :name="col.id" :row="row"></slot>
                    <span v-else>{{`${col.prefix} ${ifnull(sanitise(row[col.id],col))} ${col.suffix}`}}</span>
                </div>
             </template>
        </div>
           </div>
        </template>
    </transition-group>
    <div class="list-footer">
 
                   <template v-for="col,indexcol in table_def.columns_def" :key="indexcol">
                        <div class="list-col  almarai_700_normal" :style="col.css" :class="colClasses(col)" v-if="col.visible==true">
                                  <span v-if="indexcol==1"> {{`${lists.length} ${lists.length!=1?table_def.translations.footer_items:table_def.translations.footer_item}`}}</span>
                                        <span v-else-if="col.footer_total===true&&col.type=='price'">{{`${col.prefix} ${formatPrice(colTotalAll(col))} ${col.suffix}`}}</span>
                                        <span v-else-if="col.footer_total===true">{{`${col.prefix} ${colTotalAll(col)} ${col.suffix}`}}</span>
                                      <span v-else>&nbsp;</span>
                        </div>
                   </template>
              
         
   
    </div>
    <div class="d-flex justify-content-center">
        <span class="loadmore mulish_400_normal" @click="loadmore">Charger plus</span>
    </div>

</div>
</template>
<script>
import { ref, onMounted, nextTick, computed, watch } from 'vue';
import { useStore } from 'vuex';
import { formatDate,formatPrice } from '../../helpers/helpers';
import { DISPLAY_LOADER, HIDE_LOADER, ITEM_LIST_FILTER, ITEM_LIST_GET_COLUMN_FILTERS, ITEM_LIST_GET_CURRENT, ITEM_LIST_GET_LISTS, ITEM_LIST_GET_SORT, ITEM_LIST_GET_TABLES, ITEM_LIST_LOAD_MORE, ITEM_LIST_MODULE, ITEM_LIST_MULTI_CHECK, ITEM_LIST_MULTI_CHECK_LISTS, ITEM_LIST_MULTI_UNCHECK, ITEM_LIST_RESET_MULTI_CHECK, ITEM_LIST_SELECT_CURRENT, ITEM_LIST_SET_TABLE, ITEM_LIST_SET_TABLEDEF, ITEM_LIST_SORT, ITEM_LIST_TABLEDEF, ITEM_LIST_TABLE_RELOAD, LOADER_MODULE } from '../../../store/types/types';
import { useRouter } from 'vue-router';
import ItemListDateFilter from './ItemListDateFilter.vue';
import TableFilter from "../TableFilter.vue";
import ItemListMultiFilter from "./ItemListMultiFilter.vue";
import ItemListQuickLinks from "./ItemListQuickLinks.vue";

export default {

    
    name: "ItemListTable",
    components:{
        ItemListDateFilter,
        TableFilter,
        ItemListMultiFilter,
        ItemListQuickLinks
    },
    props: { 
        table_def: {
            required: true,
            type: Object,
            
        }
    },

    setup(props) {
        const router=useRouter();
        const store=useStore();
        const identifier=props.table_def.identifier;//table identifier
        const droppos=ref({top:"20px",right:'auto',bottom:'auto',left:'0',transformOrigin:'top center'});
        const grouped_by=ref('');
        let   groupval='';
        const openedGroup=ref([]);
        const hidden_col=ref([]);
        const columnSelection =  ref([
                {
                    id: "champsvisible",
                    name: 'Champs',
                    options: [],
                },
            ])

            const listGroup = ref([
                {
                    label: 'Grouper par',
                    options: [
                        { value: '', display: 'Aucune' }
                
                    ],
                },
                //can add more here
            ]);

        onMounted(()=>{
            let table_def=props.table_def;
            let sortedcol=[];
             let hiddencols=window.localStorage.getItem(`hiddenCol_${identifier}`);
             if(hiddencols!=null){
                 hidden_col.value=hiddencols.split(',');
             }
        

          props.table_def.columns_def.forEach((item,i)=>{
                                  let found=hidden_col.value.filter(id=>id==item.id);
                             if(found.length>0){
                                item.visible=false;
                             }else{
                                 item.visible=true;
                             }
                        });
            let sortedcols=window.localStorage.getItem(`sort_${identifier}`);

                if(sortedcols!=null){
                    let cols=sortedcols.split(',');
            
                    let c=0;
                    props.table_def.columns_def.forEach((item,i)=>{
                                if(item.type!="checkbox"){
                                        c++;
                                }
                    });
              
                    if(c!=cols.length){
                        window.localStorage.removeItem(`sort_${identifier}`);
                        sortedcols=window.localStorage.getItem(`sort_${identifier}`);
                    }
                  }

            if(sortedcols!=null){
                let cols=sortedcols.split(',');
                cols.forEach((col,index)=>{
                        props.table_def.columns_def.forEach((item,i)=>{
                          
                            if(item.type!="checkbox"){
                               
                                if(item.id==col){
                                    sortedcol.push(item);
                                }
                            }else if(index==0){
                               
                                sortedcol.push(item);
                            }
                        });
                           
                            
                        });
                        table_def.columns_def=sortedcol;
            }
         
            let table={
                table_def:table_def
            }


        for(const i in props.table_def.columns_def){
            if(props.table_def.columns_def[i].allow_groupby===true){
                listGroup.value[0].options.push({value:props.table_def.columns_def[i].id,display:props.table_def.columns_def[i].display_name});
            }
            if(props.table_def.columns_def[i].type!="checkbox"){
                columnSelection.value[0].options.push({id:props.table_def.columns_def[i].id,value:props.table_def.columns_def[i].display_name,check:props.table_def.columns_def[i].visible});
            }
        }
       
                showLoader('Veuillez patienter. Chargement en cours...');
                store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_TABLEDEF}`,table).finally(()=>hideLoader());
        
        });
        const grouperPar = ref({});
        const onTableFilerChange=(val)=>{
            let hcol=[];
            for(const i in val.checkboxOptions[0].options){
             if(!val.checkboxOptions[0].options[i].check)
             hcol.push(val.checkboxOptions[0].options[i].id)
            }
               hidden_col.value=hcol;   
    
            window.localStorage.setItem(`hiddenCol_${identifier}`,hcol);
            if(typeof val.selectedOptions[0]!="undefined")
            grouped_by.value=val.selectedOptions[0];
        }
            watch(() => hidden_col.value, (currentValue, oldValue) => {
                         props.table_def.columns_def.forEach((item,i)=>{
                             let found=currentValue.filter(id=>id==item.id);
                             if(found.length>0){
                                item.visible=false;
                             }else{
                                 item.visible=true;
                             }
                          
                        });
            });



         const loadmore=()=>{
             showLoader('Veuillez patienter.')
            store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_LOAD_MORE}`).finally(()=>hideLoader());
        }
        const table=computed(()=>{
            let tables=store.getters[`${ITEM_LIST_MODULE}${ITEM_LIST_GET_TABLES}`];
            if(typeof tables[identifier]!="undefined")
            return tables[identifier].table_def;
        });
        const lists=computed(()=>{
            let lists=store.getters[`${ITEM_LIST_MODULE}${ITEM_LIST_GET_LISTS}`];
            if(typeof lists[identifier]!="undefined"){
                groupval="";
                if(grouped_by.value!=''){
                    let list=_.cloneDeep(lists[identifier]);
                    const newlist=groupTogether(list,grouped_by.value);
                    return newlist;
                }
                return lists[identifier];
            }

                return [];
            });
        //group together

        const groupTogether=(list,grouped_by)=>{
            const map=groupBy(list,obj=>obj[grouped_by]);
            let newlist=ref([]);
            map.forEach((val, key, map)=>{
                  for(const i in val){
                      newlist.value.push(val[i]);
                  }
            })
            
            return newlist.value;
        }    
        //group by method
        const  groupBy=(list, keyGetter)=>{
                const map = new Map();
                list.forEach((item) => {
                    const key = keyGetter(item);
                    const collection = map.get(key);
                    if (!collection) {
                        map.set(key, [item]);
                    } else {
                        collection.push(item);
                    }
                });
                return map;
            }
        //set sortby
        const sortby=(col,multiple_col)=>{
            store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_SORT}`,{col:col,multiple_col:multiple_col});
        }
        //watch for any column sort    
        const sortings=computed(()=>{
            let sort=store.getters[`${ITEM_LIST_MODULE}${ITEM_LIST_GET_SORT}`];
              if(typeof sort[identifier]!="undefined")
                return sort[identifier];

                return [];
        });
         watch(() => sortings, (currentValue, oldValue) => {
                    showLoader('Tri en cours. Veuillez patienter')
                  store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_TABLE_RELOAD}`,{fullreload:true}).finally(()=>hideLoader());   
            },
                { deep: true }
            );
        //end watch for any column sort

        //set a column filter
        const filterColumn=(col,word)=>{
            store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_FILTER}`,{col:col,word:word});
        }
        //watch for any column filter change and send request to server

        const colfilters=computed(()=>{
            let col_filters=store.getters[`${ITEM_LIST_MODULE}${ITEM_LIST_GET_COLUMN_FILTERS}`];
            if(typeof col_filters[identifier]!="undefined")
                return col_filters[identifier];
            });


         watch(() => colfilters, (currentValue, oldValue) => {
                    showLoader('Filtrage en cours. Veuillez patienter')
                  store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_TABLE_RELOAD}`,{fullreload:true}).finally(()=>hideLoader());   
            },
                { deep: true }
            );
         //end watch for any column filter change and send request to server
         const CURRENT_SELECTED=computed(()=>{
            let current_selecteds=store.getters[`${ITEM_LIST_MODULE}${ITEM_LIST_GET_CURRENT}`];
            if(typeof current_selecteds[identifier]!="undefined")
                return current_selecteds[identifier];
            });
         const MULTI_CHECKED=computed(()=>{
            let MULTI_CHX=store.getters[`${ITEM_LIST_MODULE}${ITEM_LIST_MULTI_CHECK_LISTS}`];
            if(typeof MULTI_CHX[identifier]!="undefined")
                return MULTI_CHX[identifier];

                return [];
            });

        const headerClasses=(item,sortings)=>{
     
            let header_class=item.header_class;
            if(item.type=="checkbox")
            header_class=`${header_class} d-flex justify-content-center chx noselect`;

            if(typeof item.sort!="undefined" && item.sort==true)
            header_class+=' sortable';

            let s=sortings.filter(obj=>obj.id==item.id);
            if(s.length>0)
            header_class+=` ${s[0].orderby}`;
            

            return header_class;
        }
         const colRowClasses=(col,row)=>{
            let col_class=col.class;
            if(col.type=="checkbox")
            col_class=`${col_class} justify-content-center`;

            if(ifnull(row[col.id])=='--')
            col_class=`${col_class} justify-content-center`;

            return col_class;
        }
        const colClasses=(col)=>{
            let col_class=col.class;
           
        
            return col_class;
        }
         const selectrow=(id,colname)=>{
   
                if(colname=='line_select') return;
                if(typeof CURRENT_SELECTED.value!="undefined"&&CURRENT_SELECTED.value==id){
                    store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_SELECT_CURRENT}`,{current:''});
                }else{
                    store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_SELECT_CURRENT}`,{current:id});
                    router.push({
                        name:table.value.item_route_name,
                        params: {
                            id:id,
                        },
                    });
                }
            }
        const checkboxclicked=(checkbox)=>{
             
                if(CURRENT_SELECTED.value==checkbox.id&&checkbox.value==false){
                    store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_SELECT_CURRENT}`,{current:''});
                 
                }
                if(checkbox.value==true){
                    store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_MULTI_CHECK}`,checkbox.id);
                }
                if(checkbox.value==false){
                    store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_MULTI_UNCHECK}`,checkbox.id);
                }
            }
        const  checkboxallclicked=(checkbox)=>{
                if(checkbox.value==false)
                    store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_RESET_MULTI_CHECK}`);

                if(checkbox.value){
                    const list=_.cloneDeep(lists.value);
                    list.forEach(item => store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_MULTI_CHECK}`,item.id));
                }
            }

        const ifnull=(val)=>{
            if(val===null||val===''||typeof val ==="undefined")
            return '--';

            return val;
        }
        const sanitise=(val,col)=>{
            if(col.type==="number"&&isNaN(val))
            return NaN;

            return val;
        }
        const showLoader=(msg)=>{
            store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`, [true, msg]);
        }
        const hideLoader=()=>{
            store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
        }
        //column drag
        const startDrag=(event,item)=>{
             if(item.type=="checkbox"){
                return;
             }
            event.dataTransfer.dropEffect='move';
            event.dataTransfer.effectAllowed='move';
            event.dataTransfer.setData('ItemID',item.id);
         
            event.srcElement.classList.add('dragging');
        }
        const endDrag=(event,item)=>{
                let draggoverel= document.querySelectorAll('.draggedOver');
                    draggoverel.forEach(node=>{
                            node.classList.remove('draggedOver');
                    });
          
                event.srcElement.classList.remove('dragging');
        }
        const onDrop=(event,dropOverItem)=>{
            if(dropOverItem.type=="checkbox")
            return;
            const ItemID=event.dataTransfer.getData('ItemID');
         
            let cols=[];
            props.table_def.columns_def.forEach((item,index)=>{
            
                if(item.type!="checkbox"){
                    if(item.id==dropOverItem.id){
                        cols.push(ItemID);
                        cols.push(item.id);
                    }else if(item.id!=ItemID){
                         cols.push(item.id);
                    }
                }
                
            });

     cols=cols.filter((v, i, a) => a.indexOf(v) === i);
  
            window.localStorage.setItem(`sort_${identifier}`,cols);
            let sortedcol=[];
          
         cols.forEach((col,index)=>{
                         props.table_def.columns_def.forEach((item,i)=>{
                            if(item.type!="checkbox"){
                                if(item.id==col){
                                    sortedcol.push(item);
                             
                                }
                            }else if(index==0){
                                sortedcol.push(item);
                               
                            }
                        });
                           
                            
                        });
                    let table_def=props.table_def;
                     table_def.columns_def=sortedcol;
                    let table={
                            table_def:table_def
                        }
                      
                   store.commit(`${ITEM_LIST_MODULE}${ITEM_LIST_SET_TABLE}`,table);
        }
         const onDragOver=(event,item)=>{
          if(item.type=="checkbox")
            return;
             event.srcElement.classList.add('draggedOver');
             event.preventDefault();
        }
        const onDragEnter=(event,item)=>{
            if(item.type=="checkbox")
            return;
             event.srcElement.classList.add('draggedOver');
             event.preventDefault();
        }
           const onDragLeave=(event,item)=>{
            if(item.type=="checkbox")
            return;
             event.srcElement.classList.remove('draggedOver');
    
        }
        const isDraggable=(col)=>{
            if(col.type=="checkbox"||props.table_def.rearrange_columns==false)
            return false;

            return true;
        }
        //end column drag

        const filterdate=(data)=>{
                 store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_FILTER}`,{col:data.col,word:data.date});
        }
        const multifilter=(data)=>{
                store.dispatch(`${ITEM_LIST_MODULE}${ITEM_LIST_FILTER}`,{col:data.col,word:data.values});
        }
        const showGroupHeader=(val)=>{
            if(val==groupval){
                return false;
            }
            groupval=val;
            return true;
        }
        const countGroupItem=(grouped_by,val)=>{
            let c=0;

                for(const i in lists.value){
                    if(lists.value[i][grouped_by]==val){
                        c++;
                    }
                }
                return `${c} ${c!=1?props.table_def.translations.group_items:props.table_def.translations.group_item}`;
        }
        const isGroupVisible=(group)=>{
            if(grouped_by.value=='')
            return true;
            let opened=openedGroup.value.filter(g=>g==group);
            if(opened.length>0)
            return true;

            return false;
        }
        const toggleGroupVisible=(group)=>{
            groupval='';
            if(isGroupVisible(group)){
                openedGroup.value=openedGroup.value.filter(g=>g!=group);
            }else{
                openedGroup.value.push(group);
            }
        }

        const colTotal=(val,col)=>{
                let total=0;

                for(const i in lists.value){
                    if(lists.value[i][grouped_by.value]==val){
                        total+=lists.value[i][col.id];
                    }
                }
            if(typeof col.tofixed!='undefined')
            return total.toFixed(col.tofixed);
            
            return total;
        }
        const colTotalAll=(col)=>{

               let total=0;

                for(const i in lists.value){
                        total+=lists.value[i][col.id];
                }
            if(typeof col.tofixed!='undefined')
            return total.toFixed(col.tofixed);

            return total;
        }
        const styleRow=(row)=>{
            if(typeof props.table_def.highlight_row !="undefined")
            for(const i in props.table_def.highlight_row.where){
                if(row[props.table_def.highlight_row.where[i]['col']]===props.table_def.highlight_row.where[i]['value'])
                return `background-color:${props.table_def.highlight_row.backgroundColor};color:${props.table_def.highlight_row.color};`
           }
        }
      return{
          table_def:props.table_def,
  
          lists,
          filterColumn,
          headerClasses,
          colClasses,
          colRowClasses,
          formatDate,
          formatPrice,
          checkboxclicked,
          checkboxallclicked,
          selectrow,
          ifnull,
          CURRENT_SELECTED,
          loadmore,
          MULTI_CHECKED,
          startDrag,
          endDrag,
          onDragEnter,
          onDragLeave,
          onDragOver,
          onDrop,
          isDraggable,
          sortby,
          sortings,
          droppos,
          filterdate,
          grouped_by,
          showGroupHeader,
          countGroupItem,
          isGroupVisible,
          toggleGroupVisible,
          colTotal,
          sanitise,
          styleRow,
          colTotalAll,
          grouperPar,
          listGroup,
          columnSelection,
          onTableFilerChange,
          multifilter
      }

    }

}
</script>

<style lang="scss" scoped>

.list-header{
    background: #FFF;
    display: flex;
    gap:8px;
    padding: 23px 5px 4px 5px;
    margin-bottom: 3px;
     box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.5);
     min-width:1600px;
     position: sticky;
     top:64px;
     z-index: 1;
}
.list-footer{
    background: #FFF;
    display: flex;
    gap:8px;
    padding: 0 5px 0 5px;
      box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.5);
      min-height: 66px;
      margin-bottom: 10px;
      min-width: 1600px;
}

.list{
    min-width: 1600px;
}
.list-header input{
    width:100%;
    height: 40px;
    background: #EEEEEE;
    border-radius: 5px 5px 0  0;
    border:none;
    padding:0 6px;
    outline: none;
    transition: all 0.3s ease-in-out;
}
.list-header input:focus{
   background: #DDDDDD;
}

.list-header-col{
    text-transform: capitalize;
    flex:1;
    font-size: 13px;
    color:#868686;
        border-left: 2px solid white;
  
        &.sortable{
             position: relative;    
            & span{
           
            padding-right: 13px;
            transition: color 300ms ease-in-out;
            cursor:pointer;
                &::after{
               
                   content: url('data:image/svg+xml; utf8, <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 3.202l3.839 4.798h-7.678l3.839-4.798zm0-3.202l-8 10h16l-8-10zm3.839 16l-3.839 4.798-3.839-4.798h7.678zm4.161-2h-16l8 10 8-10z"/></svg>');
                    display: block;
                    width: 22px;
                    height: 25px;
                   transform: scale(0.65);
                   position: absolute;
                   right: 0;
                   top:-3px;
                }
                
            }
            &:hover span{
                color:var(--lcdtOrange);
            }
            &.desc{
                & span{
                    &::after{
                        content: url('data:image/svg+xml; utf8, <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="%23e86f29" viewBox="0 0 24 24"><path d="M12 3.202l3.839 4.798h-7.678l3.839-4.798zm0-3.202l-8 10h16l-8-10zm8 14h-16l8 10 8-10z"/></svg>');
                    }
                  
                }
            }
            &.asc{
                & span{
                    &::after{
                        content: url('data:image/svg+xml; utf8, <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="%23e86f29" viewBox="0 0 24 24"><path d="M12 0l-8 10h16l-8-10zm3.839 16l-3.839 4.798-3.839-4.798h7.678zm4.161-2h-16l8 10 8-10z"/></svg>');
                    }
                  
                }
            }
            &.desc span{
                  color:var(--lcdtOrange);
            }
            &.asc span{
                  color:var(--lcdtOrange);
            }
        }
}
.list-header-col:not(.chx).draggedOver{
   border-left-color: var(--lcdtOrange);
}
.list-header-col.dragging{
    cursor: move!important;
}
.list-row{
    display: flex;
    min-height: 66px;
    background: #FFFFFF;
    box-shadow: inset 0px -1px 0px rgba(168, 168, 168, 0.25);
    gap:8px;
    padding:0 5px 0 5px;
     cursor: pointer;
        transition: background-color 300ms ease-out;
        &:hover{
            background-color: transparent;
        }
}
.list-row-group{
     background: #ECECEC;
     cursor:pointer;
}
.list-col{
    flex:1;
    align-items: center;
    display: flex;
    font-size: 16px;
    word-break: break-word;
   
}
.list-row{
    transform-origin: left center;
}
.current_sel{
    position: relative;
    z-index: 10000;
    background: rgb(247, 251, 246)!important;
}
 /*list transitions*/
    .list-enter-from{
        opacity: 0;
        transform: scale(0.6);
    }
    .list-enter-to{
        opacity: 1;
        transform: scale(1);
    }
    .list-enter-active{
        transition: all 1s ease;
    }

    .list-leave-from{
        transform-origin: right center;
        opacity: 1;
        transform: scale(1);
   
    }
    .list-leave-to{
        transform-origin: right center;
        opacity: 0;
        transform: scale(0.6);
    }
    .list-leave-active{
               transition: all 1s ease;
         transform-origin: right center;
        position:absolute;     
        width: 100%;
    }
    .list-move{
        transition:all 0.3s ease;
    }
    .loadmore{
        text-decoration: underline;
        cursor: pointer;
        text-align: center;
        margin-bottom: 10px;
        &:hover{
            color:var(--lcdtOrange);
        }
    }
.noselect {
  -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Old versions of Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome, Edge, Opera and Firefox */
}
</style>
