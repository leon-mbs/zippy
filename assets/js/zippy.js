
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
;

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


function tree(id, url)
{
    var element = document.getElementById(id)

    function hasClass(elem, className) {
        return new RegExp("(^|\\s)" + className + "(\\s|$)").test(elem.className)
    }

    function toggleNode(node) {
        // определить новый класс для узла
        var newClass = hasClass(node, 'ExpandOpen') ? 'ExpandClosed' : 'ExpandOpen'
        // заменить текущий класс на newClass
        // регексп находит отдельно стоящий open|close и меняет на newClass
        var re = /(^|\s)(ExpandOpen|ExpandClosed)(\s|$)/
        node.className = node.className.replace(re, '$1' + newClass + '$3')
    }

    function load(node) {

        function showLoading(on) {
            var expand = node.getElementsByTagName('DIV')[0]
            expand.className = on ? 'ExpandLoading' : 'Expand'
        }


        function onSuccess(data) {
            if (!data.errcode) {

                onLoaded(data)
                showLoading(false)
            } else {
                showLoading(false)
                onLoadError(data)
            }
        }


        function onAjaxError(xhr, status) {
            showLoading(false)
            var errinfo = {
                errcode: status
            }
            if (xhr.status != 200) {
                // может быть статус 200, а ошибка
                // из-за некорректного JSON
                errinfo.message = xhr.statusText
            } else {
                errinfo.message = 'Некорректные данные с сервера'
            }
            onLoadError(errinfo)
        }


        function onLoaded(data) {
            // alert(data);

            $(node).append(data);
            node.isLoaded = true
            toggleNode(node)
        }

        function onLoadError(error) {
            var msg = "Ошибка " + error.errcode
            if (error.message)
                msg = msg + ' :' + error.message
            alert(msg)
        }


        showLoading(true)


        $.ajax({
            url: url + ':' + $(node).attr('nodeid') + ':load&ajax=true',
            //data: node.id,
            dataType: "html",
            success: onSuccess,
            error: onAjaxError,
            cache: false
        })
    }

    element.onclick = function(event) {
        event = event || window.event
        var clickedElem = event.target || event.srcElement

        if (!hasClass(clickedElem, 'Expand')) {
            return // клик не там
        }

        // Node, на который кликнули
        var node = clickedElem.parentNode
        if (hasClass(node, 'ExpandLeaf')) {
            return // клик на листе
        }

        if (node.isLoaded || node.getElementsByTagName('LI').length) {
            // Узел уже загружен через AJAX(возможно он пуст)
            toggleNode(node)
            return
        }


        if (node.getElementsByTagName('LI').length) {
            // Узел не был загружен при помощи AJAX, но у него почему-то есть потомки
            // Например, эти узлы были в DOM дерева до вызова tree()
            // Как правило, это "структурные" узлы
            // ничего подгружать не надо
            toggleNode(node)
            return
        }

        // загрузить узел
        load(node)

    }



}

function treeCheck(checker)
{

    $(checker.parentNode).find('input').each(function(o, elem) {
        if (checker.checked)
        {
            $(elem).attr("checked", "checked");
        }
        else
        {
            $(elem).removeAttr("checked")
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
