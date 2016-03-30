
function tree(id, url)
{
    var element = document.getElementById(id)

    function hasClass(elem, className) {
        return new RegExp("(^|\\s)" + className + "(\\s|$)").test(elem.className)
    }

    function toggleNode(node) {
        // РѕРїСЂРµРґРµР»РёС‚СЊ РЅРѕРІС‹Р№ РєР»Р°СЃСЃ РґР»СЏ СѓР·Р»Р°
        var newClass = hasClass(node, 'ExpandOpen') ? 'ExpandClosed' : 'ExpandOpen'
        // Р·Р°РјРµРЅРёС‚СЊ С‚РµРєСѓС‰РёР№ РєР»Р°СЃСЃ РЅР° newClass
        // СЂРµРіРµРєСЃРї РЅР°С…РѕРґРёС‚ РѕС‚РґРµР»СЊРЅРѕ СЃС‚РѕСЏС‰РёР№ open|close Рё РјРµРЅСЏРµС‚ РЅР° newClass
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
                // РјРѕР¶РµС‚ Р±С‹С‚СЊ СЃС‚Р°С‚СѓСЃ 200, Р° РѕС€РёР±РєР°
                // РёР·-Р·Р° РЅРµРєРѕСЂСЂРµРєС‚РЅРѕРіРѕ JSON
                errinfo.message = xhr.statusText
            } else {
                errinfo.message = 'РќРµРєРѕСЂСЂРµРєС‚РЅС‹Рµ РґР°РЅРЅС‹Рµ СЃ СЃРµСЂРІРµСЂР°'
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
            var msg = "РћС€РёР±РєР° " + error.errcode
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
            return // РєР»РёРє РЅРµ С‚Р°Рј
        }

        // Node, РЅР° РєРѕС‚РѕСЂС‹Р№ РєР»РёРєРЅСѓР»Рё
        var node = clickedElem.parentNode
        if (hasClass(node, 'ExpandLeaf')) {
            return // РєР»РёРє РЅР° Р»РёСЃС‚Рµ
        }

        if (node.isLoaded || node.getElementsByTagName('LI').length) {
            // РЈР·РµР» СѓР¶Рµ Р·Р°РіСЂСѓР¶РµРЅ С‡РµСЂРµР· AJAX(РІРѕР·РјРѕР¶РЅРѕ РѕРЅ РїСѓСЃС‚)
            toggleNode(node)
            return
        }


        if (node.getElementsByTagName('LI').length) {
            // РЈР·РµР» РЅРµ Р±С‹Р» Р·Р°РіСЂСѓР¶РµРЅ РїСЂРё РїРѕРјРѕС‰Рё AJAX, РЅРѕ Сѓ РЅРµРіРѕ РїРѕС‡РµРјСѓ-С‚Рѕ РµСЃС‚СЊ РїРѕС‚РѕРјРєРё
            // РќР°РїСЂРёРјРµСЂ, СЌС‚Рё СѓР·Р»С‹ Р±С‹Р»Рё РІ DOM РґРµСЂРµРІР° РґРѕ РІС‹Р·РѕРІР° tree()
            // РљР°Рє РїСЂР°РІРёР»Рѕ, СЌС‚Рѕ "СЃС‚СЂСѓРєС‚СѓСЂРЅС‹Рµ" СѓР·Р»С‹
            // РЅРёС‡РµРіРѕ РїРѕРґРіСЂСѓР¶Р°С‚СЊ РЅРµ РЅР°РґРѕ
            toggleNode(node)
            return
        }

        // Р·Р°РіСЂСѓР·РёС‚СЊ СѓР·РµР»
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