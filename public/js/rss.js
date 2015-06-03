/**
 * Created by hyeonseungjae on 15. 5. 30..
 */


jQuery(function($){
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
});