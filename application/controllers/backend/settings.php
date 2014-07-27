<?php

class Backend_Settings_Controller extends xPortal_Controller
{


    public function get_index()
    {

        /*
         * View data
         */
        $data = array(
            'selected_page' => 'settings',
            'breadcrumbs'   => array(
                'last'                  => __('common.settings')
            ),

                'settings'  => $settings = Backend_Settings::all()
        );

          return View::make('backend.settings.edit',$data);


    }//GET index
    

    public function post_index()
    {

        $settings = Backend_Settings::all();


      

        /*
         * Validate data
         */
        $inputs = Input::get();

        
        unset( $inputs['csrf_token'] );

        $setting = new Backend_Settings();


        if( $setting->validate($inputs,  Backend_Settings::validation_rules() ) )
        {


            foreach($inputs as $key => $value)
            {


                Backend_Settings::where('name','=',$key)
                                ->update(array(
                                                 'value'=> $value
                    ));


            }//Update Settings


            /*
             * Go to settings
             */
            return Redirect::to('safe/settings')->with('updated', true);

        }//if it validated


        /*
         * return with errors
         */
        return Redirect::to('safe/settings')->with_errors($setting->errors())->with_input();

    }//POST index

}//end class