<?php

class Backend_Province extends Base_Model
{

    public static $table = 'provinces';

    public static function get_province_name_by_id($province)
    {
        return self::where('id','=',$province)
                   ->take(1)
                   ->only('name');

    }//get province name by id

}//end class
