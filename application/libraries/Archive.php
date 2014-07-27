<?php

/**
 * Class Archive
 *
 * This class is used to  archive_folder,files and delete old archives.
 *
 */
class Archive {


       protected $archives_folder;

       protected $time;

       protected $backslash;

       protected $archives_folder_location;

       protected $life;//after how much time do we remove the archives

       public function __construct()
       {

            $this->archives_folder = Config::get('portal.archives_folder');

            $this->time = time();

            $this->backslash = Config::get('portal.back_slash');

            //the folder name contains a random string of 40 + time()
            $this->archives_folder_location = $this->archives_folder.$this->backslash.Str::random(40).'_'.$this->time.$this->backslash;

            /**
             *
             * Create the time directory where we will add the archive
             *
             */
            if(!is_dir($this->archives_folder)){

                mkdir($this->archives_folder);
            }

             mkdir($this->archives_folder_location);

       }//construct


    /**
     * Archives a given folder and it returns the string
     *
     * @param $path
     * @return false if there were problems, the path to the archive otherwise
     */
    public function archive_folder($path)
    {

        ini_set('memory_limit', '-1');

        //path where the archive will be created
        $path_zip = $this->archives_folder_location.basename($path).'.zip';


        $zip = new ZipArchive();

        //create the file and throw the error if unsuccessful
        if ($zip->open($path_zip, ZipArchive::CREATE ) !== true) return false;


        //add folder to archive
        $path = str_replace('\\', $this->backslash, realpath($path));

        //if is directory
        if (is_dir($path) === true)
        {

           //take everything from the folder directories ,subdirectories ,files...
           $folder_path = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);

            //foreach subdirectory
            foreach($folder_path as $fp)
            {
                //replace \\ , / with $fp
                $fp = str_replace('\\', $this->backslash, $fp);

                //if in array subdirectories continue
                if( in_array(substr($fp, strrpos($fp, $this->backslash) + 1 ), array('.', '..')) )
                    continue;

                 //get real path
                $fp = realpath($fp);

                //if is dir
                if(is_dir($fp) == true)
                {

                    $zip->addEmptyDir(str_replace($path. $this->backslash,'',$fp.$this->backslash));


                }//if is folder
                else if(is_file($fp) === true)
                {
                    $zip->addFromString(str_replace($path . $this->backslash, '', $fp), file_get_contents($fp));

                }//else if is file
            }


        }//if is dir
        else if(is_file($path) === true)
        {
            $zip->addFromString(basename($path),file_get_contents($path));

        }//else is file

        $zip->close();

        //return the path where the archive is
        return $path_zip;

    }//archive_and_download



    /**
     * Archives given files and it returns the string
     *
     * @param $files
     * @return false if there were problems, the path to the archive otherwise
     */
    public function archive_files($files)
    {

        //path where the archive will be created
        $path_zip = $this->archives_folder_location.'xPortal_'.date("Y-m-d").'.zip';


        $zip = new ZipArchive();

        //create the file and throw the error if unsuccessful
        if ($zip->open($path_zip, ZipArchive::CREATE ) !== true) return false;

        //add folder to archive

            //foreach files
            foreach($files as $fp)
            {

                //get real path
                $fp = realpath($fp);

                //remove path from inside the archive
                $local_path = substr($fp,strrpos($fp,$this->backslash) + 1);

                //check if is file
                if(is_file($fp) === true)
                {
                    $zip->addFile($fp, $local_path);

                }//if is file
                else
                {
                   return false;

                }//if not file return false

           }//foreach files


         $zip->close();

        //return the path where the archive is
        return $path_zip;

    }//archive_files_and_download



    /**
     * Deletes archives older than 1 day
     *
     * @return bool
     *
     */
    public static function garbage_collector()
    {

        $slash = Config::get('portal.back_slash');
        //path to archives folders
        $paths = glob(Config::get('portal.archives_folder').$slash.'*');

        $yesterday = strtotime("yesterday");
   
        foreach($paths as $p)
        {


            /**
             * Extract the time from folder name (the folders are composed from 40 char + _ + time()
             */
            $folder = substr(basename($p),41);

            settype($folder,"integer");

        
            //if archive older than 1 day
            if($folder < $yesterday)
            {

              Folder::quick_delete($p);
       
            }//foreach path

        }//foreach archive folders


    }//garbage_collector

}//end class