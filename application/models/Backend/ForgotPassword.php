<?php

class Backend_ForgotPassword extends Base_Model
{

    public static $table = 'forgot_password';


    public static function check_request($email){

        //get 2 hours ago time
        $two_hours_ago = date('Y-m-d H:i:s',strtotime('-2 hours'));

        return   DB::table('forgot_password as f')
                   ->join('users as u','f.user_id','=','u.id')
                   ->where('u.email','=',$email)
                   ->where('f.time','>',$two_hours_ago)
                   ->get(array('u.id'));


    }//check request


    public static function check_string($string){

        //get 2 hours ago time
        $two_hours_ago = date('Y-m-d H:i:s',strtotime('-2 hours'));

        return self::where('random_string','=',$string)
                   ->where('time','>',$two_hours_ago)
                   ->only('user_id');

    }//check request



   /*
    *
    *  Delete requests < 2 hours ago
    *
    */
    public static function clear_requests() {

        $two_hours_ago = date('Y-m-d H:i:s',strtotime('-2 hours'));

        self::where('time','<',$two_hours_ago)->delete();

    }//clear_requests


}//end class