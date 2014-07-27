<?php

return array(

    'date_format'           => 'F j, Y',


    'accepted_extensions'   => array('image/jpeg','image/jpg','image/png','image/gif',
                                                              'application/pdf',
                                                              'application/msword',
                                                              'text/csv',
                                                              'application/vnd.ms-excel',
                                                              'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                                              'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),

    'max_upload_size'       => 20971520,//in bytes

    // 'back_slash'            => "\\",

        'back_slash'           => '/',

    

    // 'portal_parent_folder'  => 'C:\\wamp\\www\\portal\\Portal-Files',

    'portal_parent_folder'  => '/home/andrei/www/xportal/Portal-Files', 

    // 'archives_folder'       => 'C:\\wamp\\www\\portal\\portal_archives',

    'archives_folder'       => '/home/andrei/www/xportal/portal_archives',

    // 'import_folder'         => 'C:\\wamp\\www\\portal\\imported_files',

    'import_folder'         => '/home/andrei/www/xportal/imported_files',



    /*
     * Folders structure
     */
    'corporate'       =>  array(  
                                'first_level'  => array('Corporate'), 
                                'second_level' => array(
                                                          'Corporate Tax Return',
                                                          'Correspondens',
                                                          'Financial Statements',
                                                          'Notice of Assessment',
                                                          'Supporting Documents'
                                                         ) 
                                ) ,

                           

    'individual'    => array(  
                                'first_level'  => array('Individual'), 
                                'second_level' => array(
                                                                'Personal Tax Return',
                                                                'Supporting Documents',
                                                                'Notice of Assessment',
                                                                'Notice of Reassessment'
                                                                
                                                            ),
                                ) ,




);