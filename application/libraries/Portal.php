<?php

/**
 * Portal class
 *
 * Each instance of the portal works with a single folder that needs to be passed as a parameter
 */
class Portal {


    /**
     * @var string - All folders are part of this folder
     */
    public $parent_folder_path;


    /**
     * Selected folder
     */
    public $folder;

    /**
     * Directory separator
     */
    public $back_slash;


    /**
     * Class used to manipulate folders
     */
    public $folder_tools;


    /**
     * When we create folders for clients we need to generate them from 2005 to present
     */
    public $start_year;


    public function __construct($folder)
    {

        /*
         * The root of the folder with which we work, this folder contains all folders of the Portal
         */
        $this->parent_folder_path = Config::get('portal.portal_parent_folder');

        /*
         * Establish the back slash used based on the OS
         */
        $this->back_slash = Config::get('portal.back_slash');

        /*
         * Establish the full path to the selected folder, if it's '' we work with the parent folder
         */
        $this->folder = $folder == '' ? $this->parent_folder_path : $this->parent_folder_path . $this->back_slash . $folder;

//        echo $this->folder;exit;
        /**
         *
         * Check if folder exists
         *
         * If not we stop here otherwise there will be some errors
         */
        if(!is_dir($this->folder)) return false;



        /*
         * Assign folder class
         */
        $this->folder_tools = new Folder($this->folder);


        /*
         * The year from which the client needs folders
         */
        $this->start_year = Config::get('portal.portal_folders_start_year');

    }///__construct()


    /**
     * Returns false if the folder doesn't exist
     */
    public function status()
    {

        return is_dir($this->folder);

    }//status

    /**
     * Checks if the folder is writable
     *
     * @return bool
     */
    public function is_writable()
    {

        return $this->folder_tools->is_writable();

    }//check if the folder is writable


    /**
     * Checks if the folder is empty
     *
     * @return bool
     */
    public function is_dir_empty()
    {

        return $this->folder_tools->is_dir_empty();

    }//check if the folder is writable



    /**
     * Return the folders from a folder, not recursive
     *
     * @param $ignore array //list of folders to ignore
     * @return array
     *
     */
    public function get_folders($ignore = null)
    {

        return $this->folder_tools->folders($ignore);

    }//get_folders


    /**
     * Returns all information needed on the right side:
     *
     *  * total files
     *  * folder size
     *  * date time created
     *  * last edited date time
     *
     * @return array
     */
    public function get_folder_info_with_size()
    {

        return array(
            'total_files'        => $this->folder_tools->count_files($this->folder),
            'folder_size'        => $this->folder_tools->folder_size($this->folder),
            'folder_creation'    => $this->folder_tools->creation_time,
            'folder_modification'=> $this->folder_tools->modification_time,
            'last_files_added'   => $this->folder_tools->last_files_added($this->folder, 3)
        );

    }//get_folder_info


    public function get_folder_info_without_size()
    {

        return array(
            'total_files'        => $this->folder_tools->count_files($this->folder),
//            'folder_size'        => $this->folder_tools->folder_size($this->folder),
            'folder_creation'    => $this->folder_tools->creation_time,
            'folder_modification'=> $this->folder_tools->modification_time,
            'last_files_added'   => $this->folder_tools->last_files_added($this->folder, 3)
        );

    }//get_folder_info


    /**
     * Returns all the files from the folder
     *
     * @return array
     */
    public function files()
    {

        return $this->folder_tools->files();

    }//files

    /**
     * Create a folder in current folder
     *
     * @param $folder_name string the name of the folder to be created
     * @return bool
     */
    public function create($folder_name)
    {

        return $this->folder_tools->create($folder_name);

    }//create


    /**
     *  TBA
     */
    public function rename($new_name)
    {
        //$this->folder_tools = new Folder($this->folder);

        return $this->folder_tools->rename($new_name);

    }//rename

    /**
     * Delete the entire contents of a folder
     *
     */
    public function delete_recursive()
    {

        return $this->folder_tools->delete();

    }//delete_recursive

    /**
     *
     * @param $files array path to the files to be deleted
     *
     * @return int //number of deleted files
     */
    public function delete_files($files)
    {

        return $this->folder_tools->delete_files($files);

    }//delete_files

    /**
     * Downloads any given file
     *
     * @param $file string //the name of the file, the file must exist in this folder
     * @return file
     */
    public function download_file($file)
    {
        return Response::download($this->folder . $this->back_slash . $file);

    }//download_file
    

    /**
     * Archive the folder
     * @return /path to the folder archive
     *
     */
    public function archive_folder()
    {
        //Instantiate Archive class
        $archive = new Archive;

        return $archive->archive_folder($this->folder);

    }//archive_folder


    /**
     * Archive multiple files and prompt download
     *
     * @param $files array //list of files to archive, the files must exists in current folder
     *
     * @return /path to the files archive
     */
    public function archive_files($files)
    {
        //Instantiate Archive class
        $archive = new Archive;

        /*
         * Assign full path to files
         */
        foreach( $files as $key => $value )
        {

            $files[$key] = $this->folder . $this->back_slash . $value;

        }//foreach files

        return $archive->archive_files($files);

    }//archive_files


    /**
     * Check if a folder exists
     *
     * @param $folder_name
     * @return bool
     */
    public function folder_exists($folder_name)
    {

        return $this->folder_tools->folder_exists($folder_name);

    }//folder_exists


     /**
     * Check if a file exists
     *
     * @param $file_name
     * @return bool
     */
    public function file_exists($file_name)
    {

        return $this->folder_tools->file_exists($file_name);

    }//file_exists



    /**
     * Deletes archives older than 1 day
     * @return bool
     *
     */
    public function delete_old_archives()
    {

        $archive = new Archive();

        $archive->garbage_collector($archive);

    }//delete_old_archives




    /** Creates a new user
     *
     * We iterate from start year until current and create the folder structure based on the client type
     *
     * @param $name string //The name of the client
     * @param $start_folder string //The name of the first folder
     * @param $client_type string //As it's declared in portal config for folder structure
     * @param $old_start_folder string//if is set we have to rename old folders
     */
    public function create_user($name, $start_folder,  $client_type, $old_start_folder = false)
    {


        $current_year = date('Y');

        $folder_structure = Config::get("portal.{$client_type}");

     
        /*
         * Create client folder
         */
        $this->folder_tools->create($name);

         //add the folders client folder
        foreach( $folder_structure['first_level'] as $folder )
        {

            Folder::quick_create($this->folder . $this->back_slash . $name .  $this->back_slash . $folder);

        }//foreach folder structure



        
        if($client_type == 'individual') {

            $start_year = $start_folder;

            $start      = '';
        }
        else {

            $start_year = date('Y',strtotime($start_folder));

            $start      = date('F d, ',strtotime($start_folder));
        }



        if($old_start_folder) {

            $old_start =   date('F d, ',strtotime($old_start_folder));

        }//if was set the old folder
        else {

            $old_start = '';
        }

       


        do
        {

            if( $this->folder_tools->folder_exists($name . $this->back_slash . $old_start . $start_year) ) {

                $folder_to_rename = new Folder($this->folder_tools->folder.$this->back_slash .$name . $this->back_slash . $old_start . $start_year);


                $folder_to_rename->rename( $start . $start_year);

            }//if for the current year the folder with the old_start month and year exists - rename it
            else {

        
                 //create the year folder
                 $this->folder_tools->create($name . $this->back_slash . $start . $start_year);  


                //add the folders in years folder
                foreach( $folder_structure['second_level'] as $f )
                {

                    Folder::quick_create($this->folder . $this->back_slash . $name . $this->back_slash . $start . $start_year . $this->back_slash . $f);

                }//foreach folder structure


            }//else create it

            //increment the year
            $start_year++;

        }

        while( $start_year <= $current_year );


    }//create_user


}//end class