<?php

class Backend_Portal_Controller extends xPortal_Controller
{



    public function get_index()
    {


       /*
       * Get folders by user type
       *
       */
       $user = Session::get('user');


       //get all client types
       $client_types = Backend_Client_Type::get();

       //init var
       $folders =  array();


       $parent_route  = Config::get('portal.portal_parent_folder');

       $slash         = Config::get('portal.back_slash');



       /*
       * Get personal folder
       *
       */
       $personal_folder_path = Backend_User::personal_folder_path($user['id']);



      
       if($user['type'] == 0) {


            if(is_dir($parent_route.$slash.$personal_folder_path)) {

                 //add personal folder to folders list
                 $folders[]   = array(
                                          'name' => 'Personal Folder',
                                          'route' => $personal_folder_path
                                      );

             }//if the directory exists


              if(is_dir($parent_route.$slash.'users')) {

                  //add users folder
                  $folders[] = array(
                                      'name'  => 'Users',
                                      'route' => 'users'
                                      );

              }//is the dir exists

               
               if(is_dir($parent_route.$slash.'clients')) {

                    //add clients folder
                    $folders[] = array(
                                        'name'  => 'Clients',
                                        'route' => 'clients'
                                        );

              }//is the dir exists



         }//if admin
  
         else if($user['type'] == 1){

           if(is_dir($parent_route.$slash.$personal_folder_path)) {

                   //add personal folder to folders list
                   $folders[]   = array(
                                            'name' => 'Personal Folder',
                                            'route' => $personal_folder_path
                                        );

               }//if the directory exists


            if(is_dir($parent_route.$slash.'clients')) {

                    //add clients folder
                    $folders[] = array(
                                        'name'  => 'Clients',
                                        'route' => 'clients'
                                        );

              }//is the dir exists


            


         }//if user 
         else {


              if(is_dir($parent_route.$slash.$personal_folder_path)) {

                  $portal = New Portal($personal_folder_path);

                  $folders_obj  = $portal->get_folders();

                  //get the array of the first level folders for all client types
                  $first_level_folders = Backend_Client::get_first_level_folders();

                  $first_folders = [];

                  foreach ($folders_obj as $f){

                        // if(basename($f) == 'Article of Incorporation' || )

                    if(in_array(basename($f), $first_level_folders, true)) {

                      $first_folders[]  =  array(
                                                    'name'  => basename($f),
                                                    'route' => $personal_folder_path.$slash.basename($f)
                                                );

                    }//if one of the first level folders
                    else {

                         $folders[] = array(
                                          'name'  => basename($f),
                                          'route' => $personal_folder_path.$slash.basename($f)
                                      );
                    }

                      
                  }

               sort($folders, SORT_FLAG_CASE);


               arsort($first_folders);

                
                foreach($first_folders as $ff) {

                     array_unshift($folders, array(
                                                'name'  => $ff['name'],
                                                'route' => $ff['route']
                                          ));

                }
              

               
                 /*
                * Get user's id of is user or client' folder
                *
                */
                $base_name  = basename($personal_folder_path);

                $position   = strpos($base_name,'-');

                $id        = substr($base_name, 0, $position );

         
                if ($id && Backend_User::get_account_route($id, $personal_folder_path) ){
                

                     array_unshift($folders, array(
                                                'name'  => 'My Profile',
                                                'route' => Backend_User::get_account_route($id, $personal_folder_path)
                                          ));

                 }

                
             }//if the directory exists


         }//if client


        /*
         * View data
         */
        $data = array(
            'selected_page'       => 'portal',
            'breadcrumbs'         => array(
                                          // 'safe/home'           => __('common.home'),
                                          'last'                  => __('common.portal')
                                         ),

            'folders'             => $folders

        );

        return View::make('backend.portal.portal',$data);


    }//GET index




    /*
    *
    *   Get folders and files for portal page
    *   when click on folder name
    */
    public function get_folders_and_files() {

        if(Request::ajax() ){

            /*
            *
            *  Get inputs - folder route
            *
            */
            $slash = Config::get('portal.back_slash');


            $folder_route      =  Input::get('folder_route','');

          
            $return = array();


            $folder = new Portal($folder_route);



         if ($folder_route != '' && Backend_User::has_folder_access($folder_route) ) {


              if($folder_route == 'clients' && Cache::has('clients_subfolders') ) {


   
                      $subfolders = Cache::get('clients_subfolders');

                      $folder_information_array = Cache::get('clients_subfolders_info');



              }//if clients 
              else if($folder_route == 'clients'.$slash.'individual' && Cache::has('individuals_subfolders') ) {

                      $subfolders = Cache::get('individuals_subfolders');

                      $folder_information_array = Cache::get('individuals_subfolders_info');

              }else if ( $folder_route == 'clients'.$slash.'corporate' && Cache::has('corporate_subfolders') ) {

                      $subfolders = Cache::get('corporate_subfolders');

                      $folder_information_array = Cache::get('corporate_subfolders_info');

              }else {


                /*
                * If not cached
                *
                */
                $user_folder = Backend_User::personal_folder_path(Session::get('user.id'));

                 $user_folder_array = explode($slash, $user_folder);


                //get subfolders
                $subfolders_array = $folder->get_folders(array(end($user_folder_array)));

                $subfolders = array();




                $first_level_folders = Backend_Client::get_first_level_folders();

                $first_folders = [];


                foreach($subfolders_array as $f) {

                  if($folder_route == 'users' || $folder_route == 'clients'.$slash.'individual' || $folder_route == 'clients'.$slash.'corporate' ) {

                    $base_name  = basename($f);

                    $exploded_name = explode('-',$base_name);

                    $position   = strrpos($base_name,'-');
//
//                    $id         = substr($base_name, -$position);

                      $id = end($exploded_name);

                  
                      if((int)$id) {

                         $file_name = str_replace('-',' ',substr($base_name,0,$position));
                      }
                      else{

                          $file_name = basename($f);
                      }
                  

                  }//if users or client routes
                  else {

                    $file_name = basename($f);

                  }//

                   if(in_array(basename($f), $first_level_folders, true)) {

                             $first_folders[]  =  array(
                                                    'name'  => basename($f),
                                                    'route' => $folder_route.$slash.basename($f)
                                                );

                    }else {
                   
                           $subfolders[] = array(
                                                  'name'   => $file_name,
                                                  'route'  => $folder_route.$slash.basename($f)
                                                );

                         }

                }//loop subfolders

                sort($subfolders);



                arsort($first_folders);

                
                foreach($first_folders as $ff) {

                     array_unshift($subfolders, array(
                                                'name'  => $ff['name'],
                                                'route' => $ff['route']
                                          ));

                }
              

               
                 /*
                * Get user's id of is user or client' folder
                *
                */
                $base_name  = basename($folder_route);

                // $position   = strpos($base_name,'_');

                // $id        = substr($base_name, 0, $position );

                $exploded_name = explode('-',$base_name);

                $position   = strrpos($base_name,'-');

                $id = end($exploded_name);

         
                 if( (int)$id && Backend_User::get_account_route($id, $folder_route) ) {

                     array_unshift($subfolders, array(
                                                'name'  => 'My Profile',
                                                'route' => Backend_User::get_account_route($id, $folder_route)
                                          ));
                 }



                  if( $folder_route == 'clients' || $folder_route == 'clients'.$slash.'individual'|| $folder_route == 'clients'.$slash.'corporate'
                  || $folder_route == 'users'  ) {

                      $folder_information_array = $folder->get_folder_info_without_size();

                  }else {

                      $folder_information_array = $folder->get_folder_info_with_size();
                  }




                 /* Cache folders */

                   if($folder_route == 'clients' ) {
           
                            Cache::put('clients_subfolders',$subfolders,60);

                            Cache::put('clients_subfolders_info',$folder_information_array,60);
                    
                    }//if clients 
                    else if($folder_route == 'clients'.$slash.'individual'  ) {

                            Cache::put('individuals_subfolders',$subfolders,60);

                            Cache::put('individuals_subfolders_info',$folder_information_array,60);

                    }else if ( $folder_route == 'clients'.$slash.'corporate' ) {

                             Cache::put('corporate_subfolders',$subfolders,60);

                             Cache::put('corporate_subfolders_info',$folder_information_array,60);

                    }

                 /* End cache folders */
  


                

               }//end else cached

               
                
                //get files
                $files_array = $folder->files();

                $files = [];

                foreach($files_array as $file) {

                  $files[] = array(
                                    'name'   => basename($file),
                                    'route'  => $file
                                  );


                }//loop files

                 sort($files);

               
       

                //get infos
                // $folder_information_array = $folder->get_folder_info();

                $folder_information['total_files']          = $folder_information_array['total_files'];
                if( isset($folder_information_array['folder_size']) ) {
                    $folder_information['folder_size']          = Convertor::bytes_to_mb($folder_information_array['folder_size']);
                }
                $folder_information['folder_creation']      = date(Config::get('portal.date_format'),$folder_information_array['folder_creation']);
                $folder_information['folder_modification']  = date(Config::get('portal.date_format'),$folder_information_array['folder_modification']);
                $folder_information['last_files_added']     = array();



                foreach($folder_information_array['last_files_added'] as $lfa) {

                    $folder_information['last_files_added'][] = basename($lfa);

                }//endforeach

                //data for view
                $return = array(
                                  'subfolders'          => $subfolders, 
                                  'files'               => $files, 
                                  'folder_information'  => $folder_information
                                  );

              
                //only files
                return  Response::json($return);

            }//if we clicked on folder  - get subfolders and files


            

        }//if ajax request

    }//POST folders and files




    /*
    *
    *  Get folder info - right column, files, folder info, last_files_added 
    *
    */
      public function post_folder_info() {

        if(Request::ajax() ){

           /*
            *
            *  Get inputs - folder route
            *
            */
            $folder_route      = Input::get('folder_route','');

            $slash = Config::get('portal.back_slash');

            if ($folder_route != '' && Backend_User::has_folder_access($folder_route) ) {

                $return = array();

                $folder = new Portal($folder_route);


               //get infos
//                $folder_information_array = $folder->get_folder_info_with_size();

                if( $folder_route == 'clients' || $folder_route == 'clients'.$slash.'individual'|| $folder_route == 'clients'.$slash.'corporate'|| $folder_route == 'clients'.$slash.'not-for-profit'
                    || $folder_route == 'clients'.$slash.'charity' ||  $folder_route == 'users'  ) {

                    $folder_information_array = $folder->get_folder_info_without_size();

                }else {

                    $folder_information_array = $folder->get_folder_info_with_size();
                }

                $folder_information['total_files']          = $folder_information_array['total_files'];
                if(isset($folder_information['folder_size']  )) {
                    $folder_information['folder_size']          = Convertor::bytes_to_mb($folder_information_array['folder_size']);
                }

                $folder_information['folder_creation']      = date(Config::get('portal.date_format'),$folder_information_array['folder_creation']);
                $folder_information['folder_modification']  = date(Config::get('portal.date_format'),$folder_information_array['folder_modification']);
                $folder_information['last_files_added']     = array();

                foreach($folder_information_array['last_files_added'] as $lfa) {

                    $folder_information['last_files_added'][] = basename($lfa);

                }//endforeach

            
                //data for view
                $return = array(
//                                  'subfolders'          => $subfolders,
//                                  'files'               => $files,
                                  'folder_information'  => $folder_information
                                  );

              
                //only files
                return  Response::json($return);

            }//if we clicked on folder  - get subfolders and files

             return Response::json( array('error' => 1 ));

        }//if ajax request

    }//POST folder_info




    public function post_add_folder(){

      if(Request::ajax() ){


        if(!Users_Auth::has_access(3) ) return Response::json(array('error' => '1','error_message' => 'Access denied!'));


          /*
          *
          *  Check if folder exists
          *
          */
        $parent_folder = Input::get('folder_route','');


        $slash = Config::get('portal.back_slash');


        $portal_parent_folder = Config::get('portal.portal_parent_folder');


        if($parent_folder == '' || !is_dir($portal_parent_folder.$slash.$parent_folder)  || !Backend_User::has_folder_access($parent_folder)  ) {

            return Response::json(array('error' => '1'));

        }//if the folder doesn't exist



        /*
        * Validate inputs
        *
        */
        $inputs = Input::all();

        array_shift($inputs);

    
       $new_folder = new Backend_Folder();


       if( $new_folder->validate($inputs, Backend_Folder::$validation) )
       {



          if( is_dir($portal_parent_folder.$slash.$parent_folder.$slash.$inputs['folder_name']) ) {

              return Response::json(array('error' => '1','error_message' => 'This folder aready exists.'));

          }else {

          
                 /*
                  *
                  * Create folder
                  *
                  */
                 $folder = new Portal($parent_folder);

                 $folder->create($inputs['folder_name']);


                  /*
                  * Go to add folder with success
                  */
                  return Response::json(array('error' => '0','folder_route' => $parent_folder.$slash.$inputs['folder_name'] ));


            }//else same

        }//if it validates
        else {

          return Response::json(array('error' => '1','error_message' => $new_folder->errors() ));

        }//if it did'n validate



      }//if ajax request

    }//post_add_folder




    public function post_delete_folder() {


      if(Request::ajax() ){


            if(!Users_Auth::has_access(4)) return Response::json(array('error' => '1','error_message' => 'Access Denied!'));


            $folder_route = Input::get('folder_route','');

            $slash = Config::get('portal.back_slash');

            $portal_parent_folder = Config::get('portal.portal_parent_folder');


            if($folder_route == '') {

                return Response::json(array('error' => '1','error_message' => 'No folder selected!'));

            }//if the folder doesn't exist

            if( !is_dir($portal_parent_folder.$slash.$folder_route)  ) {

                return Response::json(array('error' => '1','error_message' => 'Folder doesn\'t exist!'));

            }//if the folder doesn't exist

             if( !Backend_User::has_folder_access($folder_route) ) {

                return Response::json(array('error' => '1','error_message' => 'Access Denied! '));

            }//if the folder doesn't exist



            //
            $folder = new Portal($folder_route);


            /*
            *
            *  Delete Folder
            *
            */
            if( $folder->delete_recursive() ){
 
                return Response::json( array('error' => 0 ));

            }//if the folder was deleted
            else {

                return Response::json( array('error' => 1 ));

            }//if the folder was not deleted



        }//if ajax request


    }// post_delete_folder




    public function get_download_folder() {


            if(!Users_Auth::has_access(5)) return Response::error('404');



            /*
            *
            *  Get inputs
            *
            */
            $folder_route = Input::get('folder_route','');

            if($folder_route == '' || !Backend_User::has_folder_access($folder_route) ) return Response::error('404');


            /*
            *
            *  Check if folder exists
            *
            */
            $folder = new Portal($folder_route);

           

            if ( !$folder->is_dir_empty() ) {

                $to_download = $folder->archive_folder();

                  if($to_download) {

                      return Response::download($to_download);

                  }//if the archive was created

            }//if folder not empty
            else {

                return Redirect::to('safe/portal')->with('folder_empty',true);

            }//if folder empty

            
           


    }// post_download_folder




/**************************** FILES *******************************/



    /*
    *
    *  Upload files to folders
    *
    */
    public function post_upload_files(){


          if (Request::ajax()){

                

                /*
                * Get inputs and check if the logged user has access to the parent folder
                *
                */
                 $inputs = Input::all();

                 $folder_route = $inputs['folder_route'];

                 if($folder_route == '' || !Backend_User::has_folder_access($folder_route) ) return Response::error('404');


                 //get user details
                 $user_id = Backend_User::get_user_id_by_folder_route($folder_route);

              

                if ($user_id) {  

                   $user_info = Backend_User::get_details($user_id);

                   $user_info = $user_info[0];

               }


              
                  /*
                  * Upload files
                  *
                  */
                    if ( isset($inputs['uploaded_files']) && count($inputs['uploaded_files']) != 0 ){


                        $uploaded_files = array();

                        foreach($inputs['uploaded_files'] as $key=>$value) {

                          foreach($value as $v => $val) {

                            $uploaded_files[$v][$key] = $val;

                          }//foreach values


                        }//foreach uploaded_files



                        /*
                         *
                         *  Validation
                         *
                         */
                        $config_size = Config::get('portal.max_upload_size');

                        $extensions  = Config::get('portal.accepted_extensions');

                        $error = FALSE ;
                        $size  = 0;

                         foreach($uploaded_files as $u){

                      
                             if ( !in_array($u['type'], $extensions)  || ( $u['size'] > $config_size )   ){

                                  $error = TRUE;
                               
                              }

                              $size = $size+$u['size'];


                         }//end foreach


                         if($user_id) {

                                /*
                                * We'll see later
                                *
                                */
                                 if ($user_info->user_type == 2 )
                                {
                                    $client_folders_size_permision = Backend_Client_Size::where('id', '=', $user_info->client_size_type_id)->take(1)->only('sizetype');

                                    

                                    $client_folders_size_permision = $client_folders_size_permision*1024*1024;

                                    

                                    $portal = new Portal($folder_route);

                                    $folder_size = $portal->get_folder_info_with_size()['folder_size'];


                                    $client_folder_size = $size+$folder_size;


                                    if($client_folders_size_permision < $client_folder_size){

                                        $error = TRUE;
                                        echo "file_size";
                                        exit;

                                    }
                                }

                        }//if user id


                         if ($error){

                            echo 'error';

                         }//if has errors
                         else {

                                  $return = array();

                                  
                                  $portal_files_route = Config::get('portal.portal_parent_folder');

                                  $slash = Config::get('portal.back_slash');

                                  $path = $portal_files_route.$slash.$folder_route;

                                 


                                  foreach($uploaded_files as $u){


                                        /*
                                        *
                                        *  Check if the file's name is already taken. If taken rename it .
                                        *
                                        */
                                         $file_name = str_replace(array(' ',','),'-',$u['name']);

                                          if ( File::exists( $path.$slash.str_replace(array(' ',','),'-',$u['name']) )) {

                                            $i = 0;

                                            while ( File::exists( $path.$slash.str_replace(array(' ',','),'-',$u['name'])) ) {

                                              $i++;

                                              $u['name'] = $i.'-'.$file_name;

                                            }//while

                                          }//if file exists

                                        


                                        /*
                                        *
                                        *  Upload file
                                        *
                                        */
                                        if( File::move($u['tmp_name'], $path.$slash.str_replace(array(' ',','),'-',$u['name'])) ){

                                            $return[] = $u;

                                        }//if uploaded


                                  }//foreach added image


                                 if( count($return) > 0 && $user_id ) {

                                    if($user_info->user_type == 2 ) {

                                        //update new files variable
                                        Backend_Client_Details::update_new_files($user_id, 1);

                                    }//if client

                                 }//if at least a file was uploaded to a user's folder


                                  /*
                                  *
                                  *  Mail to admin
                                  *
                                  */
                                  $email_from                = Backend_Settings::r('system_email');
                                  $email_name                = Backend_Settings::r('system_name_email');
                                  $alert_notification_email  = Backend_Settings::r('alert_notification_email');


                                  /*
                                  *
                                  *  uploaded files
                                  *
                                  */
                                  $files = '';

                                  foreach($return as $r) {

                                    $files .= $r['name'].', ';

                                  }//foreach files uploaded


                                  $files = substr($files, 0, -2);



                                  if($user_id && $user_info->user_type != 0) {


                                  
                                          /*
                                          *
                                          *  Get name
                                          *
                                          */
                                          if ($user_info->user_type == 2){

                                             /*
                                            *
                                            *  Get_client's name
                                            *
                                            */
                                            if ($user_info->client_type_id == 1 ) {

                                              $name = $user_info->title.' '. $user_info->last_name;

                                            }//individual
                                            else {

                                              $name = $user_info->name;

                                            }//corporate


                                             //Plain
                                            $plain_message = __('common.plain_alert_admin_files_uploaded', array('client' => $name, 'files' => $files, 'folder' => $folder_route ));

                                            //HTML
                                            $html_message  = __('common.html_alert_admin_files_uploaded', array('client' => $name, 'files' => $files, 'folder' => $folder_route ));

                                            $subject = __('common.alert_admin_files_upload_subject', array('client' => $name));


                                          }//if client
                                          else if ($user_info->user_type == 1){

                                            $name  = $user_info->first_name.' '.$user_info->last_name;

                                            //Plain
                                            $plain_message = __('common.plain_alert_admin_files_uploaded_user', array('client' => $name, 'files' => $files, 'folder' => $folder_route ));

                                            //HTML
                                            $html_message  = __('common.html_alert_admin_files_uploaded_user', array('client' => $name, 'files' => $files, 'folder' => $folder_route ));


                                            $subject = __('common.alert_admin_files_upload_subject_user', array('client' => $name));

                                          }//if user





                                          $data_email    = array(
                                                                  'title'   => __('common.html_alert_admin_files_uploaded_title'),
                                                                  'message' => $html_message
                                                               );

                                          $html_message_body = render('backend.email.email', $data_email);




                                  }//if user id

                                  /*
                                  *
                                  *  Return added files
                                  *
                                  */
                                  $to_return = '';

                                  foreach($return as $r) {

                                      $ext_name = explode('.',$r['name']);

                                      $to_return .= '<li file_route="'.$path.$r['name'].'"><span> .'.$ext_name[1].'</span> <br/>'.$ext_name[0].'</li>';

                                  }//end foreach
                                
                                  echo $to_return;


                              }//if no errors

                          }//if there are  images added
                          else{

                            echo 'no-images-added';
                          }




                }//if it's ajax request


    }//POST upload files





    public function post_delete_files() {


      if(Request::ajax() ){

            /*
            *
            *  Get inputs
            *
            */
            $routes      = Input::get('routes',array());

            /*
            *
            *  If no ids were sent sent error
            *
            */
            if ( count($routes) == 0 ) {

               return Response::json( array('error_code' => 1 ));

            }//ids



            /*
            *
            *  Delete Files
            *
            */
            $deleted_files = array();

            $return = '';



            foreach($routes as $r) {

              if($r != '') {

                 if( File::delete($r) ) {

                    $return .= basename($r).', ';

                     $deleted_files[] = $r; 

                 }

                  
              }

            }//foreach routes


            $files  = substr($return, 0, -2);


            /*
            *
            *  Response
            *
            */
            if( count($deleted_files) == 0 ) {

                  return Response::json( array('error_code' => 2 ));

            }//if at least one file was deleted
            elseif(count($deleted_files) < count($routes))
            {

                  return Response::json( array('error_code' => 3, 'deleted_files' => $deleted_files ));

            }//if not all files were deleted




             return Response::json( array('error_code' => 0 , 'deleted_files' => $deleted_files ));

        }//if ajax request


    }//post_delete files
    



  public function get_download_files() {


            if(!Users_Auth::has_access(5)) return Response::error('404');

          


             /*
            *
            *  Get inputs
            *
            */
            $routes      = Input::get('routes','');

            $routes = substr($routes,0,-3);

           


            $routes_array = explode(':|:',$routes);

            // echo "<pre>";
            // print_r($routes_array);
            // echo "</pre>";
            // exit;

            if(count($routes_array) == 1 ) {
              
                return Response::download($routes_array[0]);

            }//is just one file
            else{

                $archive = new Archive();

                $zipname = $archive->archive_files($routes_array);

                return Response::download($zipname);


            }//if more than one file





    }//post_download files



    /*
    * New files alert
    *
    */
     public function get_notify_client($user_id)
    {



      if(Request::ajax() ){




               if($user_id != 0) {

                       $user_info = Backend_User::get_details($user_id);

                       $user_info = $user_info[0];
                             
                        

  
                      /*

                      *
                      *  Mail to client
                      *
                      */

                      $email_from  = Backend_Settings::r('system_email');
                      $email_name  = Backend_Settings::r('system_name_email');
                      $alert_notification_email = Backend_Settings::r('alert_notification_email');

                        /*
                        *
                        *  Get name
                        *
                        */
                        if ($user_info->user_type == 2){

                           /*
                          *
                          *  Get_client's name
                          *
                          */
                          if ($user_info->client_type_id == 1 ) {

                            $title = DB::table('client_individual_titles')->where('id','=',$user_info->title)->only('title');


                            $name = $title.' '. $user_info->last_name;

                          }//individual
                          else {

                            $name = $user_info->name;

                          }//corporate

                        }
            

                     //Plain
                      $plain_message1 = __('common.general_plain_alert_client_files_uploaded', array('client' => $name));

                      //HTML
                      $html_message1  = __('common.general_html_alert_client_files_uploaded');


                      $data_email1    = array(
                                              'title'   => __('common.general_html_alert_client_files_uploaded_title',array('client' => $name)),
                                              'message' => $html_message1
                                           );

                      $html_message_body1 = render('backend.email.email', $data_email1);


                      MailHelper::send($user_info->email, $email_from, $email_name, $plain_message1, $html_message_body1, $email_name,__('common.alert_client_files_upload_subject'));


                      /*
                      * Update new files variables 
                      *
                      */
                      Backend_Client_Details::update_new_files($user_id, 0);


                      return Response::json( array('error' => 0));

               }//if id


               return Response::json( array('error' => 1));

       }

        

    }//post_notify_client



    public function post_refresh_list()
    {



        if(Request::ajax() ){

            /*
            *  Clear cache
            *
            */
            Cache::forget('clients_subfolders');
            Cache::forget('clients_subfolders_info');
            Cache::forget('individuals_subfolders');
            Cache::forget('individuals_subfolders_info');
            Cache::forget('corporate_subfolders');
            Cache::forget('corporate_subfolders_info');
            Cache::forget('not-for-profit_subfolders');
            Cache::forget('not-for-profit_subfolders_info');
            Cache::forget('charity_subfolders');
            Cache::forget('charity_subfolders_info');

        }

    }//post_refresh_list




}//end class