$(document).ready(function() {
  $('#forgot-password').click(function(e) {
    e.preventDefault();
    $('.form-wrapper').toggle('500');
  });
  $('#login').click(function(e) {
    e.preventDefault();
    $('.form-wrapper').toggle('500');
  });
  
  var signinForm = $('#signIn');
  $('#username', signinForm).focusout(function() {
    if (!usernameIsValid(signinForm)) {
      $(this).addClass('has-error');
    } else {
      $(this).removeClass('has-error');
    }
  });
  
  $('#password', signinForm).focusout(function() {
    if (!passwordisValie(signinForm)) {
      $(this).addClass('has-error');
    } else {
      $(this).removeClass('has-error');
    }
  });
  
  signinForm.submit(function() {
    return usernameIsValid(signinForm) && passwordisValie(signinForm);
  });
});

function usernameIsValid(signInForm) {
  return $('#username', signInForm).val().length > 3;
}

function passwordisValie(signInForm) {
  return $('#password', signInForm).val() !== "";
}