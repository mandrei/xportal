<?php

class Backend_Client_Details extends Base_Model
{

    public static $table = 'clients_details';

    
    public static function get_details_by_client_id($client_id)
    {

        return self::where('user_id','=',$client_id)
                   ->take(1)
                   ->get();

    }//get_details_by_client_id


    public static function get_client_type_by_client_id($user_id)
    {

           $type = self::where('user_id','=',$user_id)
                      ->take(1)
                      ->only('client_type_id');

          return $type;

    }//get_client_type_by_client_id


    public static function get_client_details_id_by_client_id($client_id)
    {

        $details_id = self::where('user_id','=',$client_id)
                          ->take(1)
                          ->only('id');

        return $details_id;

    }//get_client_details_id_by_client_id


    public static function get_client_type_id_by_user_id($id)
    {

        return self::where('user_id','=',$id)
                   ->take(1)
                   ->only('client_type_id');

    }//get_client_type_id_by_user_id



    public static function get_client_corporations()
    {
        return self::join('users as u','u.id','=','clients_details.user_id')
                   ->join('clients_corporations as cp','cp.client_id','=','clients_details.id')
                   ->where('u.deleted','=',0)
                   ->where('clients_details.client_type_id','!=',1)
                   ->order_by('cp.name')
                   ->get(array(
                                'u.id',
                                'cp.name'
            ));

    }//get_corporation_except


    public static function get_client_individuals()
    {
        return self::join('users as u','u.id','=','clients_details.user_id')
                   ->join('clients_individuals as ci','ci.client_id','=','clients_details.id')
                   ->where('u.deleted','=',0)
                   ->where('clients_details.client_type_id','=',1)
                   ->order_by('ci.default_client_name')
                   ->get(array(
                                'u.id',
                                'ci.default_client_name'

            ));

    }//get_corporation_except


    public static function update_new_files($client_id, $status_to_set) {

      return self::where('user_id','=',$client_id)
                  ->update(array('new_files'=>$status_to_set));

    }//update_new_files


    public static function create($array) {

    
        return DB::table(self::$table)
                   ->insert_get_id(array(
                                        'user_id'             => $array['user_id'],
                                        'street_address'      => $array['street_address'],
                                        'city'                => $array['city'],
                                        'province'            => $array['province'],
                                        'postal_code'         => $array['postal_code'],
                                        'state'               => $array['state'],
                                        'zip_code'            => $array['zip_code'],
                                        'country'             => $array['country'],
                                        'phone'               => $array['phone'],
                                        'fax'                 => $array['fax'],
                                        'client_type_id'      => $array['client_type_id'],
                                        'client_size_type_id' => $array['client_size_type_id'],
                                        'folders_size'         => $array['folders_size'],
                                        'update_client'       => $array['update_client'],
                                        'new_files'           => $array['new_files'],
                                        ));      



    }//create

}//Client_Types