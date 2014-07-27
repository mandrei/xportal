<?php

class Backend_Users_Controller extends xPortal_Controller
{

    public function get_add()
    {


        /*
         * View data
         */
        $data = array(
            'selected_page'     => 'users',
            'breadcrumbs'       => array(
                                        // 'safe/home'       => __('common.home_page'),
                                        'safe/users'         => __('common.users'),
                                        'last'               => __('common.add')
                                    ),
            'languages'         => Backend_Language::all(),
            'modules'           => Backend_Module::get_modules()
                    );


        return View::make('backend.user.add', $data);

    }//GET add



    public function post_add()
    {
    
        /*
         * Create a new object
         */
        $user = new Backend_User();


        /*
         * Validate data
         */
        $inputs = array(
            'first_name'            => Input::get('first_name'),
            'last_name'             => Input::get('last_name'),
            'email'                 => Input::get('email', false),
            'username'              => Input::get('username'),
            'password'              => Input::get('password', false),
            'password_confirmation' => Input::get('password_confirmation'),
            'start_date'            => Input::get('start_date'),
            'user_type'             => Input::get('user_type'),
            'phone'                 => Input::get('phone'),
            'address'               => Input::get('address'),
            'language'              => 1
        );


        if( $user->validate($inputs,  Backend_User::$validation_rules  ) )
        {

            /*
             * If we don't have a password supplied, generate a random one
             */
            if($inputs['password'] == false &&  $inputs['email'] == false ){

                return Redirect::to('safe/user/add')->with('no_email_no_password', true)->with_input();

            } //if neighter password or email was set
            else if($inputs['password'] == false &&  $inputs['email'] != false)
            {
                $password = Str::random(6, 'alpha');

            }//if no password
            else
            {
                $password = $inputs['password'];

            }//if password

            if ( count(Input::get('permissions') ) == 0 ) {

                  return Redirect::to('safe/user/add')->with('no_permissions', true)->with_input();  

            }//if no permissions were selected

            $user_details = new Backend_User_Details();




            DB::transaction(function() use ($inputs,$user,$password,$user_details) {

                    /*
                     * Insert in the database
                     */
                    $user->email              = $inputs['email'];
                    $user->username           = $inputs['username'];
                    $user->password           = Security::encrypt( $password );
                    $user->language_id        = 1;
                    $user->user_type          = 1;

                    //insert
                    $user->save();


                    
                    $user_details->user_id      = $user->id;
                    $user_details->first_name   = $inputs['first_name'];
                    $user_details->last_name    = $inputs['last_name'];
                    $user_details->phone        = $inputs['phone'];
                    $user_details->address      = $inputs['address'];

                    $user_details->save();

                    /*
                    *
                    *  Add permissions
                    *
                    */
                    $permissions = Input::get('permissions') ;

                    foreach($permissions as $p) 
                    {
                        $permission = new Backend_User_Permission();

                        $permission->user_id    = $user->id;
                        $permission->module_id  = $p;

                        $permission->save();

                        /*
                        *
                        *  If the selected permission is a child, add parent too 
                        *
                        */
                        $parent = Backend_Module::where('id','=',$p)->take(1)->only('parent');

                        

                        if ( $parent != 0 && !in_array($parent, $permissions) ) {

                            //check if the parent was already added 
                            $parent_added = Backend_User_Permission::where('module_id','=',$parent)->where('user_id','=',$user->id)->count();

                            if($parent_added  == 0 ) {

                                $permission = new Backend_User_Permission();

                                $permission->user_id    = $user->id;
                                $permission->module_id  = $parent;

                                $permission->save();

                            }

                            

                        }//if has parent add it

                    }//endforeach


                    
                  
            });//end transactions


                    // create physical folder
                    $slash = Config::get('portal.back_slash');

                    if( !is_dir(Config::get('portal_parent_folder').$slash.'users')) {

                        $users_folder = new Portal('');

                        if($users_folder->status()  ) {
                             $users_folder->create('users');
                        }

                        
                    }//if the users folder doesn't exist



                    $folder_name = str_replace(' ', '-', $user_details->last_name).
                                    '-'.
                                    str_replace(' ', '-',$user_details->first_name).
                                    '-'.
                                    $user->id;

                    $user_folder = new Portal('users');

                    if( $user_folder->status()  ) {

                            $user_folder->create($folder_name);
                    }
                    


            /*
             * Check if an email was set, if true, we send
             * an email with the login credentials to the user.
             */
            if( $inputs['email'] != false )
            {


                /*
                 *
                 *  Send email to user
                 *
                 */

                $email_from  = Backend_Settings::r('system_email');
                $email_name  = Backend_Settings::r('system_name_email');

                $link = URL::base().'/login';

                /*
                 *
                 *  Get user's language
                 *
                 */
                $user_language  = Backend_Language::get_abbr_by_id( $user->language_id );


                $user_language = strtolower($user_language);


            
                //Plain
                $plain_message  = __('common.plain_message_new_account', array( 'name'       => $user_details->first_name . ' '.$user->last_name , 
                                                                                'email'      => $user->email,
                                                                                'username'   => $user->username,
                                                                                'password'   => $password,
                                                                                'link'       => $link ), 
                                                                            $user_language );

                //HTML
                $html_message   =  __('common.html_message_new_account', array('email'=> $user->email, 'username'=> $user->username, 'password'=> $password, 'link'=>$link ), $user_language);


                $data_email     = array(
                                            'title'   => __('common.html_message_new_account_title', array('name' => $user_details->first_name . ' ' . $user_details->last_name), $user_language ),
                                            'message' => $html_message
                                        );

                $html_message_body = render('backend.email.email', $data_email);    
               

                /*
                 *
                 *  Send email
                 *
                 */
                MailHelper::send($user->email, $email_from, $email_name, $plain_message, $html_message_body,$email_name, __('common.authentication_data'));


            }//if we have an email address



            /*
             * Go to all users
             */
            return Redirect::to('safe/users');



        }//if it validated

        /*
        /*
         * Else it didn't validate
         */
        return Redirect::to('safe/user/add')->with_errors($user->errors())->with_input();

    }//POST add



    public function get_edit($user_id)
    {


        /*
         * Find the user
         */
        $user = Backend_User::find_undeleted($user_id);

        

        /*
         * Check if it's a valid user
         */
        if( !$user ) return Response::error('404');


        /*
         * Find the user
         */
        $user = Backend_User::infos($user_id);

        
        $user = $user[0];


        /*
        *
        *  Get user's permissions
        *
        */
        $permissions_obj = Backend_User_Permission::where('user_id','=',$user_id)->get('module_id');

        $permissions = array();

        foreach($permissions_obj as $p){

            $permissions[] = $p->module_id;

        }//end foreach


        /*
         * View data
         */
        $data = array(
            'selected_page'     => 'users',
            'breadcrumbs'       => array(
                                            // 'safe/home'              => __('common.home_page'),
                                            'safe/users'             => __('common.users'),
                                            'last'                   => $user->first_name. ' '.$user->last_name
                                      ),
            'user'              => $user,
            'languages'         => Backend_Language::all(),
            'modules'           => Backend_Module::get_modules(),
            'permissions'       => $permissions
        );

        return View::make('backend.user.edit', $data);


    }//GET edit




    public function post_edit($user_id)
    {


        /*
         * Find the user
         */
        
        $user = Backend_User::find_undeleted($user_id);


        /*
         * Check if it's a valid user
         */
        if( !$user ) return Response::error('404');



        /*
         * Validate data
         */
        $inputs = array(

            'first_name'       => Input::get('first_name'),
            'last_name'        => Input::get('last_name'),
            'user_type'        => Input::get('user_type'),
            'username'         => Input::get('username'),
            'email'            => Input::get('email'),
            'address'          => Input::get('address'),
            'phone'            => Input::get('phone'),
            'language'         => 1

        );

       


        if( $user->validate($inputs, Backend_User::edit_validation($user_id)) )
        {


            if ( count(Input::get('permissions') ) == 0 ) {

                  return Redirect::to('safe/user/'.$user_id.'/edit')->with('no_permissions', true)->with_input();  

            }//if no permissions were selected


             /*
             * Get old first and last name for folder update
             *
             */
            $user = Backend_User::infos($user_id);
            $user = $user[0];

            $old_name = str_replace(' ', '-', $user->last_name).
                                    '-'.
                                    str_replace(' ', '-',$user->first_name).
                                    '-'.
                                    $user_id;

                                   

            // $old_name = $user_id.'_'.$user->first_name.'_'.$user->last_name;




             Backend_User::where('id','=',$user_id)->update(array(
                                                                                'username'      => $inputs['username'],
                                                                                'email'         => $inputs['email'],
                                                                                'language_id'   => 1,
                                                                              
                                                                                ));


            /*
            *
            *  Update details
            *
            */
            Backend_User_Details::where('user_id','=',$user_id)->update(array(
                                                                                'first_name'    => $inputs['first_name'],
                                                                                'last_name'     => $inputs['last_name'],
                                                                                'phone'         => $inputs['phone'],
                                                                                'address'       => $inputs['address']
                                                                                ));

            /*
            *
            *  Update permissions
            *
            */
            //delete previous permissions
            Backend_User_Permission::where('user_id','=',$user_id)->delete();



            //add permissions
            $permissions = Input::get('permissions') ;

            foreach($permissions as $p) 
            {
                $permission = new Backend_User_Permission();

                $permission->user_id    = $user_id;
                $permission->module_id  = $p;

                $permission->save();

                /*
                *
                *  If the selected permission is a child, add parent too if wasn't added before
                *
                */
                $parent = Backend_Module::where('id','=',$p)->take(1)->only('parent');


                if ( $parent != 0 && !in_array($parent, $permissions) ) {

                    $permission = new Backend_User_Permission();

                    $permission->user_id    = $user_id;
                    $permission->module_id  = $parent;

                    $permission->save();

                }//if has parent add it

            }//endforeach


            /*
            * Rename folder
            *
            */
            $new_name = str_replace(' ', '-', $inputs['last_name']).
                                    '-'.
                                    str_replace(' ', '-',$inputs['first_name']).
                                    '-'.
                                    $user_id;


                                    

            if($old_name != $new_name ) {

                $slash = Config::get('portal.back_slash');

                $folder = new Portal('users'.$slash.$old_name);

                if($folder->status() ) $folder->rename($slash.$new_name);


            }

            


            /*
             * Go to edit
             */
            return Redirect::to('safe/user/'.$user_id .'/edit')->with('updated',true);

        }//if it validates


        /*
         * Go back
         */
        return Redirect::to('safe/user/'.$user_id .'/edit')->with_errors($user->errors());



    }//POST edit



    public function get_all()
    {

        /*
         * Order, page
         */
        $order_by 			= Input::get('order_by',  'username' );

        $order_direction 	= Input::get('order_direction' , 'asc' );

        $page 				= Input::get('page' , 1);


        /*
         * Search query
         */
        $search = Input::get('search', '!');



        /*
         * Check order_by & order_direction
         */
        $order_by 			= Tools::check_order_by($order_by, array('email','username','first_name','last_name','phone'));

        $order_direction 	= Tools::check_order_direction($order_direction);



        /*
         * Get the users, paginated and check if there are eny users
         */
        $users = Backend_User::paginated($order_by, $order_direction, $search, 10);


        /*
         * Count users
         */
        $count = Backend_User::count_undeleted_users();


        /*
         * View data
         */
        $data = array(
            'selected_page' => 'users',
            'breadcrumbs'   => array(
                                        // 'safe/home'  => __('common.home_page'),
                                        'last'       => __('common.users'),

                                    ),
            'users'					=> $users->results,
            'total_users'			=> $users->total,
            'found_users'			=> count($users->results),
            'links'	   		 	 	=> $users->appends(array('order_by' => $order_by ,'order_direction' => $order_direction, 'search' => $search ))->links(),
            'page'			  		=> $page,
            'count'                 => $count,
            'order_by'				=> $order_by,
            'order_direction'		=> $order_direction,
            'search'                => $search,
            'table_head'			=> array(
                                                'first_name' 	=> 'first_name',
                                                'last_name'  	=> 'last_name',
                                                'email'	  		=> 'email',
                                                'username'		=> 'username',
                                                'phone'         => 'phone',
                                                'actions'	    => '!'
            )
        );

        unset($users);


        return View::make('backend.user.all', $data);


    }//GET All




    public function get_details($user_id)
    {


        /*
         * Get the user and check if it's a valid one
         */
        $user = Backend_User::infos($user_id);

        if( !$user ) return Response::json( array( 'error' => 1 ) );



        $user = $user[0]->to_array();


        $user['permissions']  = Backend_User_Permission::permission($user_id);

    


        return Response::json( $user );


    }//get_details




    public function get_delete($id)
    {

       

        $user = Backend_User::find_undeleted($id);


        /*
         * Check if it's a valid user
         */
        if( !$user ) return Response::error('404');


        //user info
        $user_info = Backend_User::infos($id);

        $user_info =  $user_info[0];


        /*
        *
        *  Delete users folders and files
        *
        */
        $slash = Config::get('portal.back_slash');

        $user_folder = 'users'.$slash.str_replace(' ', '-', $user_info->last_name).
                                    '-'.
                                    str_replace(' ', '-',$user_info->first_name).
                                    '-'.
                                    $id;

                                 

        $folder = new Portal($user_folder);

        if($folder->status() ) {

             $folder->delete_recursive();
    
        }

       
        

        /*
        *
        *  Update user
        *
        */
        $user->username = $user->username."[DELETED]";
        $user->email    = $user->email."[DELETED]";
        $user->deleted  = 1;

        $user->save();


        return Redirect::to('safe/users')->with('deleted',true);

    }//GET delete




}//end class