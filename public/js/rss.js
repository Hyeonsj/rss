/**
 * Created by hyeonseungjae on 15. 5. 30..
 */


jQuery(function($){
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
        if($("#wrapper").hasClass("toggled")){
            $("#container_warpper").css("margin-left", "8.5%");
        }
        else{
            $("#container_warpper").css("margin-left", "0");
        }
    });



});

jQuery(document).ready(function($){

    $('#menu').metisMenu({
        toggle: false
    });


    $(document).on('click', ".dropdown-menu li a", function(){
        var $selected_type = $(this).attr("data-menu-type");
        var $rss_url = $(document).find("#page-content-wrapper .container #container_warpper .rss_url").val();
        var $tag_i = $(this).closest(".dropdown-menu").siblings(".dropdown-toggle").find(".fa");
        if($selected_type == "list"){
            $tag_i.removeClass("fa-th").removeClass("fa-th-list").addClass("fa-bars");
        }
        else if($selected_type == "gally"){
            $tag_i.removeClass("fa-th-list").removeClass("fa-bars").addClass("fa-th");
        }
        else if($selected_type == "blog"){
            $tag_i.removeClass("fa-th").removeClass("fa-bars").addClass("fa-th-list");
        }


        $.ajax({
            url:"/rss/getContent/",
            data: "rss_url="+$rss_url+"&view_type="+$selected_type,
            type:"post",
            dataType:'json',
            success:function(json){
                if(json.status=='success') {
                    var $data = json.data;
                    console.log(json.data);
                    if($data.view_type == "blog"){
                        $("#rss_content").removeClass("list-group").removeClass("gally").addClass("blog-group");
                    }
                    else if($data.view_type == "gally"){
                        $("#rss_content").removeClass("list-group").removeClass("blog-group").addClass("gally");
                    }
                    else if($data.view_type == "list"){
                        $("#rss_content").removeClass("gally").removeClass("blog-group").addClass("list-group");
                    }

                    $("#rss_content").html($data.html);

                    var $content_img = $("#rss_content .item .rss_img");
                    for(var $index = 1; $index <=$content_img.length; $index++){
                        if($data.image_list[$index-1] != ""){
                            $content_img.eq($index).attr("src", $data.image_list[$index-1]);
                        }
                        else{
                            $content_img.eq($index).css("display", "none");
                        }
                    }
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


    $("#menu ul.collapse a").on("click", function(){
        var $rss_url = $(this).attr("data-rss-url");

        $(document).find("#page-content-wrapper .container #container_warpper .rss_url").val($rss_url);

        $.ajax({
            url:"/rss/getContent/",
            data: "rss_url="+$rss_url+"",
            type:"post",
            dataType:'json',
            success:function(json){
                if(json.status=='success') {
                    var $data = json.data;
                    console.log($data);
                    if($data.view_type == "blog"){
                        $("#rss_content").removeClass("list-group").removeClass("gally").addClass("blog-group");
                    }
                    else if($data.view_type == "gally"){
                        $("#rss_content").removeClass("list-group").removeClass("blog-group").addClass("gally");
                    }
                    else if($data.view_type == "list"){
                        $("#rss_content").removeClass("gally").removeClass("blog-group").addClass("list-group");
                    }

                    $("#rss_content").html($data.html);
                    var $content_img = $("#rss_content .item .rss_img");
                    for(var $index = 1; $index <=$content_img.length; $index++){
                        if($data.image_list[$index-1] != ""){
                            $content_img.eq($index).attr("src", $data.image_list[$index-1]);
                        }
                        else{
                            $content_img.eq($index).css("display", "none");
                        }

                    }
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
                    $("#menu1").html(json.data);
                    console.log(json.data);
                }
                else if(json.status == "failure") {
                    alert(json.message);
                }
            },
            error:function(data) {
                alert("error");
            },
            complete:function () {
                //if ($.browser.msie) {
                //    $formFile.replaceWith($formFile.clone());
                //}
                //else {
                //    $formFile.val('');
                //}
            }
        });
        return false;
    });

});