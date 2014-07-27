<?php

class Backend_Language extends Base_Model{


     public static $table = 'languages';

    public static function get_abbr_by_id($lang_id)
    {

        return self::where('id', '=', $lang_id)
                   ->take(1)
                   ->only('abbreviation');

    }//get_abbr_by_id

}//end class