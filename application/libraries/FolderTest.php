<?php


/*
 * The class is used to handle actions or give information's for any given folder
 */
class FolderTest {

    public $folder;

    public $creation_time;//timestamp

    public $modification_time;//timestamp

    public $backslash;//must be passed in constructor, this differs from OS to OS

    public function __construct($folder)
    {


        $this->folder = $folder;

        $this->backslash = Config::get('portal.back_slash');

        /*
         * Establish folder's proprieties using stat
         */
        $properties = stat($folder);

        $this->creation_time = $properties['ctime'];

        $this->modification_time = $properties['mtime'];

        //free memory
        unset($properties);

    }//construct


    /**
     * @return bool
     */
    public function is_writable()
    {

        return is_writable($this->folder);

    }//is_writable

    /**
     * Return a list of all folders that exist within, not recursive
     *
     * @param $ignore array //optional, if we want to skip certain folders
     * @return array
     */
    public function folders($ignore=null)
    {

        $directory = $this->folder. $this->backslash . "*";

        $ignore_folders = $ignore == null ? array() : $ignore;

        $folder_array = array();

        foreach(glob($directory, GLOB_ONLYDIR) as $f)
        {
            if( !in_array(basename($f), $ignore_folders) )
            {

                $folder_array[] = $f;

            }//if the file is not on ignore list

        }//foreach folder

        return $folder_array;

    }//folders



    /**
     * Return all files from a folder
     *
     * @param $folder [string] full path to the folder
     * @return array
     */
    public function files()
    {
        $folder_route = $this->folder. $this->backslash . "*";

        $files_array = array();

        foreach(array_filter(glob($folder_route), 'is_file') as $fi)
        {
            $files_array[] = $fi;

        }//foreach files

        return $files_array;

    }//files


    /**
     * Count all files from a folder
     *
     * @param $folder [string] full path to the folder
     * @return int
     */
    public function count_files($folder)
    {
        $folder .= $this->backslash . "/*";

        $files = $folder;

        $i = 0;

        foreach(array_filter(glob($files), 'is_file') as $fi)
        {
            $i++;

        }//foreach files

        return $i;

    }//count_files



    /**
     * Returns the size of a folder - recursive
     *
     * @return int - the size in bytes
     */
    public function folder_size()
    {

        $folder = $this->folder;

        $size = 0;

        foreach(new DirectoryIterator($folder)  as $f)
        {

                if($f->isDot() && $f->getBasename() == '.') {

                        echo "<pre>";
                        print_r($f->getBasename());
                        echo "</pre>";

                    echo "<pre>";
                    print_r($f->getSize());
                    echo "</pre>";

                }




        }//foreach
        exit;

        return $size;

    }//folder_size




    /**
     * Returns last files added - descending order
     *
     * @param $folder [string] full path to the folder,
     * @param $count [int] the number of files to return
     * @return array
     */
    public function last_files_added($folder, $count = 3)
    {

        $folder .= "/*";

        $files = $folder;

        $files_array = array();

        //create array with all the files routes and their created date
        foreach(array_filter(glob($files), 'is_file') as $fi)
        {
            $files_array[$fi] = filemtime($fi);

        }//foreach files


        //sort
        arsort($files_array);


        $newest = array_slice($files_array, 0, $count);

        $files_array = array();

        foreach($newest as $key => $n ) {

            $files_array[] = $key;
        }


        return $files_array;


    }//last_files_added




    /**
     * Delete recursively the content of a folder
     *
     * @return boolean - true if the folder was deleted
     */
    public function delete()
    {

        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->folder, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path)
        {

            $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());

        }//foreach path found

        rmdir($this->folder);


        //to check if the folder was deleted simply check if directory exists
        return !is_dir($this->folder);

    }//delete


    /**
     * Deletes all files from a folder
     *
     * @param $files array
     * @return bool / array // returns number of deleted files if all files were deleted, otherwise it returns an array with undeleted
     *                         files
     */
    public function delete_files($files)
    {

        $count = 0;//no of files deleted

        $undeleted_files = array();

        foreach($files as $f)
        {

            //full path to the file to be deleted
            $file_to_delete = $this->folder . $this->backslash . $f;


            if( file_exists( $file_to_delete ) )
            {

                if( !unlink( $file_to_delete ) ) $undeleted_files[] = $f;


                $count++;//increment no of deleted items

            }//if the file exists


        }//foreach file

        if(count($undeleted_files) > 0)
        {
            return $undeleted_files;

        }//if count of undeleted files is not empty
        else
        {
            return $count;

        }//else return the count of files


    }//delete files


    /**
     * Creates a directory, if the directory exists returns true
     *
     * @param string $folder_name
     * @return bool
     */
    public function create($folder_name)
    {

        if(is_dir( $this->folder . $this->backslash . $folder_name)) return true;

        return mkdir( $this->folder . $this->backslash . $folder_name );

    }//create


    /**
     * Check if a folder exists
     *
     * @param $folder_name
     * @return bool
     */
    public function folder_exists($folder_name)
    {
        return is_dir( $this->folder . $this->backslash . $folder_name );

    }//folder_exists



    /**
     * The same as delete but static
     *
     * Delete recursively the content of a folder
     *
     * @param $folder string // the full path of the folder to be deleted
     * @return boolean - true if the folder was deleted
     */
    public static function quick_delete($folder)
    {

        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path)
        {

            $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());

        }//foreach path found

        rmdir($folder);


        //to check if the folder was deleted simply check if directory exists
        return is_dir($folder);

    }//quick_delete



    /**
     * The same as create() only static
     *
     * Creates a directory, if the directory exists returns true
     *
     * @param string $folder_name //Full path
     * @return bool
     */
    public static function quick_create($folder_name)
    {

        //return true if the folder exists already
        if( is_dir( $folder_name ) ) return true;

        return mkdir( $folder_name );

    }//create



    public function rename($new_name)
    {

        //the name of the folder without path
        $folder_base_name = basename($this->folder);

        //folder path where we rename the folder
        $folder_path = strstr($this->folder, $folder_base_name, true);


        return rename($this->folder, $folder_path . $new_name);

    }//rename

    public function is_dir_empty() {
        if (!is_readable($this->folder)) return NULL;
        $handle = opendir($this->folder);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                return FALSE;
            }
        }
        return TRUE;
    }//is dir empty



}//end class