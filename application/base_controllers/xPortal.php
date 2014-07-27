<?php

class xPortal_Controller extends Controller {


    /*
     * All our controllers will be restful
     */
    public $restful = true;



    /**
     * Catch-all method for requests that can't be matched.
     *
     * @param  string    $method
     * @param  array     $parameters
     * @return Response
     */
    public function __call($method, $parameters)
    {
        return Response::error('404');
    }



    public function __construct()
    {

        parent::__construct();
       
        /************************************************
         *
         * Protect from CSRF attacks if it's not AJAX or login route
         *
         *
         ************************************************/
        if( !Request::ajax() & Request::uri() != 'login/safe' )
        {

            $this->filter('before', 'csrf')->on('post');

        }//if it's not AJAX Request


    }//__construct

}//end class