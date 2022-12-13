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
