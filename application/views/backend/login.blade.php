<!DOCTYPE html>
<html>
<head>

  <title>Portal Login</title>

  <link rel="shortcut icon" type="image/x-icon" href="{{ URL::base()}}/img/favicon.ico" />

  <link href='css/login.css' rel='stylesheet' type='text/css'>

  {{ HTML::script('js/common/jquery-1.8.3.min.js') }}

</head>
<body>


	<div class="login_center">

	  <div class="span12">

	    <div class="row-fluid">

	      <div class="span7">

	    <?php echo Form::open('login/safe', 'POST',array('id' => 'login-form' )); ?>

         @if( Session::has('login_errors') )

           <span class='error-message'>{{ __('common.login_errors') }}</span>

              <script type="text/javascript">

                  $(document).ready(function(){

                      $('#email').addClass('error-input');

                      $('#password').addClass('error-input');

                  });

              </script>

          @elseif(Session::has('request_invalid') )

                <span class='error-message' id="err_message">{{ __('common.invalid_request') }}</span>


          @elseif(Session::has('password_sent') )

                <span class='error-message' id="err_message"> {{ __('common.a_new_password_sent') }} <br/></span>

          @endif

          <table id='my-account-table'>

            <tr>

              <td>{{ __('common.email_or_username') }}:</td>

              <td><input type="text" name="email_or_username" id="email" /></td>

            </tr>

            <tr>

              <td>{{ __('common.password') }}:</td>

              <td><input type="password" name="password" id="password" /></td>

            </tr>

            <tr>

              <td colspan='2'>&nbsp;</td>

            </tr>

            <tr>

              <td><a href="#" id='show-toggle' onclick="show_forgot_pass()">{{ __('common.forgot_password?') }}</a></td>

              <td><input type="submit" value="{{ __('common.login') }}" class='right'/></td>

            </tr>

          </table>

      		<?php echo Form::close(); ?><!--end login -->

    		<form id="reset-password" accept-charset="UTF-8" onsubmit="reset_password();return false;">


                @if(Session::has('reset_errors') )

                        <span class='error-message'>

                             {{ __('common.authentication_invalid') }}

                        </span><!-- END ERROR SPAN -->

                <script type="text/javascript">

                    $(document).ready(function(){

                        $('#reset_email').addClass('error-input');
                    });

                </script>

                @endif

                <span>{{ __('common.reset_password') }}</span>

	          <br /><br />

                <span class="error-message" id="err_message"  style='display:none;'> </span>

                 <span class="alert-success" id="success_message"  style='display:none;'>

                        <br /> {{ __('common.info_about_password_recovery') }}<br />

                 </span>

            <table>

              <tr>

                <td>{{ __('common.email') }}:</td>

                <td><input type='text' name="reset_email" id='reset_email' /></td>

              </tr>

              <tr>

                <td>&nbsp;</td>

              </tr>

              <tr>

                <td><a href="#" id='back' onclick="show_login_form()"><?php echo __('common.back') ?></a></td>

                <td><input type='button' onclick="reset_password();return false;" value='<?php echo __('common.reset') ?>' class='right'/></td>

              </tr>


            </table>

	      </form><!--end reset -->

	      </div><!--end span 9 out of 12 -->

	      <div class="span5">

	      	<a href="{{Url::base()}}/login"><img src='{{URL::base()}}/img/logo/logo_login.png' alt='xPortal' title='xPortal Login' /></a>

          <div class='clear'></div>

	      </div><!--end span 3 out of 12 -->

        <div class='clear'></div>

	    </div><!--end row fluid -->

	  </div><!--end span max -->

	</div><!--end row fluid -->




<script type="text/javascript">


    function show_forgot_pass()
    {

        $('#login-form').hide();

        $('.error-message').hide();

        $('#reset-password').show();

    }


    function show_login_form()
    {

        $('#reset-password').hide();

        $('#login-form').show();

    }



      $('.submit-container>a').click(function(){

        $('form').show();

        $(this).parents('form').hide();

      });

      //this script removes yellow background for Chrome when password and user are saved.
      function setVals() {
      $('input:-webkit-autofill').each(function () {

      var text = $(this).val();
      var name = $(this).attr('name');

      $(this).after(this.outerHTML).remove();//remove chrome's presets

      $('input[name=' + name+']').val(text);//add presaved email and password.

      });

      }//end setVals

      $(window).load(function () {

          setVals();//call 1

          setTimeout("setVals()", 100);//call 2

          setTimeout("setVals()", 200);//call 3

          setTimeout("setVals()", 300);//call 4



      });

     function error_fn(){

      $('#email').addClass('error-input');

      $('#password').addClass('error-input');

     }


      function reset_password() {

          //hide send button
          $('#reset').hide();


          //empty errors area
          $('#err_message').show();



          /*
           *
           *  Initialize Variables
           *
           */
          var BASE ='{{ URL::base() }}';

          var err = 0;

          var err_message = '';

          var email = $('input[name="reset_email"]').val();

          var csrf_token = $('input[name="csrf_token"]').val();


          /*
           *
           *  Check email
           *
           */
          var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

          if( re.test( email ) == false){

              err_message += 'Email format is invalid.';
              err = 1;

              $('#reset_email').addClass('error-input');

          }//if email


          /*
           *
           *  If valid email
           *
           */
          if( err == 0 ){



              $.ajax({
                  type: 'POST',
                  url:  BASE+'/reset_password',
                  data: {
                      email:          email,
                      csrf_token:     csrf_token

                  },//data


                  dataType:'json',

                  success: function(json){

                      if(json.error === 1){


                          $('#err_message').show();

                          $('#success_message').hide();

                          $('#err_message').html(json.message);

                          //show send button
                          $('#reset').show();

                      }//if json.error == 1

                      else{


                          $('#err_message').hide();

                          $('#form').hide();

                          $('#info').hide();

                          $('#reset_table').hide();

                          $('#reset').hide();

                          $('#success_message').fadeIn();



                      }//else


                  }//success

              });//ajax

          }//if there are no errors
          else{


              //show errors
              $('#err_message').show();
              $('#err_message').html(err_message);

              //show send button
              $('#reset').show();

          }//if there are errors


      }//reset_password

</script>

</body>
</html>