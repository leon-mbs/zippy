
/*Zippy  framework*/

 
 
 
function getUpdate(q)
{
 
    
fetch(q)
  .then((response) => {
    return response.text();
  })
  .then((data) => {
   eval(data);
  })
   .catch(function (error) {
            console.log('error', error)
          });  
    
}
 

function submitForm(formid, q)
{
 
    
    var f = document.getElementById(formid)  ;
    let formdata = new FormData(f);
    
    
    
    fetch(q,{
      method: 'POST',
            body: formdata  
    })
      .then((response) => {
        return response.text();
      })
      .then((data) => {
       eval(data);
      }) 
      .catch(function (error) {
            console.log('error', error)
          });      
    
} 
 
function beforeZippy(id) {

    var i = id.lastIndexOf('_');
    if (i > 0) {
        id = id.substring(0, i);
    }

    var f = 'check_' + id;

    if (typeof window[f] == 'function') {


        return window[f]();
    }
    return true;
}


//возвращает URL при вызове  метода страницы 
//method -  наименования  метода
//params  - массив параметров
function getMethodUrl(method,params=null){
   
       var p='';
       if(Array.isArray(params))  {
           p =   params.join(':');
       }
       var url = window._baseurl+'::'+method+':'+p+'&ajax=true'
       return url;
        
}   

    
// вызов бэкэнд метода страницы на бекенде
//method -  наименования  метода
//params  - массив параметров//postdata  - данные  если  POST запрос (например  FormData)
//callback  - функция вызываемая  после успешного  ответа  сервера. Принамает  текстовый параметр)
//callerror  - функция вызываемая в  случае  шибкти  запроса
function  callPageMethod(method,params,postdata,callback  , callerror=null     )
{        
       
 
       var url = getMethodUrl(method,params) 
     
       var opt={
           method: 'GET' ,
           credentials: "same-origin"               
       };
       if(postdata !=null) {
          opt.method = "POST"
          opt.body =  postdata  
           
       }
        fetch(url,opt)                                            
          .then((response) => {
              
            return response.text();
          })
          .then((data) => {
               callback(data )
            
          })
          .catch(function (error) {
            console.log('error', error)
            if(callerror != null){
                callerror(error);  
            }             
          });  

}