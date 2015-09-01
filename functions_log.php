<?php
if (! function_exists ( 'write_log' )) {
	add_option ( "write_log_to" , "dev@shangrila.farm");
	add_option ( "write_log_subject" , "[NSTLOG]");
	
	function write_log($log, $send_mail = false, $level = "INFO") {
		if (is_array ( $log ) || is_object ( $log )) {
			$log = json_encode($log);
		} else {
			$log = json_encode( array( "log" => "" . $log ) );
		}
		
		$ip = $_SERVER['REMOTE_ADDR'];
		if ( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
			$ip .= "/" . $_SERVER['HTTP_X_FORWARDED_FOR'];	
		}
		$request_uri = $_SERVER['REQUEST_URI'];
		$request_method = $_SERVER['REQUEST_METHOD'];
		$unique_id = $_SERVER['UNIQUE_ID'];

		$db = debug_backtrace ();
		$db = $db [0];

		$subject = $db ["file"];
		$pattern = '/(.+\/)(.*.php)/';
		$matches = array (1);
		preg_match ( $pattern, $subject, $matches );

		$log_message = "[$level] $unique_id $ip $request_method $request_uri" . $matches [2] . " " . $db ["line"] . ": " . $log;
		
		error_log ( $log_message );
		
		if ( $send_mail ) {
			wp_mail ( get_option ( "write_log_to" ),
					get_option ( "write_log_subject" ),
					$log_message );
		}
	}
}
?>
