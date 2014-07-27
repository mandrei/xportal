<?php

class Backend_Folder extends Base_Model {



     public static $validation = array(

                            'folder_name'    => 'required|max:100',
                            'folder_route'	 => 'required'
    );

    

}//end class