<?php

class Backend_Client_Individual extends Base_Model
{

    public static $table = 'clients_individuals';

    public static $validation_rules_import_individuals = array(
    
    

        'calendar_year_start'         => 'required|not_in:0', 
        'title'                       => 'required|exists:client_individual_titles,title',
        'first_name'                  => 'required|min:2|max:50',
        'initials'                    => 'min:1|max:3',
        'last_name'                   => 'required|max:50',
        'default_client_name'         => 'required|max:100',
        'marital_status'              => 'exists:marital_status,name',

        //client-details table
        
        'street_address'              => 'max:1024',
        'city'                        => 'max:256',
        'country'                     => 'exists:countries,name',


        'phone'                       => 'min:10|max:20|match:/^([0-9\(\)\/\+ \-]*)$/',
        'fax'                         => 'min:10|max:20|match:/^([0-9\(\)\/\+ \-]*)$/',

        'email'                       => 'required|min:5|max:150|email|unique:users,email',

        'username'                    => 'required|min:1|max:50|unique:users,username',
        'client_size_type'            => 'required|exists:client_size_type,sizetype'
        
        
    );//validation rules
    


    public static function get_full_name_by_client_details_id($id)
    {
        $name = self::where('client_id','=',$id)
                    ->take(1)
                    ->only(DB::raw('CONCAT(last_name,",",first_name) AS name'));

        return $name;

    }//get_full_name_by_client_details_id


    public static function get_name_for_email_by_client_details_id($id) {

        return self::join('client_individual_titles','clients_individuals.title','=','client_individual_titles.id')
                    ->where('client_id','=',$id)
                    ->take(1)
                    ->only(DB::raw('CONCAT(client_individual_titles.title," ",clients_individuals.last_name) AS name'));

        
    }//get_name_for_email_by_client_details_id


    public static function get_individual_except($user_id)
    {
        return self::where('client_id','!=',$user_id)
                   ->get();

    }//get_individual_except


    public static function get_all() {

        $query = "SELECT 
                           u.id, CONCAT(ci.first_name,'_',ci.last_name) as name
                   FROM           
                            clients_individuals as ci
                    INNER JOIN 
                            clients_details as cd 
                    ON 
                            ci.client_id =  cd.id
                    INNER JOIN
                            users as u 
                    ON
                            cd.user_id = u.id
                    WHERE
                            u.deleted = 0 

                   ";

        return DB::query($query);

    }//get all 

    public static function delete_by_individual_id($individual_id)
    {
         return self::where('id','=',$individual_id)->delete();
       
    }//delete_by_individual_id


    public static function create($array) {

    
        return DB::table(self::$table)
                   ->insert_get_id(array(
                                        'client_id'                => $array['client_id'],
                                        'calendar_year_start'      => $array['calendar_year_start'],
                                        'title'                    => $array['title'],
                                        'first_name'               => $array['first_name'],
                                        'initials'                 => $array['initials'],
                                        'last_name'                => $array['last_name'],
                                        'default_client_name'      => $array['default_client_name'],
                                        'birth_date'               => $array['birth_date'],
                                        'marital_status'           => $array['marital_status']
                                        ));      



    }//create


}//Client_Individual