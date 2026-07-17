
/*Zippy  framework*/

 
 
// ajax  request 
function getUpdate(q)
{
 
    
fetch(q)
  .then((response) => {
    return response.text();
  })
  .then((data) => {
        try{
            console.log(data);          
              
           if( isJsonString(data)) {
               updateFromAjax(data) 
           }   else
           {
               eval(data); 
           }
            
             
        } catch(err) {
                console.log(err)    
                console.log(data)    
        }
  })
   .catch(function (error) {
            console.log('error', error)
          });  
    
}
 
// ajax  request
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
            try{
               console.log(data);          
               if( isJsonString(data)) {
                   updateFromAjax(data) 
               }   else
               {
                   eval(data); 
               }
                
            } catch(err) {
                    console.log(err)    
                    console.log(data)    
            }
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
           p =  ':'+ params.join(':');
       }
       var url = window._baseurl+'::'+method+p+'&ajax=true'
       return url;
        
}   

    
// вызов бэкэнд метода страницы на бекенде
//method -  наименования  метода
//params  - массив параметров//postdata  - данные  если  POST запрос (например  FormData)
//callback  - функция вызываемая  после успешного  ответа  сервера. Принамает  текстовый параметр)
//callerror  - функция вызываемая в  случае  шибкти  запроса
function  callPageMethod(method,params,postdata,callback =null   , callerror=null     )
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
               
                if(callback != null){
                   callback(data ) 
                }          
          })
          .catch(function (error) {
            console.log('error', error)
            if(callerror != null){
                callerror(error);  
            }             
          });  

}

function isJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

// ajax  ответ для обновления  елементов
function updateFromAjax(str) {
    try {
      var arr = JSON.parse(str);
      for (const c of arr) {
      
         
         if(c.type == "Label"){
             if(c.ishtml) {
                $("#"+c.type).html(c.data)   
             }  else{
                $("#"+c.id).text(c.data)   
             }
         }
         if(c.type == "TextInput"){
             $("#"+c.id).val(c.data)   
         }
         if(c.type == "TextArea"){
             $("#"+c.id).text(c.data)   
         }
         if(c.type == "DropDownChoice"){
             $("#"+c.id).val(c.data)   
         }
         if(c.type == "DropDownChoiceOptions"){
             $("#"+c.id).empty()   
             for (const o of c.data) {
                 $("#"+c.id).append("<option value=\""+o.key+"\">"+o.value+"</option>")    
             }
         }
           if(c.type == "Visible"){
          
             if(c.visible == true || c.visible == 1 ) {
                 $("#"+c.id).show()   
                   console.log('show'); 
          
             }   else {
                 $("#"+c.id).hide()     
                   console.log('hide'); 
          
             }
             
             console.log('[for="'+c.id+'"]'); 
              
              
           
            
             $('[for="'+c.id+'"]').each(function(index, element) {
                 
                 if(c.visible == true || c.visible == 1 ) {
                     $(element).show()   
                 }   else {
                     $(element).hide()     
                 }     
             } )
             $('[data-label="'+c.id+'"]').each(function(index, element) {
                  
              if(c.visible == true || c.visible == 1 ) {
                     $(element).show()   
                 }   else {
                     $(element).hide()     
                 }     
             }  )
                     
            
         }
         if(c.type == "Attribute"){
            
             $("#"+c.id).attr(c.attr,c.value)   
    
         }        
 
         if(c.type == "DataTable"){
        
             $("#"+c.id ).html(c.data)   
    
         }
      
         if(c.type == "Function"){
              if (typeof window[c.name] == 'function') {
                  if(c.data)  {   
                    window[c.name](c.data);  
                  }   else {
                     window[c.name](); 
                  }
                  
              }   else {
                  console.log(c.name+' not found') 
              }
  
         }        
      }      
        
    } catch (e) {
      console.log(e)
    }
   
}