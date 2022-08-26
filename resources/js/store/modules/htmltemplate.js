import { HTMLTEMPLATE_GET_GLOBALCONF, HTMLTEMPLATE_GET_ID, HTMLTEMPLATE_LOAD_GLOBALCONF, HTMLTEMPLATE_SAVE_GLOBALCONF, HTMLTEMPLATE_SAVE_ELEMENT, HTMLTEMPLATE_SET_GLOBALCONF, HTMLTEMPLATE_SET_ID, HTMLTEMPLATE_LOAD_ELEMENTS, HTMLTEMPLATE_SET_ELEMENTS, HTMLTEMPLATE_GET_ELEMENTS, HTMLTEMPLATE_REMOVE_ELEMENT, HTMLTEMPLATE_UNSET_ELEMENT, HTMLTEMPLATE_REPOSITION_ELEMENT, HTMLTEMPLATE_SET_RENDERSQL, HTMLTEMPLATE_SET_FOOTERLIST, HTMLTEMPLATE_SET_HEADERLIST, HTMLTEMPLATE_GET_FOOTERLIST, HTMLTEMPLATE_GET_HEADERLIST, HTMLTEMPLATE_SET_CURRENTHEADER, HTMLTEMPLATE_SET_CURRENTFOOTER, HTMLTEMPLATE_GET_CURRENTHEADER, HTMLTEMPLATE_GET_CURRENTFOOTER, HTMLTEMPLATE_SAVE_HF } from "../types/types";


export const htmltemplate= {
    namespaced:true,
    state: {
        id:null,
        rendersql:false,
        example:'',
        elements:[],
        generalconfig:{
            name:'',
            type:'',
            measuringunit:'px',
            htmltemplate_header_id:0,
            htmltemplate_footer_id:0,
            pagemargin:{
                top:10,
                right:10,
                bottom:10,
                left:10
            },
            global_sql:'',
            global_test_vars:'',
            qrcode:false,
        },
        currentHeader:null,
        currentFooter:null,
        headerList:[],
        footerList:[]
    },
    mutations: {
        [HTMLTEMPLATE_SET_GLOBALCONF]: (state, generalconfig) => {
            state.generalconfig.name = generalconfig.name;
            state.generalconfig.type = generalconfig.type;
            state.generalconfig.measuringunit = generalconfig.measuringunit;
            state.generalconfig.htmltemplate_header_id = generalconfig.htmltemplate_header_id;
            state.generalconfig.htmltemplate_footer_id = generalconfig.htmltemplate_footer_id;
            state.generalconfig.pagemargin.top = generalconfig.margin_top;
            state.generalconfig.pagemargin.right = generalconfig.margin_right;
            state.generalconfig.pagemargin.bottom = generalconfig.margin_bottom;
            state.generalconfig.pagemargin.left = generalconfig.margin_left;
            state.generalconfig.global_sql = generalconfig.global_sql;
            state.generalconfig.global_test_vars = generalconfig.global_test_vars;
            state.generalconfig.example=generalconfig.example;
            state.generalconfig.qrcode=generalconfig.qrcode==1?true:false;
            state.generalconfig.qrcode_rendered=generalconfig.qrcode_rendered;

        },
        [HTMLTEMPLATE_SET_ID]: (state, id) => state.id = id,
        [HTMLTEMPLATE_SET_ELEMENTS]:(state,elements)=>state.elements=elements,
        [HTMLTEMPLATE_UNSET_ELEMENT]:(state,element_id)=>state.elements=state.elements.filter(obj=>obj.id!=element_id),
        [HTMLTEMPLATE_SET_RENDERSQL]:(state,rendersql)=>state.rendersql=rendersql,
        [HTMLTEMPLATE_SET_HEADERLIST]:(state,headerList)=>state.headerList=headerList,
        [HTMLTEMPLATE_SET_FOOTERLIST]:(state,footerList)=>state.footerList=footerList,
        [HTMLTEMPLATE_SET_CURRENTHEADER]:(state,currentHeader)=>state.currentHeader=currentHeader,
        [HTMLTEMPLATE_SET_CURRENTFOOTER]:(state,currentFooter)=>state.currentFooter=currentFooter,
    },
    actions: {
        
        [HTMLTEMPLATE_LOAD_ELEMENTS]: async({commit,state}) => {
            return axios.post(`/get-htmltemplate-elements`,{template_id:state.id,rendersql:state.rendersql}).then((response)=>{
                commit(HTMLTEMPLATE_SET_ELEMENTS,response.data.elements);
                commit(HTMLTEMPLATE_SET_CURRENTHEADER,response.data.currentHeader);
                commit(HTMLTEMPLATE_SET_CURRENTFOOTER,response.data.currentFooter);
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });

        

        },
        [HTMLTEMPLATE_LOAD_GLOBALCONF]: async({commit,state}) => {
            return axios.post(`/get-htmltemplate-conf`,{template_id:state.id}).then((response)=>{
                commit(HTMLTEMPLATE_SET_GLOBALCONF,response.data.conf);
                commit(HTMLTEMPLATE_SET_FOOTERLIST,response.data.footerList);
                commit(HTMLTEMPLATE_SET_HEADERLIST,response.data.headerList);
      
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });

        

        },
        [HTMLTEMPLATE_SAVE_GLOBALCONF]: async({commit,state}, conf) => {
          
            return axios.post(`/save-htmltemplate-conf`,{conf,template_id:state.id}).then((response)=>{
                commit(HTMLTEMPLATE_SET_ID,response.data.id);
               
    
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });

        },
        [HTMLTEMPLATE_SAVE_ELEMENT]: async({commit,state}, element) => {
          
            return axios.post(`/save-htmltemplate-element`,{template_id:state.id,element}).then((response)=>{
         
    
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });

        },
        [HTMLTEMPLATE_REMOVE_ELEMENT]: async({commit,state}, element) => {
          
            return axios.post(`/remove-htmltemplate-element`,{template_id:state.id,element}).then((response)=>{
         
                commit(HTMLTEMPLATE_UNSET_ELEMENT,element.id);
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });

        },
        [HTMLTEMPLATE_REPOSITION_ELEMENT]: async({commit,state}, payload) => {
          
            return axios.post(`/reposition-htmltemplate-element`,{template_id:state.id,payload}).then((response)=>{
         
              
                return  Promise.resolve(response);
                      
              }).catch((error)=>{
                return  Promise.resolve(error);
              });

        },
        [HTMLTEMPLATE_SAVE_HF]: async({commit,state}, payload) => {
          
          return axios.post(`/save-hf`,{payload}).then((response)=>{
       
            
              return  Promise.resolve(response);
                    
            }).catch((error)=>{
              return  Promise.resolve(error);
            });

      },
    },
    getters: {
        [HTMLTEMPLATE_GET_GLOBALCONF]: state => state.generalconfig,
        [HTMLTEMPLATE_GET_ID]: state => state.id,
        [HTMLTEMPLATE_GET_ELEMENTS]: state => state.elements,
        [HTMLTEMPLATE_GET_FOOTERLIST]: state => state.footerList,
        [HTMLTEMPLATE_GET_HEADERLIST]: state => state.headerList,
        [HTMLTEMPLATE_GET_CURRENTHEADER]: state => state.currentHeader,
        [HTMLTEMPLATE_GET_CURRENTFOOTER]: state => state.currentFooter,
    }
}