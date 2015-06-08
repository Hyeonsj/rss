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

    var youtube_id =  "hpwLXEmuAco";

    $.ajax({
        url: "https://www.googleapis.com/youtube/v3/videos",
        data : "part=contentDetails&id="+youtube_id+"&key=AIzaSyBpvYuLgghcrczsYfkpjhnKvFLXV4_lLxE",
        success: function (data) {
            console.log(convert_time(data.items[0].contentDetails.duration));
        },
        error: function (data) {
            console.log(data);
        }
    });

    function convert_time(duration) {
        var a = duration.match(/\d+/g);

        duration = "";

        if (a.length == 3) {
            duration = a[0]+":";
            duration = duration + a[1]+":";
            duration = duration + a[2];
        }

        if (a.length == 2) {
            duration = duration + a[0]+":";
            duration = duration + a[1];
        }

        if (a.length == 1) {
            duration = duration + a[0];
        }
        return duration
    }
});