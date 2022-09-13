import store from "../../store/store";
import {DISPLAY_LOADER, HIDE_LOADER, LOADER_MODULE, SET_LOADER_MSG, TOASTER_CLEAR_TOASTS, TOASTER_MESSAGE, TOASTER_MODULE} from "../../store/types/types";



export const featureUnavailable=((feature)=>{
    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:feature+' feature not yet implemented.',ttl:5,type:'success'});
});
export const displayError=((msg)=>{
    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:msg,ttl:5,type:'danger'});
});
export const displaySuccess=((msg)=>{
    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:msg,ttl:5,type:'success'});
});
export const clearMsg=(()=>{
    store.commit(`${TOASTER_MODULE}${TOASTER_CLEAR_TOASTS}`);
});
export const displayLoader=((msg)=>{
    store.dispatch(`${LOADER_MODULE}${DISPLAY_LOADER}`,[true,msg]);
});
export const hideLoader=((msg)=>{
    store.dispatch(`${LOADER_MODULE}${HIDE_LOADER}`);
});
export const formatPrice=price=>`${price !== 0 && typeof price != 'undefined' ? price?.toFixed(2) : 0} €`;

export const isFloat=x=>{
    if(typeof x == 'number' && !isNaN(x)){

        if (Number.isInteger(x)) {
            return false
        }
        else {
            return true;
        }

    } else {
        return false;
    }
}

export const formatDate=(date_str,format)=>{
    console.log(date_str,format);
    if(date_str=='0000-00-00' || date_str===null||typeof(date_str)=="undefined")
    return "--/--";
    if(typeof format==="undefined")
        format='DD/MM/YY';
    const date=new Date(date_str);
    let options = {  month: 'numeric', day: 'numeric' };
    if(format==='DAY DD/MM')
        options = { weekday: 'short',  month: 'numeric', day: 'numeric' };
    if(format==='DAYL DD/MM')
    options = { weekday: 'long',  month: 'numeric', day: 'numeric' };    

    if(format==='DD/MM/YY')
    options = { year: 'numeric',  month: 'numeric', day: 'numeric' };  
    const dateTimeFormat = new Intl.DateTimeFormat('fr-FR', options);

    return dateTimeFormat.format(date).replace(',','').toUpperCase();
}
//remove duplicate object form array by comparing an attribute value
export const removeDuplicatesBy=(keyFn, array)=>{
    var mySet = new Set();
    return array.filter(function(x) {
        var key = keyFn(x), isNew = !mySet.has(key);
        if (isNew) mySet.add(key);
        return isNew;
    });
}

export const br=(str)=>{
    if(typeof str=="undefined")
    return '';
    if(str.trim()!=''&&str!=null)
    return `<br/>${str}`;

    return str;
}