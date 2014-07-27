<?php

class Backend_Client_Size extends Base_Model
{

    public static $table = 'client_size_type';


    /*
     * Associations per client id
     */
    public static function get_size_type()
    {

        $size_obj = self::get(array(
                                            'id',
                                            'sizetype'

                    ));


        return $size_obj;

    }//get_associations_per_client_id*/


}//end class