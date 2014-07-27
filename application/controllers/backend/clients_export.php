<?php

class Backend_Clients_Export_Controller extends xPortal_Controller
{


    public function get_export() {

        $selected_clients = Input::get('clients','');

        $client_type      = Input::get('client_type',0);

        $selected_clients = explode(',', $selected_clients);

     
        $clients = Backend_Client::get_clients_to_export($selected_clients, $client_type);


        $delimiter = ','; 


       if(Input::get('separate_files') == 1) {

            $csv_individuals = "Client Type".$delimiter.

                "Calendar Year Start".$delimiter.
                "Title".$delimiter. 
                "First Name".$delimiter.
                "Initials".$delimiter.
                "Last Name".$delimiter.
                "Default Client Name".$delimiter.
                "Birth Date".$delimiter.
                "Marital Status".$delimiter. 

                "Street Address".$delimiter.
                "City".$delimiter.
                "Country".$delimiter.

                "Phone".$delimiter.
                "Fax".$delimiter.

                "Email".$delimiter.
                "Username".$delimiter.
                "Max Size Folders Upload".
                "\r\n";


            $csv_corporate = "Client Type".$delimiter.

                "Name".$delimiter.
                "Calendar Year Start".$delimiter. 

                "Contact Person First Name".$delimiter.
                "Contact Person Last Name".$delimiter.
                "Position".$delimiter.

                "Street Address".$delimiter.
                "City".$delimiter.
                "Country".$delimiter.

                "Phone".$delimiter.
                "Fax".$delimiter.

                "Email".$delimiter.
                "Username".$delimiter.
                "Max Size Folders Upload".
                "\r\n";

             foreach( $clients as $c )
             {

                if( $c->client_type_id == 1 ) {

                    $csv_individuals .= str_replace($delimiter,' ',$c->client_type) . $delimiter;

                    $csv_individuals .= str_replace($delimiter,' ',$c->calendar_year_start) . $delimiter;
                    $csv_individuals .= str_replace($delimiter,' ',$c->title) . $delimiter;
                    $csv_individuals .= str_replace($delimiter,' ',$c->first_name) . $delimiter;
                    $csv_individuals .= str_replace($delimiter,' ',$c->initials) . $delimiter;
                    $csv_individuals .= str_replace($delimiter,' ',$c->last_name) . $delimiter;
                    $csv_individuals .= str_replace($delimiter,' ',$c->default_client_name) . $delimiter;
                    $csv_individuals .= str_replace($delimiter,' ',$c->birth_date) . $delimiter;
                    $csv_individuals .= str_replace($delimiter,' ',$c->marital_status) . $delimiter;

                    $csv_individuals .= str_replace($delimiter,' ',$c->street_address) . $delimiter;
                    $csv_individuals .= str_replace($delimiter,' ',$c->city) . $delimiter;
                    $csv_individuals .= str_replace($delimiter,' ',$c->country) . $delimiter;

                    $csv_individuals .= str_replace($delimiter,' ',$c->phone). $delimiter;
                    $csv_individuals .= str_replace($delimiter,' ',$c->fax) . $delimiter;

                    $csv_individuals .= str_replace($delimiter,' ',$c->email) . $delimiter;
                    $csv_individuals .= str_replace($delimiter,' ',$c->username) . $delimiter;
                    $csv_individuals .= str_replace($delimiter,' ',$c->sizetype) ;

                    $csv_individuals .= "\r\n";

             }else {

                    $csv_corporate .= str_replace($delimiter,' ',$c->client_type) . $delimiter;

                    $csv_corporate .= str_replace($delimiter,' ',$c->corporation_name) . $delimiter;
                    $csv_corporate .= str_replace($delimiter,' ',$c->corporation_calendar_year_start) . $delimiter;
                    $csv_corporate .= str_replace($delimiter,' ',$c->contact_person_first_name) . $delimiter;
                    $csv_corporate .= str_replace($delimiter,' ',$c->contact_person_last_name). $delimiter;
                    $csv_corporate .= str_replace($delimiter,' ',$c->contact_person_position) . $delimiter;

                    $csv_corporate .= str_replace($delimiter,' ',$c->street_address) . $delimiter;
                    $csv_corporate .= str_replace($delimiter,' ',$c->city) . $delimiter;
                    $csv_corporate .= str_replace($delimiter,' ',$c->country) . $delimiter;

                    $csv_corporate .= str_replace($delimiter,' ',$c->phone) . $delimiter;
                    $csv_corporate .= str_replace($delimiter,' ',$c->fax) . $delimiter;

                    $csv_corporate .= str_replace($delimiter,' ',$c->email) . $delimiter;
                    $csv_corporate .= str_replace($delimiter,' ',$c->username) . $delimiter;
                    $csv_corporate .= str_replace($delimiter,' ',$c->sizetype) ;

                    $csv_corporate .= "\r\n";

             }
                

            }//foreach clients 

            $portal_archives = Config::get('portal.import_folder');

            $slash_obj = new SystemTools;

            $slash = $slash_obj->backslash();

            $date = date('Y-m-d_H-i-s');


            $handle = fopen($portal_archives.$slash.'clients_individuals.csv',"w");

            $write = fwrite($handle, $csv_individuals);

            if($write) {
                fclose($handle);
            }
            else {

                die('Export failed');
          }

        

            $handles = fopen($portal_archives.$slash.'clients_corporate.csv',"w");

            $write = fwrite($handles, $csv_corporate);

            if($write) {

                fclose($handles);
            }
            else {

                die('Export failed');
            }


            /*
            * Add files to zip
            *
            */

            $zip = new ZipArchive();

            $res = $zip->open($portal_archives.$slash.'clients_'.$date.'.zip', ZipArchive::CREATE);

            if ($res === TRUE) {

                $zip->addFile($portal_archives.$slash.'clients_individuals.csv', 'clients_individuals.csv');

                $zip->addFile($portal_archives.$slash.'clients_corporate.csv', 'clients_corporate.csv');

                $zip->close();


                unlink($portal_archives.$slash.'clients_corporate.csv');

                unlink($portal_archives.$slash.'clients_individuals.csv');

                return Response::download($portal_archives.$slash.'clients_'.$date.'.zip');

            } else {
                echo 'failed, code:' . $res;
            }


       }else {

        /* create export files - individuals and corporations */
     

        $csv = "Client Type".$delimiter.

                "Calendar Year Start".$delimiter.
                "Title".$delimiter. 
                "First Name".$delimiter.
                "Initials".$delimiter.
                "Last Name".$delimiter.
                "Default Client Name".$delimiter.
                "Birth Date".$delimiter.
                "Marital Status".$delimiter. 

                "Name".$delimiter.
                "Corporation Calendar Year Start".$delimiter. 
                "Contact person".$delimiter.
                "Position".$delimiter.

                "Street Address".$delimiter.
                "City".$delimiter.
                "Country".$delimiter.
                "Province".$delimiter.
                "State".$delimiter.
                "Phone number".$delimiter.
                "Fax".$delimiter.

                "Email".$delimiter.
                "Username".$delimiter.
                "Max Size Folders Upload".
                "\r\n";

         

        foreach( $clients as $c )
        {


            $csv .= str_replace($delimiter,' ',$c->client_type) . $delimiter;

            $csv .= str_replace($delimiter,' ',$c->calendar_year_start) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->title) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->first_name) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->initials) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->last_name) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->default_client_name) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->birth_date) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->marital_status) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->corporation_name) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->corporation_calendar_year_start) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->contact_person_last_name.' '.$c->contact_person_first_name) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->contact_person_position) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->street_address) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->city) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->country) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->province) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->state) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->phone) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->fax) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->email) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->username) . $delimiter;
            $csv .= str_replace($delimiter,' ',$c->sizetype) ;
            


            //Add new line
            $csv .= "\r\n";

        }//foreach 


        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=clients.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $csv;


         }//one file

    }//get_export


}//end class