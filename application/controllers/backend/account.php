<?php

class Backend_Account_Controller extends xPortal_Controller
{

    public function get_user_edit_account()
    {


        /*
         * Get user id
         */
        $user_id = Session::get('user.id');


        $user = Backend_User::find_undeleted($user_id);

        if (!$user) return Response::error('404');


        $user_details = Backend_User::infos($user_id);

        $user_details = $user_details[0];


        /*
         * View data
         */
        $data = array(
            'selected_page'    => 'none',
            'breadcrumbs'      => array(
                                            // 'safe/home'    => __('common.home_page'),
                                            'last'         => __('common.account_settings')
            ),

            'user'             => $user_details,
            'languages'        => Backend_Language::all(),

        );

        return View::make('backend.account.user_edit',$data);

    }//GET edit


    public function post_user_edit_account()
    {

        $user_id = Session::get('user.id');


        $user = Backend_User::find_undeleted($user_id);

        if (!$user) return Response::error('404');


        /*
         * Validate data
         */
        $inputs = array (

            'first_name'     => Input::get('first_name'),
            'last_name'      => Input::get('last_name'),
            'username'       => Input::get('username'),
            'email'          => Input::get('email'),
            'address'        => Input::get('address'),
            'phone'          => Input::get('phone')

        );


        if( $user->validate($inputs, Backend_User::edit_validation($user->id)) )
        {

            /*
             * Update user
             */
            $user->email         = $inputs['email'];
            $user->username      = $inputs['username'];
            $user->language_id   = 1;//english

            //Update
            $user->save();


             /*
             * Get old first and last name for folder update
             *
             */
            $user = Backend_User::infos($user_id);
            $user = $user[0];

    
            $old_name = str_replace(' ','-',$user->last_name).'-'.str_replace(' ','-',$user->first_name).'-'.$user_id;



            /*
             * Update user details
             */
             $user_details = array(
                                    'first_name'   => $inputs['first_name'],
                                    'last_name'    => $inputs['last_name'],
                                    'phone'        => $inputs['phone'],
                                    'address'      => $inputs['address']
             );

             Backend_User_Details::where('user_id','=',$user_id)
                                 ->update($user_details);


           /*
            * Rename folder
            *
            */
            $new_name = str_replace(' ','-',$inputs['last_name']).'-'.str_replace(' ','-',$inputs['first_name']).'-'.$user_id;

            if($old_name != $new_name ) {

                $slash = Config::get('portal.back_slash');

                $folder = new Portal('users'.$slash.$old_name);

                if($folder->status() ) $folder->rename($slash.$new_name);

            }
     
            /*
             * Go to account edit
             */
            return Redirect::to('safe/account_user/edit')->with('updated', true);


        }//if it validates


        /*
         * return with errors
         */
        return Redirect::to('safe/account_user/edit')->with_errors($user->errors());

    }//POST edit_account



    public function get_client_edit_account()
    {

        /*
         * Get client id
         */
        $client_id = Session::get('user.id');


        $client = Backend_Client::find_undeleted($client_id);

        if (!$client) return Response::error('404');


        /*
         * Find the client
        */
        $client = Backend_Client::infos($client_id);

        $client = $client[0];



        /*
         * View data
         */
        $data = array(
            'selected_page'    => 'none',
            'breadcrumbs'      => array(
                // 'safe/home'    => __('common.home_page'),
                'last'         => __('common.account_settings')
            ),

            'client'             => $client,
            'countries'          => Backend_Country::get(),
            'marital_statuses'   => DB::table('marital_status')->get(),
            'individuals'        => Backend_Client_Details::get_client_individuals(),
            'corporations'       => Backend_Client_Details::get_client_corporations(),
            'states'             => Backend_State::all(),
            'provinces'          => Backend_Province::all(),
            'titles'             => Backend_Client_Individual_Title::all(),
            'positions'          => Backend_Position::all(),

        );


        return View::make('backend.account.client_edit',$data);

    }//GET edit


    public function post_client_edit_account()
    {

        $client_id = Session::get('user.id');

         $client = Backend_Client::find_undeleted($client_id);

        if (!$client) return Response::error('404');


        $client_info = Backend_Client::infos($client_id);

        $client_info = $client_info[0];


        /*
         *
         *  Update client details(common for individual and corporation)
         *
         */
        $client_details_id = Backend_Client_Details::where('user_id','=',$client_id)->take(1)->only('id');


        /*
         * Validate data
         */
        $inputs = Input::all();

        array_shift($inputs);

       /*
        * For phone validation (000) 000-0000
        */
        
        //Fax
        $inputs['fax'] = $inputs['fax1']."".$inputs['fax2']."".$inputs['fax3'];

        
        //Phone
        $inputs['phone'] = $inputs['phone1']."".$inputs['phone2']."".$inputs['phone3'];


        if($inputs['client_type'] == 1 )
        {
            
            if($inputs['birth_date'] == '' ) $inputs['birth_date'] = '0000-00-00 00:00:00';

        }//if its individual

     

        if( $client->validate($inputs, Backend_Client::edit_client($client_info->client_type_id,$client_id,$inputs['country'])) )
        {

            /*
             * Update client
             */
            $client->email         = strtolower($inputs['email']);
            $client->username      = $inputs['username'];
            $client->language_id   = 1;
            $client->email         = $inputs['email'];

            //Update
            $client->save();

            if($inputs['country'] == 1)
            {
                /*
                 * Update client details
                 */
                $client_details = array(

                                    'street_address'  => strtoupper($inputs['street_address']),
                                    'city'            => strtoupper($inputs['city']),
                                    'state'           => $inputs['state'],
                                    'zip_code'        => $inputs['zip_code'],
                                    'country'         => $inputs['country'],
                                    'phone'           => $inputs['phone'],
                                    'fax'             => $inputs['fax'],

            );

                $client_details['update_client'] = 1;

                Backend_Client_Details::where('user_id','=',$client_id)
                                      ->update($client_details);

            }//if its usa
            elseif($inputs['country'] == 2)
            {
                $client_details = array(

                'street_address'  => strtoupper($inputs['street_address']),
                'city'            => strtoupper($inputs['city']),
                'province'        => $inputs['province'],
                'postal_code'     => $inputs['postal_code'],
                'country'         => $inputs['country'],
                'phone'           => $inputs['phone'],
                'fax'             => $inputs['fax'],

            );

            $client_details['update_client'] = 1;

                Backend_Client_Details::where('user_id','=',$client_id)
                                      ->update($client_details);

            }//else its canada



             /*
             * Change the folder name
             */
            $client_type = strtolower(Backend_Client_Type::get_name_by_client_type_id($inputs['client_type']));

            $slash = Config::get('portal.back_slash');




            if($client_info->client_type_id == 1)
            {


                  $old_name =  str_replace(' ','-',$client_info->last_name). '-'.str_replace(' ','-',$client_info->first_name.'-'.$client_id);


                /*
                *
                *  Update client's folder name in the database and in folders
                *
                */
                if ( $client_info->first_name != strtoupper($inputs['first_name']) || $client_info->last_name != strtoupper($inputs['last_name'])  ) {

                  

                    $new_name = str_replace(' ','-',strtoupper($inputs['last_name'])) .'-'.str_replace(' ','-',strtoupper($inputs['first_name'])).'-'.$client_id;

            
                    $portal_folder = 'clients'.$slash.$client_type . $slash .$old_name;

                  
                    $portal = new Portal($portal_folder);

                    if( $portal->status() )
                    {
                        $portal->rename($new_name);

                    }//if the portal object exists


                    $name = $new_name;

                }//if first name or last name changed
                else {

                    $name = $old_name;
                }



                    if($client_info->calendar_year_start != $inputs['calendar_year_start']) {
                        $start_folder = $inputs['calendar_year_start'];
                    }




                //Update client individual
               Backend_Client_Individual::where('client_id','=',$client_details_id)
                                        ->update(array(

                                                            'title'                   =>  $inputs['title'],
                                                            'calendar_year_start'     =>  $inputs['calendar_year_start'],
                                                            'first_name'              =>  strtoupper($inputs['first_name']),
                                                            'initials'                =>  strtoupper($inputs['initials']),
                                                            'last_name'               =>  strtoupper($inputs['last_name']),
                                                            'default_client_name'     =>  strtoupper($inputs['default_client_name']),
                                                            'birth_date'              =>  $inputs['birth_date'],
                                                            'marital_status'          =>  $inputs['marital_status']

                    ));


             

            }// if its individual

            //Else if Corporation
            else if ($client_info->client_type_id == 2 )
            {

                $old_name = str_replace(' ','-',$client_info->name).'-'.$client_id;

            

                 /*
                *
                *  Chnage folders names
                *
                */
                 if ( $client_info->name  != strtoupper($inputs['name']) ) {

                 

                    $new_name = str_replace(' ','-',strtoupper($inputs['name'])).'-'.$client_id;

            
                    $portal_folder = 'clients'.$slash. $client_type . $slash .$old_name;

                    $portal = new Portal($portal_folder);

                    if( $portal->status() )
                    {
                        $portal->rename($new_name);

                    }//if the portal object exists


                    $name = $new_name;



                }//if first name or last name changed

                else {

                    $name  = $old_name;
                }


                  if($client_info->calendar_year_start != $inputs['corporation_calendar_year_start']) {
                        $start_folder = $inputs['corporation_calendar_year_start'];
                }


                $to_update = array(
                                                             'name'                             =>  strtoupper($inputs['name']),
                                                             'calendar_year_start'              =>  $inputs['corporation_calendar_year_start'],
                                                             'contact_person_last_name'         =>  $inputs['contact_person_last_name'],
                                                             'contact_person_first_name'        =>  $inputs['contact_person_first_name'],
                                                             'position'                         =>  $inputs['position']
                                                             
                                );



                 //Update client corporation
                Backend_Client_Corporation::where('client_id','=',$client_details_id)
                                          ->update($to_update);



            }//else if corporation




            /*
            *  Change subfolders
            *
            */
            if (isset($start_folder)) {


                $type_folder = new Portal('clients'.$slash.$client_type);

                $type_folder->create_user($name, $start_folder, $client_type);
            }


           /*
            *
            *  Send email to admin
            *
            */
            $email_from  = Backend_Settings::r('system_email');
            $email_name  = Backend_Settings::r('system_name_email');
            $alert_notification_email = Backend_Settings::r('alert_notification_email');


          //If its individual
          if($client_info->client_type_id == 1)
          {
            //Plain
            $plain_message2  = __('common.plain_message_client_edit_individual', array('first_name' => $client_info->first_name, 'last_name'  => $client_info->last_name ));

            //HTML
            $html_message2   =  __('common.html_message_client_edit_individual', array('first_name' => $client_info->first_name, 'last_name'  => $client_info->last_name));


            $data_email2     = array(
                'title'   => __('common.html_client_edit_title_notification'),
                'message' => $html_message2
            );

            $html_message_body2 = render('backend.email.email', $data_email2);



            /*
             *
             *  Send email
             *
             */
            MailHelper::send($alert_notification_email, $email_from, $email_name, $plain_message2, $html_message_body2,$email_name, __('common.client_edit_notification'));

         }//If its individual

          //If its corporation
          if($client_info->client_type_id == 2 )
          {
             //Plain
             $plain_message1  = __('common.plain_message_client_edit_corporation', array('name' => $client_info->name));

             //HTML
              $html_message1   =  __('common.html_message_client_edit_corporation', array('name' => $client_info->name));


              $data_email1     = array(
                    'title'   => __('common.html_client_edit_title_notification'),
                    'message' => $html_message1
                );

                $html_message_body1 = render('backend.email.email', $data_email1);



               /*
                *
                *  Send email
                *
                */
               MailHelper::send($alert_notification_email, $email_from, $email_name, $plain_message1, $html_message_body1,$email_name, __('common.client_edit_notification'));

            }//If its corporation


            /*
             * Go to account edit
             */
            return Redirect::to('safe/account_client/edit')->with('updated', true);


        }//if it validates


        /*
         * return with errors
         */
        return Redirect::to('safe/account_client/edit')->with_errors($client->errors());

    }//POST edit_account




    public function get_change_password()
    {

        /*
         * Get user id
         */
        $user_id = Session::get('user.id');


        /*
         * Find the user
         */
        $user = Backend_User::find($user_id);

        /*
         * View data
         */
        $data = array(
            'selected_page'    => 'none',
            'breadcrumbs'      => array(
                                            // 'safe/home'       => __('common.home_page'),
                                            'last'            => __('common.change_password')
                                        ),
             'user'            => $user,

        );

        return View::make('backend.account.password.change',$data);


    }//GET change


    public function post_change_password()
    {

        $user_id = Session::get('user.id');

        $user = Backend_User::find($user_id);



        /*
         * Check current password
         */
        $current_password = Security::encrypt( Input::get('current_password') );



        if( !Backend_User::is_valid_password($user_id, $current_password) )
        {

            return Redirect::to('safe/change_password')->with('current_password_fail', true);

        }//if the current password is not ok


        /*
         * Validate data
         */
        $inputs = array (
                            'new_password'     => Input::get('new_password'),
                            'confirm_password' => Input::get('confirm_password')
        );


        if( $user->validate($inputs, Backend_User::$change_password_rules ) )
        {

            $user->password = Security::encrypt($inputs['new_password']);

            //update
            $user->save();


            /*
             * Go to account change password
             */
            return Redirect::to('safe/change_password')->with('updated', true);


        }//if it validates


        /*
         * return with errors
         */
        return Redirect::to('safe/change_password')->with_errors($user->errors());

    }//POST change


}//end class