/**
 * Created by hyeonseungjae on 15. 5. 30..
 */


jQuery(function($){
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });



});

jQuery(document).ready(function($){
    $('#jstree-proton-2').jstree({
        'plugins': ["wholerow"],
        'core': {
            'themes': {
                'name': 'proton',
                'responsive': true
            }
        }
    });

    $("#upload-rss").change(function(){
        var $formFile = $(this);
        var $form = $formFile.parents("form");
        console.log($form);
        $form.ajaxSubmit({
            url:"/rss/upload",
            dataType:'json',
            type:'post',
            iframe:true,
            target:'#hidden-iframe',
            success:function(json) {
                if(json.status == "success") {
                    //alert("!");
                }
                else if(json.status == "failure") {
                    alert(json.message);
                }
            },
            error:function(data) {
                alert("error");
            },
            complete:function () {
                if ($.browser.msie) {
                    $formFile.replaceWith($formFile.clone());
                }
                else {
                    $formFile.val('');
                }
            }
        });
        return false;
    });

});