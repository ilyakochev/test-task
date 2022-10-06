
function emailValidation( $email ) {
  var regexp = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,6})+$/;
  return regexp.test( $email );
}

function nameValidation( $user_name ) {
  var regexp = /^([a-zA-Z0-9])+$/;
  return regexp.test( $user_name );
}

$("#add_message_form").submit(
     function() {
      var $email = $('#email').val();
      var $user_name = $('#user_name').val();
      console.log( $email );
      console.log( $user_name );
          if( !emailValidation( $email )) {
            alert( 'Не верно указан адресс E-mail!' );
            return false;
          } else {
            if( !nameValidation( $user_name )) {
            alert( 'Допустимы только цифры и буквы латинского алфавита!' );
            return false;}
            else{
              return true;
            }
          }
     }
);

