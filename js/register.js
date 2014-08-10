var mailregex = /(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/;
$(document).ready(function() {
  var signupForm = $('#signup-form');
  signupForm.submit(function() {
    return passwordIsValid(signupForm) && mailIsValid(signupForm);
  });
  
  $('input[name=email]', signupForm).focusout(function() {
    if (!mailIsValid(signupForm)) {
      $(this).addClass('has-error');
      if ($('#mailError').length === 0) {
        $('#signup-form').prepend('<div id="mailError" class="alert alert-danger" style="display:none;">Please enter a valid E-Mail address.</div>');
      }
      
      $('#mailError').fadeIn('slow');
    } else {
      $('#mailError').fadeOut('slow');
      $(this).removeClass('has-error');
    }
  });
  
  $('input[name=verify_password]').focusout(function() {
    if (passwordIsValid(signupForm)) {
      $('#passwordError').fadeOut('slow');
      $('input[type=password]', signupForm).removeClass('has-error');
      return true;
    } else {
      if ($('#passwordError').length === 0) {
        $('#signup-form').prepend('<div id="passwordError" class="alert alert-danger" style="display:none;">Passwords do not match.</div>');
      }
      $('input[type=password]', signupForm).addClass('has-error');
      $('#passwordError').fadeIn('slow');
    }
  });
});

function passwordIsValid(signupForm) {
  return $('input[name=password]', signupForm).val() !== "" && $('input[name=password]', signupForm).val() === $('input[name=verify_password]', signupForm).val();
}

function mailIsValid(signupForm) {
  return $('input[name=email]', signupForm).val().match(mailregex) !== null;
}