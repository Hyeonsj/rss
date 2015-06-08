$(function(){

    function get_date() {
        var now = new Date();
        var year = now.getFullYear();
        var mon = (now.getMonth() + 1) > 9 ? '' + (now.getMonth() + 1) : '0' + (now.getMonth() + 1);
        var day = now.getDate() > 9 ? '' + now.getDate() : '0' + now.getDate();

        return year + '_' + mon + '_' + day;
    }

    var $sampleList = $("#sample-list");
    var $sampleForm = $("#modal-sample-form");

    $sampleList.on("click", "button[data-target=#modal-sample-form]", function(){
        var sample_id = $(this).attr("data-sample-id");
        $sampleForm.find("input").val("");
        $sampleForm.find("select option").prop("selected", false);

        if(sample_id) {
            $.ajax({
                url:"/admin/getSample/"+sample_id,
                dataType:"json",
                type:"get",
                success:function(json){
                    if(json.status=='success') {
                        $sampleForm.find("[name=sample_id]").val(json.data.sample_id);
                        $sampleForm.find("[name=title]").val(json.data.title);
                        $sampleForm.find("[name=link]").val(json.data.link);
                        $sampleForm.find("[name=copyright_link]").val(json.data.copyright_link);
                        $sampleForm.find("[name=language_id]").val(json.data.language_id);
                        $sampleForm.find("[name=interest_id]").val(json.data.interest_id)
                    }
                    else {
                        alert(json.message);
                    }
                },
                error:function(data){
                    alert("error");
                }
            });
        }
    });

    $("#btn-delete").on("click", function(){
        if(confirm("Are you sure you want to delete this item?")) {

            var sample_id = $sampleForm.find("[name=sample_id]").val();
            $.ajax({
                url:"/admin/deleteSample/"+sample_id,
                dataType:"json",
                type:"get",
                success:function(json){
                    if(json.status=='success') {
                        alert(json.message);
                        location.reload();
                    }
                    else {
                        alert(json.message);
                    }
                },
                error:function(data){
                    alert("error");
                }
            });
        }
    });

    $("form[method=post]").submit(function(e){
        e.preventDefault();

        var $form = $(this);
        var successReturnUrl = $form.attr("data-success-return-url");
        var errorReturnUrl = $form.attr("data-error-return-url");

        $form.ajaxSubmit({
            type : 'post',
            dataType : 'json',
            iframe : true,
            target : '#hidden-iframe',
            success : function(json) {
                if(json.status=="success") {
                    var message = $form.attr("data-success-message");
                    if (typeof message !== typeof undefined && message !== false) {
                        if(message) {
                            alert(message);
                        }
                    }
                    else {
                        alert(json.message);
                    }

                    if(successReturnUrl) {
                        location.href = successReturnUrl;
                    }
                    else {
                        location.reload(true);
                    }
                }
                else {
                    alert(json.message);

                    if(errorReturnUrl) {
                        location.href = errorReturnUrl;
                    }
                    else {
                        if(json.data) {
                            $form.find("[name="+json.data+"]").focus();
                        }
                    }
                }
            },
            error : function(data) {
                //alert("error");

            },
            complete : function(data) {
            }
        });
    });

    $userclassTable = $("#userclass-table")
    if($userclassTable.length > 0){

        $(".btn-approve").on("click", function(){
            var classroom_id = $(this).attr("data-classroom-id");

            $.ajax({
                url : "/admin/toggleClassroomStatus/"+classroom_id,
                type : "post",
                dataType : "json",
                success : function(json) {
                    if(json.status=='success') {
                        location.reload();
                    }
                    else {
                        alert(json.message);
                    }
                }
            });
        });

        $(".btn-delete").on("click", function(){
            var classroom_id = $(this).attr("data-classroom-id");

            if(confirm("Are you sure you want to delete this classroom?")) {
                $.ajax({
                    url: "/teach/delete/" + classroom_id,
                    type: "post",
                    dataType: "json",
                    success: function (json) {
                        console.log(json);
                        if (json.status == 'success') {
                            location.reload();
                        }
                        else {
                            alert(json.message);
                        }
                    }
                });
            }

        });


    }


    $users = $("#users");
    if($users.length > 0){
        var $userForm = $("#modal-user-form");


        $users.on("click", "button[data-target=#modal-user-form]", function(){
            var user_id = $(this).attr("data-user-id");

            $userForm.find("input[type=input]").val("");
            $userForm.find("select option").prop("selected", false);

            if(user_id) {
                $.ajax({
                    url:"/admin/getUser/"+user_id,
                    dataType:"json",
                    type:"get",
                    success:function(json){
                        if(json.status=='success') {
                            //console.log(json.data)
                            $userForm.find("[name=user_id]").val(json.data.user_id);
                            $userForm.find("[name=nickname]").val(json.data.nickname);
                            $userForm.find("[name=email]").val(json.data.email);
                            $userForm.find("[name=point]").val(json.data.point);
                            if(json.data.is_admin==true){
                                $userForm.find("[name=is_admin]").prop("checked",true);
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
            }
        });

        $("#btn-user-delete").on("click", function(){
            if(confirm("Are you sure you want to delete this user?")) {

                var user_id = $userForm.find("[name=user_id]").val();
                $.ajax({
                    url:"/admin/deleteUser/"+user_id,
                    dataType:"json",
                    type:"get",
                    success:function(json){
                        if(json.status=='success') {
                            alert(json.message);
                            location.reload();
                        }
                        else {
                            alert(json.message);
                        }
                    },
                    error:function(data){
                        alert("error");
                    }
                });
            }
        });
    }

    $language = $("#language");
    if($language.length > 0){
        $(".btn-delete").on("click", function(){
            if(confirm("Are you sure you want to delete this language?")) {

                var language_id = $(this).attr("data-language-id");
                    $.ajax({
                        url: "/admin/deleteLanguage/" + language_id,
                        type: "post",
                        dataType: "json",
                        success: function (json) {
                            console.log(json);
                            if (json.status == 'success') {
                                location.reload();
                            }
                            else {
                                alert(json.message);
                            }
                        },
                        error: function(a1, a2, a3){
                          alert(json.message);
                        }
                    });

            }
        });

        $(".language-edit").on("click", function(){
            var language_id = $(this).attr("data-language-id");
            var $languageForm = $("#modal-language-form");
            $languageForm.find("input[type=input]").val("");

                $.ajax({
                    url: "/admin/getLanguage/" + language_id,
                    type: "post",
                    dataType: "json",
                    success: function (json) {
                        if (json.status == 'success') {
                            console.log(json.data);
                            $languageForm.find("input[name=language_id]").val(json.data.language_id);
                            $languageForm.find("input[name=language_title]").val(json.data.language);

                        }
                        else {
                            alert(json.message);
                        }
                    },
                    error: function(a1, a2, a3){
                        alert(json.message);
                    }
                });
        });
    }

    $interest = $("#interest");
    if($interest.length > 0){
        $(".interest-edit").on("click", function(){
            var interest_id = $(this).attr("data-interest-id");
            var $interestForm = $("#modal-interest-form");
            $interestForm.find("input[type=input]").val("");

            $.ajax({
                url: "/admin/getInterest/" + interest_id,
                type: "post",
                dataType: "json",
                success: function (json) {
                    console.log(json);
                    if (json.status == 'success') {
                        console.log(json.data);
                        $interestForm.find("input[name=interest_id]").val(json.data.interest_id);
                        $interestForm.find("input[name=interest_title]").val(json.data.interest);
                    }
                    else {
                        alert(json.message);
                    }
                },
                error: function(a1, a2, a3){
                    alert(json.message);
                }
            });
        });

        $(".btn-delete").on("click", function(){
            if(confirm("Are you sure you want to delete this category?")) {

                var interest_id = $(this).attr("data-interest-id");
                $.ajax({
                    url: "/admin/deleteInterest/" + interest_id,
                    type: "post",
                    dataType: "json",
                    success: function (json) {
                        console.log(json);
                        if (json.status == 'success') {
                            location.reload();
                        }
                        else {
                            alert(json.message);
                        }
                    },
                    error: function(a1, a2, a3){
                        alert(json.message);
                    }
                });

            }
        });
    }

});