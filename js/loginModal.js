$(document).ready(function() {
  $('#forgot-password').click(function(e) {
    e.preventDefault();
    $('.form-wrapper').toggle('500');
  });
  $('#login').click(function(e) {
    e.preventDefault();
    $('.form-wrapper').toggle('500');
  });
});