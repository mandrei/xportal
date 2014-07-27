<?php

class Backend_Authentication_Login_Controller extends xPortal_Controller
{


    public function get_index()
    {


        /*
         * Load the view
         */
        return View::make('backend.login');

    }//GET index


    public function post_index()
    {

        /*
         * Gather data from inputs
         */
         $user_data = array(
            'email_or_username' => Input::get('email_or_username'),
            'password'          => Input::get('password')
        );

         
        if( Users_Auth::attempt_login($user_data['email_or_username'], $user_data['password']) )
        {


                
                if (Users_Auth::has_access(2)){

                    return Redirect::to('safe/portal');
                }
                else if(Users_Auth::has_access(6)){

                   return Redirect::to('safe/users');
                }
                 else if(Users_Auth::has_access(7)){

                   return Redirect::to('safe/clients');
                }
                 else if(Users_Auth::has_access(8)){

                   return Redirect::to('safe/settings');
                }

            
               // return Redirect::to('safe/home');

        }//if the login credentials are ok
        else
        {
           return Redirect::to('login')->with('login_errors', true);

        }//login credentials are invalid


    }//POST index



    public function post_reset_password() {


        if( Request::ajax() ){

            /*
             *
             *  Validate
             *
             */
            $validation_rules = array(
                'email'              => 'required|min:5|max:150|email|exists:users,email'
            );



            $data = array(
                'email'             => Input::get('email')
            );

            

            $validation = Validator::make($data , $validation_rules);


            if( $validation->passes() ) {


                /*
                 *
                 *  Check if the user made another request in the last 2 hours
                 *
                 */
                $request = Backend_ForgotPassword::check_request($data['email']);


                //if no request was made in the last 2 hours
                if ( count($request)  == 0 ) {


                    //get user's info by email
                    $user = Backend_User::get_id_by_email($data['email']);

                    $user  = $user[0];



                    $name = Backend_User::get_name_for_emails($user->id);

                   

                    /*
                     *
                     *  Create a forgot password request
                     *
                     */
                    $reset_data = array(
                        'user_id'          => $user->id,
                        'time'             => date('Y-m-d H:i:s'),
                        'random_string'    => Str::random(20)
                    );

                    Backend_ForgotPassword::insert($reset_data);



                    /*
                     *
                     *  Get user's language
                     *
                     */
                    $user_language = Backend_Language::get_abbr_by_id( $user->language_id );


                    $user_language = strtolower($user_language);

                    /*
                     *
                     *  Send email to user
                     *
                     */

                    //email header
                    $email_from  = Backend_Settings::r('system_email');
                    $email_name  = Backend_Settings::r('system_name_email');

                    $link = URL::to('reset_password/'.$reset_data['random_string']);


                    //PLain
                    $plain_message = __('common.plain_message_forgot_password', array('name' => $name, 'link'=> $link ) , $user_language );

                    //HTML
                    $html_message = __('common.html_message_forgot_password', array('link'=> $link), $user_language );


                    $data_email = array(
                        'title'   => __('common.html_message_forgot_password_title' , array('name' => $name), $user_language ),
                        'message' => $html_message
                    );

                    $html_message_body = render('backend.email.email', $data_email);


                    /*
                     *
                     *  Send email
                     *
                     */
                    MailHelper::send($user->email, $email_from, $email_name, $plain_message, $html_message_body, $email_name, __('common.subject_reset_password', array(), $user_language));


                    echo json_encode(
                        array(
                            'error'   => 0,
                            'message' => 'Success'
                        ));

                    return;



                }//if no request in the last 2 hours
                else {


                    echo json_encode(
                        array(
                            'error'   => 1,
                            'message' => 'A request to reset your password has been sent in the last 2 hours. <br />
                                           Please check your email.'
                        ));

                    return;



                }//if a request was already made



            }//if validation passed
            else{

                echo json_encode(
                    array(
                        'error'   => 1,
                        'message' => 'Invalid email address.'
                    ));

                return;

            }//else if didn't pass validation


        }//if it's an ajax request




    }//reset password



    public function get_reset_password($string) {


        /*
         *
         *  Delete all requests made < 2 hours ago
         *
         */
        Backend_ForgotPassword::clear_requests();

        /*
         *
         *  Check if request exists - get user_id
         *
         */
        $user_id = Backend_ForgotPassword::check_string($string);

        

        if ( $user_id ) {


            /*
             *
             *  Get user
             *
             */
            $user = Backend_User::find($user_id);


            /*
             *
             *  Update user's password
             *
             */
            $new_password   = Str::random(6,'alpha');

            $user->password = Security::encrypt( $new_password );

            $user->save();


            /*
             *
             *  Get user's language
             *
             */
            $user_language = Backend_Language::get_abbr_by_id( $user->language_id );

            $user_language = strtolower($user_language);

            $name = Backend_User::get_name_for_emails($user->id);


            /*
             *
             *  Mail to user - new password
             *
             */


            $email_from  = Backend_Settings::r('system_email');
            $email_name  = Backend_Settings::r('system_name_email');

            $link = URL::to('login');

            //Plain
            $plain_message = __('common.plain_message_get_reset_password', array('name' => $name,'new_password' => $new_password, 'link'=> "{$link}" ), $user_language );


            //HTML
            $html_message = __('common.html_message_get_reset_password', array('new_password' => $new_password, 'link'=> "{$link}" ), $user_language);


            $data_email = array(
                'title'   => __('common.html_message_get_reset_password_title', array('name' => $name ), $user_language ),
                'message' => $html_message
            );

            $html_message_body = render('backend.email.email', $data_email);



            MailHelper::send($user->email, $email_from, $email_name, $plain_message, $html_message_body, $email_name,__('common.subject_reset_password', array(), $user_language) );


            Backend_ForgotPassword::where('random_string','=',$string)->delete();


            return Redirect::to('login')->with('password_sent',true);


        }//if request was made
        else {

            return Redirect::to('login')->with('request_invalid',true);

        }//if no request found


    }//GET reset password


}//end class