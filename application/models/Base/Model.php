<?php

class Base_Model extends Eloquent{

    //otherwise when updating it tries to update some timestamps
    public static $timestamps = false;


    /*
     * Validation errors go here
     */
    protected $errors;




    public function validate($data, $rules)
    {

        // make a new validator object
        $v = Validator::make($data, $rules);

        // check for failure
        if ($v->fails())
        {

            // set errors
            $this->errors = $v->errors;

            return false;


        }//if validation fails


        // validation pass
        return true;


    }//validate



    /*
     * Used when validating to return the errors @ validation
     */
    public function errors()
    {

        return $this->errors;

    }//errors



}//end class