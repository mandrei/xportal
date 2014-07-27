<?php
/**
 * Users_Security - used to handle security for authentication
 *
 * Used DB tables:
 *
 *  login_attempts
 *  login_blocked_ips
 *
 */

class Users_Security
{


    public static $login_attempts = 'login_attempts';

    public static $login_blocked_ips = 'login_blocked_ips';

/*
 * Used to check if an IP is blocked
 */
    public static function is_ip_blocked()
    {

        /*
         *
         * IPs are blocked for 24h
         *
         * First we check if there are IPs that have timestamp less than 24h ago current timestamp - 86400 (seconds in a day)
         * if so we remove the block.
         *
         * It's easier to check at each request than to setup a cron just for this
         *
         */
        $timestamp_24h_ago = time() - 86400;

        DB::table(self::$login_blocked_ips)->where('timestamp', '<', $timestamp_24h_ago)->delete();


        /*
         *
         * Now check if the current user is blocked
         *
         *
         */
        $ip = Request::ip();

        if( DB::table(self::$login_blocked_ips)->where('ip', '=', $ip)->count() > 0 )
        {

            //TO DO: Log this action


            return true;

        }//if the ip is blocked


            return false;

    }//is_ip_blocked




    public static function check_login_attempts()
    {

        $ip = Request::ip();

        /*
         * First we delete entries older that 30 minutes ago
         *
         * It's easier to check at each request than to setup a cron just for this
         */
        $timestamp_30_min_ago = strtotime('-30 minutes');

 
        DB::table(self::$login_attempts)->where('timestamp', '<', $timestamp_30_min_ago)->delete();

        /*
         *
         * Add another attempts
         *
         */
        DB::table(self::$login_attempts)->insert(array(
            'ip'              => $ip,
            'timestamp'       => time()
        ));


        /*
         *
         * Check no of attempts
         *
         * if it's 10 block the IP
         *
         */
        if( ( DB::table(self::$login_attempts)->where('ip', '=', $ip)->count() ) >= 10 )
        {

            DB::table(self::$login_blocked_ips)->insert(array(
                'ip'         => $ip,
                'timestamp'  => time()
            ));

        }//if there are 10 attempts to login


    }//check_login_attempts


    }//end w