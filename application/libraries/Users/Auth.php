<?php

/**------------------------------------------------------------------------------------------------
 *
 * Users_Auth class
 *
 * It's used to handle authentication
 *
 **-----------------------------------------------------------------------------------------------*/

class Users_Auth{



    /**
     *
     * Checks if a user is logged in
     *
     */
    public static function is_logged()
    {

        return Session::has('user');

    }//is_logged



    /*--------------------------------------------------------------------------------------------------
     *
     * Attempt Login
     *
     *-------------------------------------------------------------------------------------------------*/
    public static function attempt_login($email_or_username, $password)
    {

        $attempt = Backend_User::check_credentials($email_or_username, $password);
   

        if( count( $attempt ) === 1 ){


                    $session_data = array(
                        'id'   		   => $attempt[0]->id,
                        'type'         => $attempt[0]->user_type,
                        'permissions'  => Backend_User_Permission::get_only_permissions($attempt[0]->id),
                        'security'	   => array(
                                                    'ip' => Request::ip()
                                                )
                    );

                $session_data['name'] = Backend_User::get_account_name($session_data['id']);

                Session::put('user', $session_data);

                return true;

        }//if we have the correct email and password

        /*
         *
         *
         * Lets check how many attempts were in the last 30 min
         *
         * If there were more than 10 we block the ip
         *
         *
         */
        Users_Security::check_login_attempts();


        return false;

    }//attempt_login




    /*
     *
     * Used to check additional things:
     *
     * The IP (maybe someone stole the cookie)
     *
     * TO ADD: user agent
     *
     */
    public static function check_session()
    {

        //Check IP
        if( Session::get('user.security.ip') != Request::ip() )
        {

            //empty session
            Session::forget('user');

            //redirect to login
            return Redirect::to('login');

        }//if the IP is not right


    }//check_session




    public static function has_access($module_id)
    {

        if (!Users_Auth::is_logged()) return false;

        $permissions = Session::get('user.permissions');

        if( in_array($module_id, $permissions) )
        {
            return true;
        }//if it has access


        return false;

    }//if it has access to a module




    public static function is_admin()
    {

        //3 is the id of portal - admin
        if( in_array(3, Session::get('user.permissions')) )
        {

            return true;

        }//if is admin


            return false;

    }//is admin


    public static function is_regular_user()
    {

        if( Session::get('user.user_type') == 2 )
        {

            return true;

        }//if is regular user


            return false;

    }//is_regular_user



    public static function logout()
    {

        Session::forget('user');

    }//logout


}//end class