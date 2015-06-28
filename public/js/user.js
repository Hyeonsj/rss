/**
 * Created by hyeonseungjae on 15. 6. 24..
 */

$(function() {


    $("button[name='go']").on("click", function () {

        var $form = $("form[method=post]");
        var successReturnUrl = $form.attr("data-success-return-url");
        var errorReturnUrl = $form.attr("data-error-return-url");
        $form.find(".has-error").removeClass("has-error");
        $form.find(".error-block").each(function () {
            $(this).remove();
        });



        $form.ajaxSubmit({
            type: 'post',
            dataType: 'json',
            iframe: true,
            target: '#hidden-iframe',
            success: function (json) {
                if (json.status == "success") {
                    var message = $form.attr("data-success-message");
                    if (typeof message !== typeof undefined && message !== false) {
                        if (message) {
                            //alert(message);
                        }
                    }
                    else {
                        //alert(json.message);
                    }

                    if (successReturnUrl) {
                        location.href = successReturnUrl;
                    }
                    else {
                        location.reload(true);
                    }
                }
                else {
                    alert(json.message);
                    if (errorReturnUrl) {
                        location.href = errorReturnUrl;
                    }
                    else {

                        if (json.data) {
                            $form.find("[name=" + json.data + "]").focus();
                            if (json.data == "link") {
                                $form.find("[name=link]").parent().addClass("has-error");
                                $form.find("[name=copyright_link]").parent().addClass("has-error");
                                $form.find("[name=copyright_link]").parent().append("<span class='error-block'>" + json.message + "</span>");

                            }
                            else if (json.data == "played_at") {

                                var $requirde_field_list = $form.find("[name='" + json.data + "[]']");
                                $requirde_field_list.each(function () {
                                    if ($(this).val() == "") {
                                        $(this).parent().addClass("has-error");
                                        $(this).parent().append("<span class='error-block'>" + json.message + "</span>");
                                        return false;
                                    }
                                });
                            }
                            else {

                                $form.find("[name='" + json.data + "']").parent().addClass("has-error");
                                $form.find("[name='" + json.data + "']").parent().append("<span class='error-block'>" + json.message + "</span>");
                            }
                        }
                    }
                }
            },
            error: function (data) {
                //alert("error");
            },
            complete: function (data) {

            }
        });

    });

});