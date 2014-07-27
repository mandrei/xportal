<?php

class Backend_Client_Corporation extends Base_Model
{

    public static $table = 'clients_corporations';

    public static $validation_rules_import_corporations = array(



        'name'                               => 'required|min:2|max:150',
        'calendar_year_start'                => 'required|after:0000-00-00',
        'contact_person_last_name'           => 'max:150',
        'contact_person_first_name'          => 'max:150',
        
        //client-details table
        'street_address'                     => 'max:1024',
        'city'                               => 'max:256',
        'country'                            => 'exists:countries,name',


        'phone'                              => 'min:10|max:10|match:/^([0-9\(\)\/\+ \-]*)$/',
        'fax'                                => 'min:10|max:20|match:/^([0-9\(\)\/\+ \-]*)$/',

        'email'                              => 'required|min:5|max:150|email|unique:users,email',

        'username'                           => 'required|min:1|max:50|unique:users,username',
        'client_size_type'                   => 'required|exists:client_size_type,sizetype'


    );//validation rules

    


    public static function get_full_name_by_client_details_id($id)
    {
        $name = self::where('client_id','=',$id)
                    ->take(1)
                    ->only('name');

        return $name;
    }




      public static function get_all() {

        $date = date('m-d');

        $query = "SELECT 
                           u.id, cc.name, LOWER(ct.name) as client_type
                   FROM           
                            clients_corporations as cc
                    INNER JOIN 
                            clients_details as cd 
                    ON 
                            cc.client_id =  cd.id
                    INNER JOIN
                            users as u 
                    ON
                            cd.user_id = u.id
                    INNER JOIN
                            clients_types as ct
                    ON
                            ct.id = cd.client_type_id
                    WHERE
                            u.deleted = 0 
                    AND
                            DATE_FORMAT(cc.fiscal_year_end, '%m-%d') = '$date'

                   ";

        return DB::query($query);

    }//get all 


   public static function  delete_by_corporation_id($corporate_id){

        return self::where('id','=',$corporate_id)->delete();

   }//delete_by_corporation_id($corporate_id)


   public static function create($array) {

    
        return DB::table(self::$table)
                   ->insert_get_id(array(
                                        'client_id'                         => $array['client_id'],
                                        'name'                              => $array['name'],
                                        'calendar_year_start'               => $array['calendar_year_start'],
                                        'contact_person_last_name'          => $array['contact_person_last_name'],
                                        'contact_person_first_name'         => $array['contact_person_first_name'],
                                        'position'                          => $array['position']
                                        ));      



    }//create
   

}//end class