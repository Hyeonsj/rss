$(function(){

    $('input, textarea').placeholder();

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

    $(".btn-follow-action").each(function(i,val){
        var $btn = $(val);
        var user_id = $btn.attr("data-user-id");
        $.ajax({
            url : "/communication/getFollow",
            type : "get",
            dataType : "json",
            data : "user_id="+user_id,
            success : function(json) {
                if(json.status=='success') {
                    if(json.data) {
                        $btn.addClass("active");
                    }
                    else {
                        $btn.removeClass("active");
                    }
                }
            },
            error : function(data) {
                //alert("error");
            }
        });
    });

    $(".btn-follow-action").on("click", function(e){
        e.preventDefault();
        var $btn = $(this);
        var user_id = $btn.attr("data-user-id");
        var classroom_id = $btn.attr("data-classroom-id");
        var discussion_id = $btn.attr("data-discussion-id");
        var vocabulary_id = $btn.attr("data-vocabulary-id");
        var vocabulary_language_id = $btn.attr("data-vocabulary-language-id");
        $.ajax({
            url : "/communication/follow",
            type : "post",
            dataType : "json",
            data : "user_id="+user_id,
            success : function(json) {
                if(json.status=='success') {
                    if(json.data) {
                        $btn.addClass("active");
                    }
                    else {
                        $btn.removeClass("active");
                    }
                }
                else if(json.status == 'error') {
                    if(json.message == 'Permission denied.') {
                        $("#login").modal("show");
                    }
                }
            },
            error : function(data) {
                //alert("error");
            }
        });
    });

    var $filter = $("#filter");
    if($filter.length > 0) {
        $("select").on("change", $filter, function(){
            $filter.submit();
        });
    }


    //facebook 로그인 부분
    var $btnFacebookLogin = $(".btn-facebook-login");
    if($btnFacebookLogin.length > 0) {
        $(document).on("click", ".btn-facebook-login",function() {
            FB.login(function() {
                checkLoginState();
            },{scope: 'email, publish_actions'});
            return false;
        });
    }
    function checkLoginState() {
        FB.getLoginStatus(function(response) {
            if(response.status == 'connected') {
                var access_token = response.authResponse.accessToken;
                var facebook_id = null;
                FB.api('/me', function(response) {
                    facebook_id = response.id;
                    if(facebook_id) {
                        $.ajax({
                            url: '/auth/facebookLogin',
                            dataType:'json',
                            type: 'post',
                            data: 'access_token='+access_token+'&facebook_id='+facebook_id,
                            success: function(data) {
                                if(data.status == "success") {
                                    location.href = "/";
                                } else if(data.status == "error") {
                                    alert(data.message);
                                } else if(data.status == "islive") {
                                    alert(data.message);
                                }
                            },
                            error: function(data) {
                            }
                        });
                    } else {
                        alert(_tutto_multi_language.msg_invalid_access);
                    }
                });

            } else if(response.status == 'not_authorized') {

            } else {

            }
        });
    }

    $("#btn-sign-up-now").on("click", function(){
        $('#login').modal('hide');
        $('#signup').modal('show');
    });

    $(".news-alarm").on("click", function(e) {
        e.preventDefault();
        $.ajax({
            url: '/communication/updateNotification',
            type: 'get',
            dataType : "json",
            success: function(json) {
                if(json.status=='success') {

                }
                else {
                    alert(json.message);
                }
            },
            error: function(data) {
                alert("알 수 없는 error.");
            }
        });
    });

    $(".news-list").find("li").on("click", function(e) {
        e.preventDefault();
        var notification_id = $(this).attr("data-notification-id");
        $.ajax({
            url: '/communication/updateNotification',
            type: 'get',
            dataType : "json",
            data: "notification_id="+notification_id,
            success: function(json) {
                if(json.status=='success') {
                    location.href = json.data;
                }
                else {
                    alert(json.message);
                }
            },
            error: function(data) {
                alert("알 수 없는 error.");
            }
        });

    });

});

function signinCallback(authResult) {
    if (authResult['code']) {
        var code = authResult['code'];
        $.ajax({
            type: 'POST',
            url: '/auth/googleLogin',
            dataType : "json",
            success: function(json) {
                if(json.status=='success') {
                    location.reload(true);
                }
                else {
                    alert(json.message);
                }
            },
            error: function(data) {
            },
            processData: false,
            data: "code="+code
        });

    } else if (authResult['error']) {
        // 오류가 발생했습니다.
        // 가능한 오류 코드:
        //   "access_denied" - 사용자가 앱에 대한 액세스 거부
        //   "immediate_failed" - 사용자가 자동으로 로그인할 수 없음
        // console.log('오류 발생: ' + authResult['error']);
    }
}

function showLoginModal() {
    $("#login").modal("show");
}

