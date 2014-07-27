<?php

class Tools{


	/**
	 * 
	 * @return type string (date)
	 * 
	 * Returns date plus/minus selected days
	 */
	public static function date_plus_minus($date = '', $plus_minus = '+', $days = 30){

		return date('Y-m-d', strtotime($date . "{$plus_minus} {$days} day" ) );

	}//date_plus_minus




	/**
	 * @param array $titles = [title => name]
	 * the title is the text that will appear in th
	 * name is the field from the database that you sort
	 * 
	 * @param array append_get = additional elements to add to the link, 
	 * 							 like pagination or a start date
	 * 
	 * Returns a th's for thead, must be inside a <tr>
	 */
    public static function sortable_table_head($titles, $order_by, $order_by_direction, $link, $append_get = array() ){

        $count = 0;

        foreach( $titles as $t => $n ){

            //default
            $order_direction = 'asc';
            $th_class = '';

            /**
             * Additional GET
             */

            $additional = '';

            foreach( $append_get as $key => $value ){

                $additional .= "&{$key}={$value}";

            }//foreach additional element


            if( $n == $order_by ){

                /**
                 * Set selected th class for
                 * this element
                 */
                $th_class = 'table-head-active';



                /**
                 * Establish arrows and order direction
                 */

                if( $order_by_direction == 'asc' ){

                    $order_direction = 'desc';

                    //arrows
                    $arrows = '<div class="arrow-up arrow-up-active"></div>';
                    $arrows .= '<div class="arrow-down"></div>';

                }//if current order_direction is asc
                else{

                    $arrows  = '<div class="arrow-up"></div>';
                    $arrows .= '<div class="arrow-down arrow-down-active"></div>';

                }//else order_by_direction == desc


            }//if n = order_by
            elseif( $n == '!' )
            {

                $arrows = '';

            }//if we don't need arrows
            else{

                $arrows = '<div class="arrow-up"></div>
							 <div class="arrow-down"></div>';

            }//else current element is not the one selected

            ?>


            <!--            <th><a href=''><span>{{ __('first_name') }}</span><div class='arrow-container'><div class='arrow-up'></div><div class='arrow-down'></div></div></a></th>-->

            <th class=" <?php echo $th_class; ?>">

                <a href="<?php echo URL::base() . '/' . $link ?>
						?order_by=<?php echo $n ?>
						&order_direction=<?php echo $order_direction; ?>
						<?php echo $additional; ?> ">

                    <span><?php echo __('common.'.$t); ?></span>

                    <div class="arrow-container">

                        <?php echo $arrows; ?>

                    </div><!-- Sorth-arrow -->


                </a>

            </th>

            <?php

            $count = 1;

        }//foreach titles

    }//sortable_table_head



	public static function formatdate($date){

		if ($date == '0000-00-00') {

			return '-';
		}

		return date(Config::get('portal.date_format'), strtotime($date));

	}//formatdate



	public static function check_order_by($order_by, $list){


        if( in_array($order_by, $list ) ){
               
               return $order_by;
               
           }//if in array order by
           else{
               
              return $list[0];

           }//else ! in array order_by we return the first element


	}//check_order_by



	public static function check_order_direction($order_direction){


		$order_list = array('asc', 'desc');

    
        	if( in_array($order_direction, $order_list ) ){
               
               return $order_direction;
               
            }//if in array order
            else{
               
              return 'asc';
               
            }//else ! in array order we return asc as default

	}//check_order_direction



	public static function format_date_time($date_time){


		return date(Config::get('bks.date_time_'.Session::get('user.language_abb')), strtotime($date_time));

	}//format_date_time($date_time)




	public static function get_timezones(){

		$zones = timezone_identifiers_list();
        
		foreach ($zones as $zone){

		    $zone = explode('/', $zone); // 0 => Continent, 1 => City
		    
		    // Only use "friendly" continent names
		    if ($zone[0] == 'Africa' || $zone[0] == 'America' || $zone[0] == 'Antarctica' || $zone[0] == 'Arctic' || $zone[0] == 'Asia' || $zone[0] == 'Atlantic' || $zone[0] == 'Australia' || $zone[0] == 'Europe' || $zone[0] == 'Indian' || $zone[0] == 'Pacific')
            {

		        if (isset($zone[1]) != ''){

		            $locations[$zone[0]][$zone[0]. '/' . $zone[1]] = str_replace('_', ' ', $zone[1]); // Creates array(DateTimeZone => 'Friendly name')

		        } //if isset zone

		    }//if zone is correct

		}//end foreach

		return $locations;

	}//get_timezones


    /*
    *
    *  Removes all files from a directory
    *
    */
    public static function clean_dir($path) {

        $files = glob($path . '*');

            foreach($files as $file){ // iterate files

                if(is_file($file))

                  unlink($file); // delete file

            }//

    }//clean dir



}//end class tools