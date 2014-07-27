<?php

class Backend_User extends Base_Model
{

    public static $table = 'users';



    /*
     *
     * Validation rules
     *
     */
    public static $validation_rules = array(

        'user_type'                   => 'not_in:0',
        'first_name'		          => 'required|min:2|max:100',
        'last_name'			          => 'required|min:2|max:100',
        'email'				          => 'required|min:5|max:120|email|unique:users',
        'username'                    => 'required|min:1|max:50|unique:users',
        'password'                    => 'min:6|max:40|same:password_confirmation',
        'phone'                       => 'min:10|max:20|match:/^([0-9\(\)\/\+ \-]*)$/',
        'address'                     => 'max:250',
        

    );



    public static function find_undeleted($user_id){

        $user = self::find($user_id);

        if ($user && $user->deleted == 0 ) {

            return $user;
        }
        else {

            return false;
        }

    }


    /*
     *
     * Validation rules for changing password
     *
     */
    public static $change_password_rules = array(

        'new_password'              => 'required|min:6|max:40',
        'confirm_password'          => 'required|min:6|max:40|same:new_password'

    );


    /*
     * Check current password
     */
    public static function is_valid_password($user_id, $password)
    {

        $count = self::where('id', '=', $user_id)
                     ->where('password', '=', $password)
                     ->count();

        if( $count == 1 )
        {

            return true;

        }//if count == 1


        //default return false
        return false;

    }//is_valid_password


    public static function edit_validation($id)
    {

        $v = self::$validation_rules;

        $v['email']    .= ',email,'.$id;
        $v['username'] .= ',username,'.$id;


        return $v;

    }//edit validation


    /*
     * Used on login
     */
    public static function check_credentials($email_or_username, $password)
    {


        $password = Security::encrypt($password);

        $email_or_username = e($email_or_username);


        $sql = "SELECT
                        id, user_type
                FROM
                        users
                WHERE
                        password = '{$password}'
                AND(
                        email = '{$email_or_username}'
                OR
                        username = '{$email_or_username}')
                AND
                        deleted = 0
                LIMIT
                        1";


        return DB::query($sql);


    }////check_credentials


    public static function paginated($order_by, $order_direction, $search, $per_page = 10)
    {


        $query = DB::table('users as u')
                   ->join('users_details as ud','u.id','=','ud.user_id')
                   ->where('u.deleted','=', 0)
                   ->where('u.user_type','!=',0)
                   ->where('u.id','!=', Session::get('user.id'));


        if( $search != '!' )
        {

            $query->where( function($query) use ($search)
            {

                $query->where('ud.first_name', 'LIKE', "%{$search}%");
                $query->or_where('ud.last_name', 'LIKE', "%{$search}%");
                $query->where('u.email', 'LIKE', "%{$search}%");
                $query->or_where('u.username', 'LIKE', "%{$search}%");

            });//


        }//if we need to search



        return $query->order_by( $order_by, $order_direction )
                     ->paginate($per_page, array(
                                                    'u.id',
                                                    'ud.first_name',
                                                    'ud.last_name',
                                                    'u.email',
                                                    'u.username',
                                                    'ud.phone',
                                                    'u.user_type'));

    }//paginated



    public static function count_undeleted_users()
    {

        return self::where('deleted','=',0)
                   ->where('user_type','=',1)
                   ->where('id','!=',Session::get('user.id'))
                   ->count();


    }//get_undeleted_users



    public static function get_id_by_email($email) {

        return self::where('email','=',$email)
                   ->get(array('id','email','language_id','user_type'));


    }//get_id_by_email


    public static function get_regular_users()
    {

        return self::where('user_type','=',2)
                   ->where('deleted','=',0)
                   ->get(array(

                                'id',
                                // DB::raw('CONCAT(first_name," ",last_name) as user_name')

                           ));

    }//get_regular_users


    public static function infos($user_id) {

        return self::left_join('users_details','users.id','=','users_details.user_id')
                    ->where('users.id','=',$user_id)
                    ->where('users.deleted','=',0)
                    ->take(1)
                    ->get();

    }//infos


    public static function get_details_by_user_id($user_id)
    {

        return self::join('users_details as ud','ud.user_id','=','users.id')
                   ->where('ud.user_id','=',$user_id)
                   ->take(1)
                   ->get(array(

                                'users.email',
                                'users.username',
                                'users.language_id',
                                'ud.user_id',
                                'ud.first_name',
                                'ud.last_name',
                                'ud.phone',
                                'ud.address'
            ));

    }//get_details_by_user_id






    public static function get_details($user_id) {

       
        $user =  self::find($user_id);



        if ( $user->user_type == 1 || $user->user_type == 0 ) {

            return self::infos($user_id);

        }//if admin or superadmin
        else{

            return Backend_Client::infos($user_id);

        }//if client

    }//get_details




    public static function get_account_name($user_id)
    {
        //Initialize name array
        $name = '';

        //Used for user_type and user id
        $data = DB::table('users')
                  ->where('id','=',$user_id)
                  ->get();

        $data = $data[0];

        //Users,Admin,Superadmin
        if($data->user_type == 1 || $data->user_type == 0 )
        {
            $name = Backend_User_Details::get_full_name_by_id($data->id);

        }//if its user get users full name


        //get clients type id for individual and company
        $client_details_id =  Backend_Client_Details::get_client_details_id_by_client_id($data->id);


        //Client Individual
        if($data->user_type == 2 && Backend_Client_Details::get_client_type_by_client_id($data->id) == 1)
        {
            $name = Backend_Client_Individual::get_full_name_by_client_details_id($client_details_id);

        }//if its client individual get client individual full name


        //Client Corporation
        if($data->user_type == 2 && Backend_Client_Details::get_client_type_by_client_id($data->id) != 1)
        {
            $name = Backend_Client_Corporation::get_full_name_by_client_details_id($client_details_id);

        }//if its client corporation get corporation name

      

        return $name;

    }//get_account_name




    /**
     * Returns the path to a user's or client's personal folder
     *
     * @param $user_id - the id of the user(table users.id)
     * @return string - the path
     */
    public static function personal_folder_path($user_id) {


        $user = Backend_User::find($user_id);

        $slash = Config::get('portal.back_slash');


        if($user) {

            $route = '';

            if ($user->user_type == 0 || $user->user_type == 1) {

                //get first and last name 
                $details = Backend_User_Details::where('user_id','=',$user->id)->get(array('first_name','last_name'));



                $details = $details[0];

                //form route
                $route .= 'users'. $slash .$details->last_name.'-'.$details->first_name.'-'.$user->id;

            }//if user or admin 
            else {

                //get client's details
                $client_details = Backend_Client::infos($user->id);

                $client_details = $client_details[0];


                if($client_details->client_type_id == 1 ) {

                    $route .= 'clients'. $slash.'individual'.$slash.str_replace(' ', '-', $client_details->last_name).'-'.str_replace(' ', '-',$client_details->first_name).'-'.$user->id;
                    
                }//if individual
                else{

                    $folder = Backend_Client_Type::get_name_by_client_type_id($client_details->client_type_id);

                    $route .= 'clients'. $slash.strtolower($folder). $slash .str_replace(' ', '-',$client_details->name).'-'.$user->id;

                }//if corporate


            }//if client

                return $route;

        }//if the user exsists

        return false;

    }//personal_folder_path




    /**
     * Check if the current/logged user has access to the folder specified in route
     *
     * @param $route - string with the route to check
     * @return boolean
     */
    public static function has_folder_access($route){


        $user_type = Session::get('user.type');

        $user_id = Session::get('user.id');

        $personal_folder_route = self::personal_folder_path($user_id);



        //if admin has access to all folders
        if( $user_type == 0  ) {

            return true;

        }//if admin

        //if user has access to his folder and client's folders
        else if($user_type == 1){

            if( substr_count($route, $personal_folder_route ) > 0 || substr_count($route, 'clients') > 0 ) {

                return true;

            }//if the route is his personald folder or contains te clients name

        }//if user
        else {

            if(substr_count($route, $personal_folder_route ) > 0) {

                return true;

            }//if is his folderor folders inside his folder


        }//if client
        

        return false;

    }//has_folder_access




    /**
     * Returns user's id based on a folder's route
     *
     * @param $route - string with the route
     * @return int
     */
    public static function get_user_id_by_folder_route($route){

        $slash = Config::get('portal.back_slash');

        $route_array = explode($slash,$route);



        if($route_array[0] == 'users' && count($route_array) > 1 ) {

             $user_folder = $route_array[1];

             $user_folder_array = explode('-',$user_folder);

             $user_id = end($user_folder_array);


             if(is_numeric($user_id))  return $user_id;
           
         
        }//if we are in one of user's folders
        else if(count($route_array) > 2 ){



             $user_folder = $route_array[2];

              $user_folder_array = explode('-',$user_folder);

             $user_id = end($user_folder_array);


        
             if(is_numeric($user_id))  return $user_id;

        }//if we are in a client's folder

        
        return false;
       

    }//get_user_id_by_folder_route






    public static  function get_account_route($user_id, $folder_route){

         $personal_folder_path = Backend_User::personal_folder_path(Session::get('user.id'));

         $user = Backend_User::find($user_id);

         if($user) {

              if($personal_folder_path == $folder_route)  {

                if($user->user_type == 1 || $user->user_type == 0  ) {

                    return URL::to('safe/account_user/edit');

                }//is user 
                else {

                    return URL::to('safe/account_client/edit');


                }//if client

             }//if personal folder go to edit account
             else {

                  if($user->user_type == 1 || $user->user_type == 0  ) {

                        return URL::to('safe/user/'.$user_id.'/edit');

                    }//is user 
                    else {

                        return URL::to('safe/client/'.$user_id.'/edit');


                    }//if client


             }//go to edit

         }

         return false;

    }//get_account_route




    /*
    * Trying to improve the model 
    *
    */
    public static function create($array) {

    
        return DB::table(self::$table)
                   ->insert_get_id(array(
                                        'email'         => $array['email'],
                                        'username'      => $array['username'],
                                        'password'      => $array['password'],
                                        'user_type'     => $array['user_type'],
                                        'language_id'   => $array['language_id'],
                                        'deleted'       => $array['deleted']
                                        ));      



    }//create


    /*
    *  Method used to get user's name for emails 
    * 
    * for Users: Last_name First_name
    * for individuals : Title Last_Name
    * for the other clients: Name
    */
    public static function get_name_for_emails($user_id)
    {
        //Initialize name array
        $name = '';

        //Used for user_type and user id
        $data = DB::table('users')
                  ->where('id','=',$user_id)
                  ->get();

        $data = $data[0];

        //Users,Admin,Superadmin
        if($data->user_type == 1 || $data->user_type == 0 )
        {
            $name = Backend_User_Details::get_name_for_emails($data->id);

        }//if its user get users full name


        //get clients type id for individual and company
        $client_details_id =  Backend_Client_Details::get_client_details_id_by_client_id($data->id);


        //Client Individual
        if($data->user_type == 2 && Backend_Client_Details::get_client_type_by_client_id($data->id) == 1)
        {
            $name = Backend_Client_Individual::get_name_for_email_by_client_details_id($client_details_id);

        }//if its client individual get client individual full name


        //Client Corporation
        if($data->user_type == 2 && Backend_Client_Details::get_client_type_by_client_id($data->id) != 1)
        {
            $name = Backend_Client_Corporation::get_full_name_by_client_details_id($client_details_id);

        }//if its client corporation get corporation name

      

        return $name;

    }//get_account_name




}//end class

