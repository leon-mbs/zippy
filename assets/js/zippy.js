
/*Zippy  framework*/

 
 
 
function getUpdate(q)
{
 
    
fetch(q)
  .then((response) => {
    return response.text();
  })
  .then((data) => {
   eval(data);
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
