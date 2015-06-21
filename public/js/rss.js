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

    $('#menu').metisMenu({
        toggle: false
    });


    $("#menu ul.collapse a").on("click", function(){
        var $rss_url = $(this).attr("data-rss-url");

        $.ajax({
            url:"/rss/getRssContent/",
            data: "rss_url="+$rss_url,
            type:"post",
            dataType:'json',
            success:function(json){

                if(json.status=='success') {
                    $("#rss_content").html(json.data);
                }
                else {
                    alert(json.message);
                }
            },
            error:function(data){
                alert("error");
            }
        });
    });

    $("#upload-rss").change(function(){
        var $formFile = $(this);
        var $form = $formFile.parents("form");
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