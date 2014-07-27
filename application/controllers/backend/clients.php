<?php

class Backend_Clients_Controller extends xPortal_Controller
{


    public function get_index()
    {


        /*
         * Order, page
         */
        $order_by           = Input::get('order_by',  'username' );

        $order_direction    = Input::get('order_direction' , 'asc' );

        $page               = Input::get('page' , 1);

        $per_page           = Input::get('per_page',10);


        /*
         * Search/filter query
         */
        $search      = Input::get('search', '!');

        $client_type = Input::get('client_type', '0');




        /*
         * Check order_by & order_direction
         */
        $order_by           = Tools::check_order_by($order_by, array('email','username','name','type'));

        $order_direction    = Tools::check_order_direction($order_direction);



        /*
         * Get the clients, paginated and check if there are any clients
         */
        $clients = Backend_Client::paginated($order_by, $order_direction, $search, $client_type, $per_page);


        /*
         * Count clients
         */
        $count = Backend_Client::count_undeleted_clients();

        /*
         * View data
         */
        $data = array(
            'selected_page' => 'clients',
            'breadcrumbs'   => array(
                                        // 'safe/home'    => __('common.home_page'),
                                        'last' => __('common.clients'),
            ),
            'clients'               => $clients->results,
            'total_clients'         => $clients->total,
            'found_clients'         => count($clients->results),
            'links'                 => $clients->appends(array(
                                                                    'order_by' => $order_by ,
                                                                    'order_direction' => $order_direction, 
                                                                    'search' => $search, 
                                                                    'client_type' => $client_type,
                                                                    'per_page' => $per_page
                                                                     ))->links(),
            'page'                  => $page,
            'count'                 => $count,
            'order_by'              => $order_by,
            'order_direction'       => $order_direction,
            'search'                => $search,
            'client_type'           => $client_type,
            'per_page'              => $per_page,
            'client_types'          => Backend_Client_Type::all(),
            'table_head'            => array(
                'id'            => 'id',
                'type'          => 'type',
                'name'          => 'name',
                'email'         => 'email',
                'username'      => 'username',
                'actions'       => '!'
            )
        );

        return View::make('backend.client.all',$data);


    }//GET index




    public function get_add()
    {


    //die
        /*
         * View data
         */
        $data = array(
            'selected_page'     => 'clients',
            'breadcrumbs'       => array(

                                                // 'safe/home'    => __('common.home_page'),
                                                'safe/clients' => __('common.clients'),
                                                'last'         => __('common.add')
                                            ),

            'countries'              => Backend_Country::get(),
            'client_types'           => Backend_Client_Type::get(),
            'marital_statuses'       => DB::table('marital_status')->get(),
            'states'                 => Backend_State::all(),
            'positions'              => Backend_Position::all(),
            'provinces'              => Backend_Province::all(),
            'titles'                 => Backend_Client_Individual_Title::all(),
            'size'                   => Backend_Client_Size::get(array(
                                                                    'id',
                                                                    'sizetype'
                                                                )), 
            'nr_individual_associated'  => count(Input::old('individual_associated')), 
            'nr_corporation_associated' => count(Input::old('corporation_associated')), 
        );

        
        return View::make('backend.client.add', $data);

    }//GET add



    public function post_add() {

        $client = new Backend_Client();

        $inputs = Input::all(); 

        array_shift($inputs);


       
       /*
        * For phone validation (000) 000-0000
        */
        //Fax
        $inputs['fax'] = $inputs['fax1']."".$inputs['fax2']."".$inputs['fax3'];

        //Phone
        $inputs['phone'] = $inputs['phone1']."".$inputs['phone2']."".$inputs['phone3'];
 

        if($inputs['birth_date'] == '' ) $inputs['birth_date'] = '0000-00-00 00:00:00';

        
 

        if( $client->validate($inputs, Backend_Client::validate_client(Input::get('client_type'),Input::get('country'))) )
        {



           /*
            *
            *  Get password
            *
            */
            if($inputs['password'] == '' &&  $inputs['email'] == '' ){

                return Redirect::to('safe/client/add')->with('no_email_no_password', true)->with_input();

            } //if neighter password or email was sets
            else if($inputs['password'] == '' &&  $inputs['email'] != '')
            {
                $password = Str::random(6, 'alpha');

            }//if no password
            else
            {
                $password = $inputs['password'];

            }//if password


            $client_details = new Backend_Client_Details();

            $individual  = new Backend_Client_Individual();

            $corporation = new Backend_Client_Corporation();


            /* Create clients folder if doesn't exist */
            
            $portal = new Portal('clients');

            if(!$portal->status()){

                $parent = new Portal('');

                $parent->create('clients');
            }

            DB::transaction(function() use ($inputs,$client,$password,$client_details, $individual, $corporation) {


                /*
                 *
                 *  Add user info
                 *
                 */
                $client->email              = strtolower($inputs['email']);
                $client->username           = $inputs['username'];
                $client->password           = Security::encrypt( $password );
                $client->language_id        = 1;//english
                $client->user_type          = 2;

                //insert
                $client->save();


                /*
                 *
                 *  Add client details(common for individual and corporation)
                 *
                 */
                $client_details->user_id            = $client->id;
                $client_details->street_address     = strtoupper($inputs['street_address']);
                $client_details->city               = strtoupper($inputs['city']);
                $client_details->country            = $inputs['country'];


                if($inputs['country'] == 1)
                {
                    $client_details->state           = $inputs['state'];
                    $client_details->zip_code        = $inputs['zip_code'];

                }//if its usa
                if($inputs['country'] == 2)
                {
                    $client_details->province        = $inputs['province'];
                    $client_details->postal_code     = $inputs['postal_code'];

                }//if its canada

                $client_details->client_size_type_id = $inputs['folder_size'];

                $client_details->phone               = $inputs['phone'];
                $client_details->fax                 = $inputs['fax'];
                $client_details->client_type_id      = $inputs['client_type'];

                $client_details->save();


                /*
                 *
                 *  Add individual
                 *
                 */
                if ($inputs['client_type'] == 1) {


                    $individual->client_id                  = $client_details->id;
                    $individual->calendar_year_start        = $inputs['calendar_year_start'];
                    $individual->first_name                 = strtoupper($inputs['first_name']);
                    $individual->initials                   = strtoupper($inputs['initials']);
                    $individual->title                      = $inputs['title'];
                    $individual->last_name                  = strtoupper($inputs['last_name']);
                    $individual->default_client_name        = strtoupper($inputs['default_client_name']);
                    $individual->birth_date                 = $inputs['birth_date'];
                    $individual->marital_status             = $inputs['marital_status'];

                    $individual->save();

                    $start_folder = $inputs['calendar_year_start'];

                    

                }// if individual
                else if ($inputs['client_type'] == 2 ){

                    /*
                     *
                     *  Add corporation
                     *
                     */
                    $corporation->client_id                             = $client_details->id;
                    $corporation->name                                  = strtoupper($inputs['name']);
                    $corporation->calendar_year_start                   = $inputs['corporation_calendar_year_start'];
                    $corporation->contact_person_last_name              = $inputs['contact_person_last_name'];
                    $corporation->contact_person_first_name             = $inputs['contact_person_first_name'];
                    $corporation->position                              = $inputs['position'];

                    $corporation->save();

                    $start_folder = $inputs['corporation_calendar_year_start'];

                     
                }//if corporation


                /*
                *
                *  Add permissions
                *
                */
                $permissions = array(
                    array(
                        'user_id'   => $client->id,
                        'module_id' => 2//2 is the id of Portal
                    ) ,
                    array(
                        'user_id'   => $client->id,
                        'module_id' => 5//5 - id of download/upload
                    )
                );

                Backend_User_Permission::insert($permissions);



                $slash = Config::get('portal.back_slash');

               /*
                *
                *  Create folders structure - database and fiels
                *
                */
               //INDIVIDUAL CLIENT
                if ($inputs['client_type'] == 1 ){


                    /*
                    *
                    *  First level - Client name
                    *
                    */
                    // $name = $individual->first_name;

                    // if ($individual->last_name != '') {

                    //     $name .= '_'.$individual->last_name;

                    // }//if has last name

                    // $name .= '_'.$client->id;  

                    $name = str_replace(' ', '-', $individual->last_name).'-'.str_replace(' ', '-', $individual->first_name).'-'.$client->id;


                    /*
                    *
                    *  Create folders - individuals
                    *
                    */

                    /* Create individual folder if doesn't exist */
                    
                    $client_folder = new Portal('clients'.$slash.'individual');

                    if(!$client_folder->status()){

                        $parent = new Portal('clients');

                        $parent->create('individual');

                    }
                    $client_folder = new Portal('clients'.$slash.'individual');

                     if($client_folder->is_writable() ) {

                         $client_folder->create_user($name, $start_folder, 'individual',false);

                     }//if is writable

           

                }//if individual
                else {

                   /*
                   *
                   * Client name by client type
                   *
                   */
                    $client_type_folder_name = strtolower(Backend_Client_Type::get_name_by_client_type_id($inputs['client_type']));


                    //client name
                    $name = str_replace(' ', '-',$corporation->name).'-'.$client->id;

                    /*
                    *
                    *  Create folders - Corporate
                    *
                    */
                     $client_folder = new Portal('clients'.$slash.$client_type_folder_name);

                    if(!$client_folder->status()){

                        $parent = new Portal('clients');

                        $parent->create($client_type_folder_name);

                    }//is the cliet type folder was not created - create it

                    $client_folder = new Portal('clients'.$slash.$client_type_folder_name);
                   
                     if($client_folder->is_writable() ) {

                         $client_folder->create_user($name, $start_folder, $client_type_folder_name, false);

                     }//if is writable

                 

                }//if corporation



            });//end transactions



           /*
            *
            *  Mail to client - with credentials
            *
            * Check if an email was set, if true, we send
            * an email with the login credentials to the client.
            */
            if( $inputs['email'] != '' )
            {


                /*
                 *
                 *  Send email to client
                 *
                 */

                $email_from  = Backend_Settings::r('system_email');
                $email_name  = Backend_Settings::r('system_name_email');

                $link = URL::base();

                /*
                 *
                 *  Get client's language
                 *
                 */
                $client_language  = Backend_Language::get_abbr_by_id( $client->language_id );

                $client_language = strtolower($client_language);



                if ($inputs['client_type'] == 1 ){

                $title = DB::table('client_individual_titles')->where('id','=',$inputs['title'])->only('title');

                $name = $title.' '.$inputs['last_name'];

            }else {
                $name = $inputs['name'];
            }


                // $name = Backend_User::get_account_name($client->id);

               

                //Plain
                $plain_message  = __('common.plain_message_new_account',
                                                                          array('name'       => $name,
                                                                                'email'      => $client->email,
                                                                                'username'   => $client->username,
                                                                                'password'   => $password,
                                                                                'link'       => $link ), 
                                                                          $client_language 
                                  );


                //HTML
                $html_message   =  __('common.html_message_new_account', array('email'=> $client->email,'username'=> $client->username,'password' => $password, 'link'=> $link ), $client_language);


                $data_email     = array(
                    'title'   => __('common.html_message_new_account_title', array('name' => $name) , $client_language),
                    'message' => $html_message
                );

                $html_message_body = render('backend.email.email', $data_email);



                /*
                 *
                 *  Send email
                 *
                 */
                MailHelper::send($client->email, $email_from, $email_name, $plain_message, $html_message_body,$email_name, __('common.authentication_data',array(), $client_language));


            }//if we have an email address



            /*
             * Go to all clients
             */
            return Redirect::to('safe/clients');


        }//if it validates

      
        /*
         * return with errors
         */


        return Redirect::to('safe/client/add')->with_input()->with_errors( $client->errors());



    }//POST add

    


    public function get_edit($client_id)
    {

        $client = Backend_Client::find_undeleted($client_id);

         /*
         * Check if it's a valid client
         */
        if( !$client ) return Response::error('404');


        /*
         * Find the client
         */
        $client = Backend_Client::infos($client_id);

        $client = $client[0];

     

        if ($client->client_type_id == 1 ) {

            $name = $client->first_name. ' '. $client->last_name;

        }//if individual
        else {

            $name = $client->name;

        }//if coorporation


        /*
         * View data
         */
        $data = array(
            'selected_page'     => 'clients',
            'breadcrumbs'       => array(
                // 'safe/home'              => __('common.home_page'),
                'safe/clients'           => __('common.clients'),
                'last'                   => $name
            ),
            'client'                 => $client,
            'countries'              => Backend_Country::all(),
            'marital_statuses'       => DB::table('marital_status')->get(),
            'individuals'            => Backend_Client_Details::get_client_individuals(),
            'corporations'           => Backend_Client_Details::get_client_corporations(),
            'states'                 => Backend_State::all(),
            'positions'              => Backend_Position::all(),
            'client_types'           => Backend_Client_Type::get(),
            'provinces'              => Backend_Province::all(),
            'titles'                 => Backend_Client_Individual_Title::all(),
            'size'                   => Backend_Client_Size::get(array(
                                                                        'id',
                                                                        'sizetype'

                                                                    )),

            'nr_individual_associated'  => count(Input::old('individual_associated')), 
            'nr_corporation_associated' => count(Input::old('corporation_associated')), 
        );



        return View::make('backend.client.edit', $data);

    }//GET edit



    public function post_edit($client_id) {

        /*
        * Find the client
        */
        $client = Backend_Client::find_undeleted($client_id);


        /*
         * Check if it's a valid client
         */
        if( !$client ) return Response::error('404');


        /*
        *
        *  Get inputs
        *
        */
        $inputs = Input::all();

  
        /*
         * For phone validation (000) 000-0000
         */

        //Fax
        $inputs['fax']   = $inputs['fax1']."".$inputs['fax2']."".$inputs['fax3'];

        //Phone
        $inputs['phone'] = $inputs['phone1']."".$inputs['phone2']."".$inputs['phone3'];


       

        if($inputs['zip_code'] == 0 ) $inputs['zip_code'] = '';

       
        if( $client->validate($inputs, Backend_Client::edit_client($inputs['client_type'], $client_id,$inputs['country'])) )
        {


              
                /*
                *
                *  Update user info
                *
                */
                $client->email              = strtolower($inputs['email']);
                $client->username           = $inputs['username'];
                $client->language_id        = 1;

                //insert
                $client->save();



                 //old name for folders   
                $old_name = Backend_Client::folder_name($client_id);

              
                        
                $slash = Config::get('portal.back_slash');
               
                $type_name  = strtolower(Backend_Client_Type::get_name_by_client_type_id($inputs['client_type']));





                /*
                *
                *  Update client details(common for individual and corporation)
                *
                */
                $client_details_id = Backend_Client_Details::where('user_id','=',$client->id)->take(1)->only('id');


                $client_details = Backend_Client_Details::find($client_details_id);

                $old_client_type = $client_details->client_type_id;


                $client_details->user_id             = $client->id;
                $client_details->street_address      = strtoupper($inputs['street_address']);
                $client_details->city                = strtoupper($inputs['city']);
                $client_details->client_size_type_id = $inputs['folder_size'];
                $client_details->client_type_id      = $inputs['client_type'];

                if($inputs['country'] == 1)
                {
                    $client_details->state         = $inputs['state'];
                    $client_details->zip_code      = $inputs['zip_code'];

                }//if its usa
                if($inputs['country'] == 2)
                {
                    $client_details->province        = $inputs['province'];
                    $client_details->postal_code     = $inputs['postal_code'];

                }//if its canada
                $client_details->country            = $inputs['country'];
                $client_details->phone              = $inputs['phone'];
                $client_details->fax                = $inputs['fax'];
                $client_details->client_type_id     = $inputs['client_type'];

                $client_details->save();



                /*
                 *
                 *  Update individual
                 *
                 */
                if ($inputs['client_type'] == 1) {


                    if($old_client_type == 1) {

                            $individual_id = Backend_Client_Individual::where('client_id','=',$client_details_id)->take(1)->only('id');

                            $individual    = Backend_Client_Individual::find($individual_id);

                            /*
                            *
                            *  Update client's folder name in folders
                            *
                            */
                            if ( $individual->first_name != strtoupper($inputs['first_name']) || $individual->last_name != strtoupper($inputs['last_name'])  ) {
                               
                                $new_name = str_replace(' ','-',strtoupper($inputs['last_name'])).'-'.str_replace(' ','-',strtoupper($inputs['first_name'])).'-'.$client_id;


                                //rename
                                $portal = new Portal($old_name);

                              
                           
                                if( $portal->status() )
                                {
                                    $portal->rename($new_name);
                                    
                                }

                               

                                $name = $new_name;

                            }//if first name or last name changed
                            else{

                                $array = explode($slash,$old_name);

                                $name = end($array);
                            }

                            

                            if($individual->calendar_year_start != $inputs['calendar_year_start']) {
                                $start_folder = $inputs['calendar_year_start'];
                                $old_start_folder = false;
                            }


                    }//if the client type was not changed
                    else {


                            $corporate_id = Backend_Client_Corporation::where('client_id','=',$client_details_id)->take(1)->only('id');

                            $corporate    = Backend_Client_Corporation::find($corporate_id);

                            // $old_type_name  = strtolower(Backend_Client_Type::get_name_by_client_type_id($old_client_type));

                            /*
                            *
                            *  Update client's folder name in folders
                            *
                            */
                            $new_name = 'clients'. $slash. $type_name.$slash.strtoupper($inputs['first_name']).'_'.strtoupper($inputs['last_name']).'_'.$client_id;


                            //rename
                            $portal = new Portal($old_name);


                            if( $portal->status() )
                            {
                                // $portal->rename($new_name);

                                rename($portal->parent_folder_path.$slash.$old_name,$portal->parent_folder_path.$slash.$new_name );
                            }

                            //$name = $client_id.'_'.strtoupper($inputs['first_name']).'_'.strtoupper($inputs['last_name']);
                            $name = str_replace(' ','-',strtoupper($inputs['last_name'])).'-'.str_replace(' ','-',strtoupper($inputs['first_name'])).'-'.$client_id;


                            $start_folder = $inputs['calendar_year_start'];
                            
                            $old_start_folder = false;




                            //delete corporation
                            Backend_Client_Corporation::delete_by_corporation_id($corporate_id);

                            //create new individual
                            $individual = new Backend_Client_Individual();





                    }//if the client type was changed 


                    $individual->client_id                  = $client_details->id;
                    $individual->calendar_year_start        = $inputs['calendar_year_start'];
                    $individual->title                      = $inputs['title'];
                    $individual->first_name                 = strtoupper($inputs['first_name']);
                    $individual->initials                   = strtoupper($inputs['initials']);
                    $individual->last_name                  = strtoupper($inputs['last_name']);
                    $individual->default_client_name        = strtoupper($inputs['default_client_name']);

                    if($inputs['birth_date'] != '')
                    {
                        $individual->birth_date             = $inputs['birth_date'];

                    }

                    $individual->marital_status             = $inputs['marital_status'];

                    $individual->save();


                   
                }// if individual
                else if ($inputs['client_type'] == 2 ){


                    if( ($old_client_type == $inputs['client_type']) ) {

                                /*
                                *
                                *  Update corporation
                                *
                                */
                                $corporation_id = Backend_Client_Corporation::where('client_id','=',$client_details_id)->take(1)->only('id');

                                $corporation    = Backend_Client_Corporation::find($corporation_id);

                      
                                /*
                                 *
                                 *  Change folders names
                                 *
                                 */
                                if ( $corporation->name  != strtoupper($inputs['name']) ) {

                                    $new_name = str_replace(' ','-',strtoupper($inputs['name']).'-'.$client_id);


                                    $folder = new Portal($old_name);

                                    if( $folder->status() )
                                    {
                                        $folder->rename($new_name);

                                    }

                                    $name = $new_name;


                                }//if first name or last name changed

                                else {

                                    $array = explode($slash,$old_name);

                                    $name = end($array);
                                }



                                if( $corporation->calendar_year_start !=  $inputs['corporation_calendar_year_start'] ) {

                                    $start_folder = $inputs['corporation_calendar_year_start'];

                                    $old_start_folder = $corporation->calendar_year_start;

                                }



                    }//if corporate
                    else if( ($old_client_type != $inputs['client_type'])  && ($old_client_type == 2) ){


                            /*
                            *
                            *  Update corporation
                            *
                            */
                            $corporation_id = Backend_Client_Corporation::where('client_id','=',$client_details_id)->take(1)->only('id');

                            $corporation    = Backend_Client_Corporation::find($corporation_id);

                  
                            /*
                             *
                             *  Change folders names
                             *
                             */
                                $new_name = 'clients'. $slash. $type_name . $slash. str_replace(' ','-',strtoupper($inputs['name'])).'-'.$client_id;


                                $folder = new Portal($old_name);

                                if( $folder->status() )
                                {
                                    // $folder->rename($new_name);

                                     rename($folder->parent_folder_path.$slash.$old_name,$folder->parent_folder_path.$slash.$new_name );

                                }

                                $name = str_replace(' ','-',strtoupper($inputs['name'])).'-'.$client_id;

     
                              
                            if( $corporation->calendar_year_start !=  $inputs['corporation_calendar_year_start'] ) {

                                $start_folder = $inputs['corporation_calendar_year_start'];

                                $old_start_folder = $corporation->calendar_year_start;

                            }

                    }
                    else {

                            $individual_id = Backend_Client_Individual::where('client_id','=',$client_details_id)->take(1)->only('id');

                            $individual    = Backend_Client_Individual::find($individual_id);


                            /*
                            *
                            *  Update client's folder name in folders
                            *
                            */
                            $new_name = 'clients'. $slash. $type_name . $slash. str_replace(' ','-',strtoupper($inputs['name'])).'-'.$client_id;



                            //rename
                            $portal = new Portal($old_name);


                            if( $portal->status() )
                            {
                                // $portal->rename($new_name);

                                rename($portal->parent_folder_path.$slash.$old_name,$portal->parent_folder_path.$slash.$new_name );
                            }

                            $name = str_replace(' ','-',strtoupper($inputs['name'])).'-'.$client_id;



                            $start_folder = $inputs['corporation_calendar_year_start'];
                            
                            $old_start_folder = false;



                            //delete individual
                            Backend_Client_Individual::delete_by_individual_id($individual_id);

                            //create new corporation
                            $corporation = new Backend_Client_Corporation();



                    }// old  type == individual



                    $corporation->client_id                         = $client_details->id;
                    $corporation->name                              = strtoupper($inputs['name']);
                    $corporation->calendar_year_start               = $inputs['corporation_calendar_year_start'];

                    $corporation->contact_person_last_name          = $inputs['contact_person_last_name'];
                    $corporation->contact_person_first_name         = $inputs['contact_person_first_name'];
                    $corporation->position                          = $inputs['position'];
                    $corporation->save();


                }//if corporation

             



                /*
                *  Change subfolders
                *
                */
                if (isset($start_folder)) {


                    $type_folder = new Portal('clients'.$slash.$type_name);

                    $type_folder->create_user($name, $start_folder, $type_name, $old_start_folder);
                }



            /*
             * Go to edit
             */
            return Redirect::to('safe/client/'.$client_id .'/edit')->with('updated',true);

        }//if it validates


        /*
         * Go back
         */
        return Redirect::to('safe/client/'.$client_id .'/edit')->with_errors($client->errors())->with_input();


    }//POST edit







    public function get_details($client_id)
    {

        /*
         * Find the client
         */
        $client = Backend_Client::infos($client_id);

        if( !$client ) return Response::json( array( 'error' => 1 ) );


        $client = $client[0]->to_array();


        $client['position'] = DB::table('positions')->where('id','=',$client['position'])->take(1)->only('name');

        $client['marital_status'] = DB::table('marital_status')->where('id','=',$client['marital_status'])->take(1)->only('name');

        $client['country_name']  = Backend_Country::get_name_by_id($client['country']);

        $client['state']    = Backend_State::get_state_name_by_id($client['state']);

        $client['province'] = Backend_Province::get_province_name_by_id($client['province']);

        $client['title']  = Backend_Client_Individual_Title::get_title_name($client['title']);

        
        return Response::json( $client );


    }//get_details



    public function get_delete($id)
    {



        $user = Backend_User::find_undeleted($id);


        /*
         * Check if it's a valid user
         */
        if( !$user ) return Response::error('404');



         /*
        *
        *  Delete clients folders and files
        *
        */

        $slash = Config::get('portal.back_slash');
        

        $client_folder = Backend_Client::folder_name($id);

        $folder = new Portal($client_folder);

        if( $folder->status() )
        {

            $folder->delete_recursive();

        }//status



        /*
        *
        *  Update user
        *
        */
        $user->username = $user->username."[DELETED]";
        $user->email    = $user->email."[DELETED]";
        $user->deleted  = 1;

        $user->save();


        return Redirect::to('safe/clients');



    }//GET delete

    public function get_unviewed($userId){

        $user = Backend_Client_Details::where('user_id','=',$userId)->update(array('update_client'=>0));

        return Response::json( $user );

    }




}//end class