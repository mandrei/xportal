<?php

class Portal_Task
{

/**
 * Task portal
 * This task is used to delete archives older than 1 day
 */

   public function garbage_collector()
   {

       Archive::garbage_collector();

       /*
       * Clear imported files too
       *
       */
       $imported_files_folder_path = Config::get('portal.import_folder');

       $slash = Config::get('portal.back_slash');



        //path to archives folders
        $paths = glob($imported_files_folder_path.$slash.'*');

        $threedaysago = strtotime("-3 days");
   
        foreach($paths as $p)
        {


            /**
             * Extract the time from folder name 
             */
            $folder = substr(basename($p),-23, 10);

            $folder_date = strtotime($folder);


            settype($folder_date,"integer");

        
            //if archive older than 1 day
            if($folder < $threedaysago)
            {
            
              File::delete($p);
       
            }//foreach path

        }//foreach archive folders




   }//portal

}//end class