<?php

class Backend_Client extends Base_Model
{

    public static $table = 'users';


    /*
     *
     * Validation rules
     *
     */
    public static $validation_rules = array(
        //common - users table
        'client_type'                 => 'required|not_in:0|exists:clients_types,id',
        

        //client-details table
        
        'street_address'              => 'max:1024',
        'city'                        => 'max:256',
        'country'                     => 'not_in:0',

        'phone'                       => 'min:10|max:10|match:/^([0-9\(\)\/\+ \-]*)$/',
        'fax'                         => 'min:10|max:20|match:/^([0-9\(\)\/\+ \-]*)$/',

        'email'				          => 'required|min:5|max:150|email|unique:users,email',

        'username'                    => 'required|min:1|max:50|unique:users,username',
        'password'                    => 'min:6|max:40|same:password_confirmation',
        
        
    );//validation rules

    



    public static function find_undeleted($client_id){

        $client = self::find($client_id);

        if ($client && $client->deleted == 0 ) {

            return $client;
        }
        else {

            return false;
        }

    }

    public static function validate_client($client_type,$country) {



        $rules = self::$validation_rules;

        if($country == 1)
        {
            $rules['state']    = 'not_in:0';
            $rules['zip_code'] = 'numeric';

        }//if its usa
        elseif($country == 2)
        {
            $rules['province']    = 'not_in:0';
            $rules['postal_code'] = 'match:/[a-z][0-9][a-z][- ]?[0-9][a-z][0-9]$/i';

        }//if its canada


        if ($client_type != 0) {

                 if ($client_type == 1) {

                    $rules['calendar_year_start']       = 'required|not_in:0';
                    $rules['title']                     = 'required|not_in:0';
                    $rules['first_name']                = 'required|min:2|max:50';
                    $rules['initials']                  = 'min:1|max:3';
                    $rules['last_name']                 = 'required|max:50';
                    $rules['default_client_name']       = 'required|max:100';
                    $rules['marital_status']            = 'exists:marital_status,id';

                }//if individual
                else{ 
                    $rules['name']                               = 'required|min:2|max:150';
                    $rules['corporation_calendar_year_start']    = 'required|after:0000-00-00';
                    $rules['contact_person_last_name']           = 'max:150';
                    $rules['contact_person_first_name']          = 'max:150';

                }//if corporation

        }//if the client type was selected



        return $rules;


    }//validate _ client



     public static function edit_client($client_type, $client_id,$country) {



        $client_details_id = Backend_Client_Details::where('user_id','=',$client_id)->take(1)->only('id');

        $rules = self::$validation_rules;

        $rules['email']         .= ','.$client_id;
        $rules['username']      .= ','.$client_id;




        if($country == 1)
        {
             $rules['state']     =  'not_in:0';
             $rules['zip_code']  = 'numeric';
          

         }//if its usa
         elseif($country == 2)
         {
             $rules['province']     = 'not_in:0';
             $rules['postal_code']  = 'match:/[a-z][0-9][a-z][- ]?[0-9][a-z][0-9]$/i';
            

         }//if its canada


        if ($client_type != 0) {

                 if ($client_type == 1) {
                    $rules['calendar_year_start']       = 'required|not_in:0';
                    $rules['title']                     = 'required|not_in:0';
                    $rules['first_name']                = 'required|min:2|max:50';
                    $rules['initials']                  = 'min:1|max:3';
                    $rules['last_name']                 = 'required|max:50';
                    $rules['default_client_name']       = 'required|max:100';
                    $rules['marital_status']            = 'exists:marital_status,id';

                }//if individual
                else{ 

          
                    $rules['name']                               = 'required|min:2|max:150';
                    $rules['corporation_calendar_year_start']    = 'required|after:0000-00-00';
                    $rules['contact_person']                     = 'max:150';

                }//if corporation

        }

   
        return $rules;


    }//validate _ client




    public static function paginated($order_by, $order_direction, $search, $client_type, $per_page = 10) {

         $query = DB::table('users as u')
                   ->join('clients_details as cd','u.id','=','cd.user_id')
                   ->join('clients_types as t','cd.client_type_id','=','t.id')
                   ->left_join('clients_individuals as i','cd.id','=','i.client_id')
                   ->left_join('clients_corporations as c','cd.id','=','c.client_id')
                   ->where('u.deleted','=', 0)
                   ->where('u.id','!=', Session::get('user.id'));


        if( $search != '!' )
        {

            $query->where( function($query) use ($search)
            {

                $query->where('i.first_name', 'LIKE', "%{$search}%");
                $query->or_where('i.last_name', 'LIKE', "%{$search}%");


                $query->or_where('c.name', 'LIKE', "%{$search}%");
                
                $query->or_where('c.contact_person_last_name', 'LIKE', "%{$search}%");
                $query->or_where('c.contact_person_first_name', 'LIKE', "%{$search}%");
                $query->or_where('c.position', 'LIKE', "%{$search}%");
                $query->or_where('cd.phone', 'LIKE', "%{$search}%");

                $query->or_where('t.name', 'LIKE', "%{$search}%");

                $query->or_where('u.email', 'LIKE', "%{$search}%");
                $query->or_where('u.username', 'LIKE', "%{$search}%");

            });//


        }//if we need to search


        if( $client_type != 0 ){

            $query->where('cd.client_type_id','=',$client_type);

        }


        return $query->order_by( $order_by, $order_direction )
                     ->paginate($per_page, array(
                                                    'u.id',
                                                    'u.email',
                                                    'u.username',
                                                    
                                                    't.name as type',
                                                    't.id as type_id',
                                                    'cd.update_client as update_client',

                                                    'cd.new_files',
                                                    DB::raw( "IFNULL(CONCAT(i.first_name,' ',i.last_name) , c.name ) as name"),


                                                    ));

    }//paginated


    public static function get_clients_to_export($selected_clients, $client_type) {

        $query = DB::table('users as u')
                   ->join('clients_details as cd','u.id','=','cd.user_id')
                   ->join('clients_types as t','cd.client_type_id','=','t.id')
                   ->join('countries','cd.country','=','countries.id')
                   ->join('client_size_type as cst','cd.client_size_type_id','=','cst.id')
                   ->left_join('provinces','cd.province','=','provinces.id')
                   ->left_join('states','cd.state','=','states.id')
                   ->left_join('clients_individuals as i','cd.id','=','i.client_id')
                   ->left_join('clients_corporations as c','cd.id','=','c.client_id')
                   ->left_join('client_individual_titles as ct','i.title','=','ct.id')
                   ->left_join('positions as pos','c.position','=','pos.id')
                   ->left_join('marital_status as ms','i.marital_status','=','ms.id')
                   ->where('u.deleted','=', 0);


        if( $client_type != 0 ){

            $query->where('cd.client_type_id','=',$client_type);

        }
   
     
        if( count($selected_clients) >= 1  &&  $selected_clients[0] != '') {

            $query->where_in('u.id',$selected_clients);
        }


        return $query->get(array(
                                    't.name as client_type',
                                    't.id as client_type_id',

                                    'i.calendar_year_start', 
                                    'ct.title as title', 
                                    'i.first_name',
                                    'i.initials',
                                    'i.last_name',
                                    'i.default_client_name',
                                    'i.birth_date', 
                                    'ms.name as marital_status', 

                                    'c.name as corporation_name',
                                    'c.calendar_year_start as corporation_calendar_year_start', 
                                    'c.contact_person_first_name',
                                    'c.contact_person_last_name',
                                    'pos.name as contact_person_position',
                                    

                                    'cd.street_address',
                                    'cd.city',

                                    'countries.name as country',
                                    'provinces.name as province',
                                    'states.name as state',

                                    'cd.phone', 
                                    'cd.fax',

                                    'u.email',
                                    'u.username',
                                    'cst.sizetype'
            ));


    }//get_clients_to_export



     public static function count_undeleted_clients()
    {

        return self::where('deleted','=',0)
                    ->where('user_type','=',2)
                    ->where('id','!=',Session::get('user.id'))
                   ->count();


    }//count_undeleted_clients


    public static function infos($client_id) {

        return  self::join('clients_details','users.id','=','clients_details.user_id')
                    ->left_join('clients_individuals','clients_details.id','=','clients_individuals.client_id')
                    ->left_join('clients_corporations','clients_details.id','=','clients_corporations.client_id')
                    ->where('users.id','=',$client_id)
                    ->where('users.deleted','=',0)
                    ->take(1)
                    ->get(array(
                                'users.id as user_id',
                                'users.email',
                                'users.username',
                                'users.user_type',
                                'clients_details.street_address',
                                'clients_details.city',
                                'clients_details.province',
                                'clients_details.postal_code',
                                'clients_details.state',
                                'clients_details.zip_code',
                                'clients_details.country',
                                'clients_details.phone',
                                'clients_details.fax',
                                'clients_details.client_type_id',
                                'clients_details.client_size_type_id',
                                'clients_individuals.id',
                                'clients_individuals.calendar_year_start',
                                'clients_individuals.title',
                                'clients_individuals.first_name',
                                'clients_individuals.initials',
                                'clients_individuals.last_name',
                                'clients_individuals.default_client_name',
                                'clients_individuals.birth_date',
                                'clients_individuals.marital_status',
                                'clients_corporations.id',
                                'clients_corporations.name',
                                'clients_corporations.calendar_year_start as corporation_calendar_year_start',
                                'clients_corporations.contact_person_last_name',
                                'clients_corporations.contact_person_first_name',
                                'clients_corporations.position'

                    ));

    }//infos



    public static function folder_name($user_id) {

        /*
         * Find the client
         */
        $client = Backend_Client::infos($user_id);

        $client = $client[0];


        $client_type_name = strtolower(Backend_Client_Type::get_name_by_client_type_id($client->client_type_id));

        

        if ($client->client_type_id == 1 ) {

            // $name = $client->first_name.'_'.$client->last_name;
            $name = str_replace(' ', '-',$client->last_name).'-'.str_replace(' ', '-',$client->first_name);

        }else {

            $name = str_replace(' ','-',$client->name);
        }

        $folder = new Portal('');

        $slash = $folder->back_slash;


        return 'clients'.$slash.$client_type_name.$slash.$name.'-'.$user_id;



    }//folder_name




    public static function get_first_level_folders()
    {   
        
            $client_types = Backend_Client_Type::get_all();

            
            $first_level_folders = [];

            foreach($client_types as $c) {

                $from_config = Config::get('portal.'.strtolower($c->name).'.first_level');

                foreach($from_config as $f) {

                    if(!in_array($f, $first_level_folders, true) && $f != '')  $first_level_folders[] = $f;

                }//enf 

            }//endforeach


            return $first_level_folders;

    }//get_first_level_folders


     public static function all_clients() {

         $query = DB::table('users as u')
                   ->join('clients_details as cd','u.id','=','cd.user_id')
                   ->join('client_size_type as cst','cd.client_size_type_id','=','cst.id')
                   ->join('clients_types as t','cd.client_type_id','=','t.id')
                   ->left_join('clients_individuals as i','cd.id','=','i.client_id')
                   ->left_join('clients_corporations as c','cd.id','=','c.client_id')
                   ->where('u.deleted','=', 0);
            
        return $query
                     ->get(array(
                                                    'u.id',
                                                    'u.email',
                                                    'u.username',
                                                    't.name as type',
                                                    't.id as type_id',
                                                    'cd.update_client as update_client',
                                                    'cst.sizetype',
                                                    'cd.new_files',
                                                    DB::raw( "IFNULL(CONCAT(i.first_name,' ',i.last_name) , c.name ) as name"),


                                                    ));

    }//all_clients


    /*
    * return true if the client has reaced $percentage_to_check % from allowed siee
    *
    */
    public static function has_reached_space($client_folder, $allowed_size, $percentage_to_check)
    {


        //get folder's size
        $folder = new Portal($client_folder);

        if ( $folder->status() ){

            //transform allowed size
             $allowed_size = $allowed_size*1024*1024;

            //folder size
            $folder_size =  $folder->get_folder_info_with_size()['folder_size'];

            $percentage_size = $percentage_to_check/100 * $allowed_size;


            if( $folder_size >= $percentage_size ) {

                return true;

            }//if has reached
            else {

                return false;
            }

        }//if the folder exists
        else {

            return false;

        }//if the folder doesn't exist

        

    }///has _reached space




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


    public static function create_folder($name, $start_folder, $client_type_folder_name, $old_start_folder) {
        

        $slash = Config::get('portal.back_slash');

        /*
        *
        *  Create folders - client type
        */
         $client_folder = new Portal('clients'.$slash.$client_type_folder_name);

        if(!$client_folder->status()){

            $parent = new Portal('clients');

            $parent->create($client_type_folder_name);

        }//is the cliet type folder was not created - create it

        $client_folder = new Portal('clients'.$slash.$client_type_folder_name);
       
         if($client_folder->is_writable() ) {

             $client_folder->create_user($name, $start_folder, $client_type_folder_name, $old_start_folder);

         }//if is writable


    }//create folder


}//end class



