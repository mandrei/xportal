<?php

class Backend_User_Permission extends Base_Model
{

    public static $table = 'users_permissions';

    public static function permission($user_id)
    {


        $sql = "SELECT
                        modules.id ,modules.name,

                        if( (SELECT
                                        users_permissions.id

                             FROM
                                        users_permissions

                             WHERE
                                        users_permissions.user_id ='".$user_id."'

                             AND
                                        modules.id = users_permissions.module_id)

                            , 1, 0 ) AS permission

                FROM
                        modules
               ";

    
        return DB::query($sql);


    }//permission


    public static function get_only_permissions($user_id)
    {

        $permissions = self::permission($user_id);

        $to_return = array();

        foreach( $permissions as $p )
        {

            if( $p->permission == 1 )
            {

                $to_return[] = $p->id;

            }//if it has permission

        }//foreach permission


        return $to_return;

    }//get_only_permissions


    



}//User_Types