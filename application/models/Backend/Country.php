<?php

class Backend_Country extends Base_Model
{

    public static $table = 'countries';

    public static function get_name_by_id($country)
    {
        return self::where('id','=',$country)
                   ->take(1)
                   ->only('name');
    }

}//end class
