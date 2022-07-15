import { ITEMLISTQUICKLINK_SET_LINKS, ITEMLISTQUICKLINK_SET_LOADED, ITEMLISTQUICKLINK_UNSET_LINK, ITEMLISTQUICKLINK_LOAD_LINKS, ITEMLISTQUICKLINK_REMOVE_LINK,ITEMLISTQUICKLINK_GET_LINKS,ITEMLISTQUICKLINK_GET_LOADED, ITEMLISTQUICKLINK_SET_SUPER,ITEMLISTQUICKLINK_GET_ISSUPERADMIN, ITEMLISTQUICKLINK_ADD_LINK, ITEMLISTQUICKLINK_SET_LINK } from "../../types/types";


export const itemlistquicklink= {
    namespaced:true,
    state: {
        loaded:false,
        links:[],
        superadmin:false
    },
    mutations: {
      [ITEMLISTQUICKLINK_SET_LINKS]:(state,links)=>{
        state.links=links;
      },
      [ITEMLISTQUICKLINK_SET_LOADED]:(state)=>{
        state.loaded=true;
      },
      [ITEMLISTQUICKLINK_SET_SUPER]:(state)=>{
        state.superadmin=true;
      },
      [ITEMLISTQUICKLINK_UNSET_LINK]:(state,quick_link_id)=>{
          state.links=state.links.filter(obj=>obj.id!=quick_link_id);
      },
      [ITEMLISTQUICKLINK_SET_LINK]:(state,link)=>{
        state.links.push(link);
      }
    },
    actions: {
      [ITEMLISTQUICKLINK_ADD_LINK]:async({commit,state,dispatch},link)=>{

        return axios.post(`/add-quick-link`,link).then((response)=>{
          commit(ITEMLISTQUICKLINK_SET_LINK,response.data.link);
          return  Promise.resolve(response);
                
        }).catch((error)=>{
          return  Promise.resolve(error);
        });

    
    },
      [ITEMLISTQUICKLINK_LOAD_LINKS]:async({commit,state,dispatch},params)=>{
        commit(ITEMLISTQUICKLINK_SET_LOADED);
        return axios.post(`/get-quick-links`,params).then((response)=>{
          commit(ITEMLISTQUICKLINK_SET_LINKS,response.data.links);
          commit(ITEMLISTQUICKLINK_SET_SUPER,response.data.is_admin);
          return  Promise.resolve(response);
                
        }).catch((error)=>{
          return  Promise.resolve(error);
        });

    
    },
    [ITEMLISTQUICKLINK_REMOVE_LINK]:async({commit,state,dispatch},quick_link_id)=>{
    
          return axios.post(`/remove-quick-link`,{quicklink_id:quick_link_id}).then((response)=>{
            commit(ITEMLISTQUICKLINK_UNSET_LINK,quick_link_id);
            return  Promise.resolve(response);
                  
          }).catch((error)=>{
            return  Promise.resolve(error);
          });
    },
  },
    getters: {
        [ITEMLISTQUICKLINK_GET_LINKS]: state => state.links,
        [ITEMLISTQUICKLINK_GET_LOADED]: state => state.loaded,
        [ITEMLISTQUICKLINK_GET_ISSUPERADMIN]: state => state.superadmin,
    }
}