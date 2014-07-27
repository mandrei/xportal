<?php

class Backend_User_Details extends Base_Model
{

    public static $table = 'users_details';



    public static function get_full_name_by_id($id)
    {
        return self::where('user_id','=',$id)
                   ->take(1)
                   ->only(DB::raw('CONCAT(last_name,", ",first_name) AS name'));
     

    }//get_name_by_id


    public static function get_name_for_emails($id) {

    	 return self::where('user_id','=',$id)
                   ->take(1)
                   ->only(DB::raw('CONCAT(last_name," ",first_name) AS name'));

    }//get name for emails



}//User_Types