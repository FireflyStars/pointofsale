import store from "../../store/store";
import {TOASTER_MESSAGE, TOASTER_MODULE} from "../../store/types/types";



export const featureUnavailable=((feature)=>{
    store.dispatch(`${TOASTER_MODULE}${TOASTER_MESSAGE}`,{message:feature+' feature not yet implemented.',ttl:5,type:'success'});
});

export const formatPrice=price=>`${price !== 0 ? price.toFixed(2) : 0} €`;

export const formatDate=(date_str,format)=>{
    if(date_str==null)
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
    const dateTimeFormat = new Intl.DateTimeFormat('en-GB', options);

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