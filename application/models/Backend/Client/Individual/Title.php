<?php

class Backend_Client_Individual_Title extends Base_Model
{

    public static $table = 'client_individual_titles';


    public static function get_title_name($title)
    {
        return self::where('id','=',$title)
                   ->take(1)
                   ->only('title');
    }//get title

}//end class