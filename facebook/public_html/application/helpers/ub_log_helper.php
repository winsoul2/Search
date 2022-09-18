<?php
function writeToLog($message, $logfile, $split_file = false) {
	if($split_file && date("d") == "01") {
		$path_parts = pathinfo($logfile);
		$new_file = $path_parts["dirname"]."/".$path_parts["filename"]."-".date("Ymd").".".$path_parts["extension"];
		if(!file_exists($new_file)) {
			rename($logfile, $new_file);
		}
	}
	
	if($fp = fopen($logfile, 'a+')) {
		fwrite($fp, date('c') . ' ' . $message . PHP_EOL);
		fclose($fp);
	}
}