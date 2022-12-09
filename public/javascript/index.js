$( ".btn-menu" ).click(function() {
  $(".nav-right").addClass('clicked');
  $(".btn-close-menu").show();
  $(".btn-menu").hide();
});

$( ".btn-close-menu" ).click(function() {
  $(".nav-right").removeClass('clicked');
  $(".btn-close-menu").hide();
  $(".btn-menu").show();
});


// DARK THEME
$( ".dark-theme" ).click(function() {
  $(".wrapper").addClass('dark-body');
  $(".dark-theme").hide();
  $(".light-theme").show();
});

$( ".light-theme" ).click(function() {
  $(".wrapper").removeClass('dark-body');
  $(".dark-theme").show();
  $(".light-theme").hide();
});