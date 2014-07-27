<?php

class Backend_Settings extends Base_Model
{


    public static $table = 'settings';



    public static $validation_rules = 'required|max:200';

    public static function validation_rules() {

        $validation_rules = array(); 

        $settings = self::get();

       
        foreach ($settings as $s) {

            if($s->name == 'system_email' || $s->name == 'alert_notification_email') {

                $validation_rules[$s->name] = 'required|max:200|email'; 

            }else {

                $validation_rules[$s->name] = 'required|max:200'; 
            }
            

        }//endforeach

        return $validation_rules;

    }//validate


    /*
     * Used to return a single value based on the name of that setting
     */
    public static function r($name)
    {
        return self::where('name', '=', $name)
                   ->take(1)
                   ->only('value');
    }//r


}//end class
