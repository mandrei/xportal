<?php


/**
 * Class SystemTools
 *
 * Handles multiple system action, e.g. get the operating system if the server
 *
 */
class SystemTools {


    /**
     *
     * @return bool
     */
    public function is_env_windows()
    {

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        {
            return true;

        }//if is linux return false

            return false;


    }//is_env_windows


    /**
     * @return bool
     */
    public function is_env_linux()
    {

        if (PHP_OS === 'Linux')
        {
            return true;

        }//if its linux return true

        return false;

    }//is_env_linux


    /**
     * @return string the backslash used by the OS ( / or \\ )
     */
    public function backslash()
    {

        if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        {

            return '\\';

        }
        elseif(PHP_OS === 'Linux')
        {

            return '/';


        }//else if is linux

      return false;

    }//backslash


}//end class
