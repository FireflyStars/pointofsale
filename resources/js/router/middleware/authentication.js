export default (to,from,next)=>{
    let htmltag=document.getElementsByTagName( 'html' )[0];
    let bodytag=document.getElementsByTagName( 'body' )[0]
    bodytag.className='';

    htmltag.className='';
    htmltag.classList.add(to.name);
    /*if(to.name=="Permissions"&&sessionStorage.getItem('auth')){
        if(hasRoles(['admin']))
        return next();

        return next('/not-found');
    }*/
    if(to.name=="Login"&&sessionStorage.getItem('auth')){
        return next('/');
    }
    if(to.meta.authenticated&&sessionStorage.getItem('auth')){
       return next();
    }

    if(!to.meta.authenticated)
       return next();

    return next('/auth/login/');
};