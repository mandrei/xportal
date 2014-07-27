<?php

class Backend_Client_Type extends Base_Model
{

    public static $table = 'clients_types';

    public static function get_name_by_client_type_id($id)
    {
        return self::where('id','=',$id)
                   ->take(1)
                   ->only('name');
    }


    public static function get_all()
    {
    	return self::get(array('id','name'));

    }//get

}//Client_Type