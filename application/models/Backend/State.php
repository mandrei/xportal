<?php

class Backend_State extends Base_Model
{

    public static $table = 'states';

    public static function get_state_name_by_id($state)
    {
        return self::where('id','=',$state)
                   ->take(1)
                   ->only('name');
    }

}//end class