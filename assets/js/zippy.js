
/*Zippy  framework*/

 
 
 
function getUpdate(q)
{
    $.ajax({
        url: q,
        dataType: "text",
        success: function(data, textStatus) {
            eval(data);
        }

    });

}
 

function submitForm(formid, q)
{


    $('#' + formid).ajaxSubmit({
        url: q,
        type: "post",
        success: function(responseText, statusText, xhr, $form) {
            eval(responseText);

        }
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
