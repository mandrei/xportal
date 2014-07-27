<?php

class Files {


	/*
	* The route to the file
	*
	*/
	private $route;


	public function __construct() {}//construct


	/*
	* Setters
	*
	*/
	public function set_route($route){

		$this->route = $route;

	}//set route


	/*
	* Getters
	*
	*/
	public function get_route(){

		return $this->route;

	}//get route




    /**
     * Returns the size of a file
     *
     * @param $file [string] full path to the file
     * @return int - the size in bytes
     */
    public function file_size()
    {
        return filesize($this->route);

    }//file_size



    public static function upload_file($file, $destination_path, $filename = false ){


    	/*
    	* Validations
    	*
    	*/
    	//file
    	if ($file['error'] != 0 || $file['name'] == '' ) return false;

		//destination_path
    	if( !file_exists($destination_path) || !is_dir($destination_path) ) {

    		mkdir($destination_path);

    	}//create folder if not exists


		/**--------------=----------------------------------------
		 *
		 * Make sure the a file was uploaded
		 *
		 ------------------------------------------------------*/
		if( !file_exists( $file['tmp_name'] ) || !is_uploaded_file( $file['tmp_name'] ) )
		{

			return false;

		}//if there was something wrong with the upload


		$slash = Config::get('portal.back_slash');

		if(!$filename) $filename = $file['name'];



		/**------------------------------------------------------
		 *
		 * Now we upload the file
		 *
		 ------------------------------------------------------*/
		if( File::move($file['tmp_name'], $destination_path.$slash.$filename )  )
	 	{
	 		return true;

	 	}//if we uploaded the file
	 	

 		return false;	 		
	 		
    }//upload_file




}//end class Files