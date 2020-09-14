$(document).ready(function(){
    $('.login-wrapper').addClass('fade-in');
   });

function delay(URL) {
    $(".login-wrapper").removeClass("fade-in");
    $(".login-wrapper").addClass("fade-out");
    setTimeout(function () {
        window.location = URL;
    }, 250)
};