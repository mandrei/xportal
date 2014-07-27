<?php

class Backend_Module extends Base_Model
{


    public static $table = 'modules';


    public static function get_modules(){


        /*
        *
        *  Get parent modules
        *
        */
        $parent_modules = self::where('parent','=',0)->get();

        $modules = array();

        foreach($parent_modules as $p) {

            $modules[] = array(
                                    'id'    => $p->id,
                                    'name'  => $p->name,
                                    'kids'  => self::where('parent','=',$p->id)->get()
                                );

        }//endforeach

      
      return $modules;
        
     }//get_tree





}//end class
