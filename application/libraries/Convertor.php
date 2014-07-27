<?php

class Convertor {


	public static function bytes_to_mb($bytes, $precision = 2){


		// $base = log($bytes) / log(1024);



  //   	$suffixes = array('', 'k', 'M', 'G', 'T');   


  //   	return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)] . ' MB';

		return bcdiv($bytes, 1048576, $precision).' MB';

	}//bytes_to_mb



}//end class Files