<?php
	//-----Account functions
	
	//Get current user ID
	function g_cui() {
		return wp_get_current_user()->ID;
	}
	
	//Get current user name
	function g_cun() {
		return wp_get_current_user()->user_login;
	}
	
	//Get current user section id
	function g_cusi() {
		$username = g_cun();
	
		if ($username == 'doeditor') return 1;
		else if ($username == 'oieditor' || $username == 'oideditor' || $username == 'oieditorm') return 2;
		else if ($username == 'cmeditor') return 3;
		return 777;
	}
	
	//Get current user section cond
	function g_cusc() {
		$ID_Section = g_cusi();
		if ($ID_Section === 777) return "";
		else return " AND a.ID_Section = {$ID_Section} ";
	}
	
	//Check current user access among the list
	function g_cua(...$users){
		foreach ($users as $user) {
			if (current_user_can($user)) return true;
		}
		return false;
	}
	
	//-----Logging
	
	//Log event
	function g_lev($event, $function, $extra = ''){
		$extevent = $event;
		if ($extra !== '') $extevent =  $event.': '.$extra;
		g_log(1, $extevent, $function);
		return array(1, $event);
	}
	
	//Log event and return JSON result
	function g_lev_j($event, $function, $extra = '') {
		return g_ctj(g_lev($event, $function, $extra));
	}
	
	//Log error
	function g_ler($event, $function, $extra = ''){
		$extevent = $event;
		if ($extra !== '') $extevent =  $event.': '.$extra;
		g_log(2, $extevent, $function);
		return array(2, $event);
	}
	
	//Log error and return JSON result
	function g_ler_j($event, $function, $extra = '') {
		return g_ctj(g_ler($event, $function, $extra));
	}
	
	//Save event to logs
	function g_log($status, $event, $function){
		$msg = array(
			'Event' => $event.' '.$function.'()',
			'Status' => $status,
			'DateTime' => date('Y-m-d H:i:s'),
			'ID_User' =>  wp_get_current_user()->ID
		);
		
		try {
			dbhandler_add_entity_TH('wp_ab_logs', $msg);
		} catch (Exception $e) { }
	}
	
	//-----String functions
	
	//Secure input
	function g_si($data) {
		if (is_null($data)) return null;
	
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
		return $data;
	}
	
	//Nullify string data
	function g_nsd($data) {
		return ($data == '') ? null : $data;
	}
	
	//Check if string is in english
	function g_ie($data) {
		return preg_match('/^[^а-яА-я]*$/', $data);
	}
	
	//Change keyboard layout
	function g_ckl($data) {
		$str_rus = array(
			"й","ц","у","к","е","н","г","ш","щ","з","х","ъ",
			"ф","ы","в","а","п","р","о","л","д","ж","э",
			"я","ч","с","м","и","т","ь","б","ю","ё",
			"Й","Ц","У","К","Е","Н","Г","Ш","Щ","З","Х","Ъ",
			"Ф","Ы","В","А","П","Р","О","Л","Д","Ж","Э",
			"Я","Ч","С","М","И","Т","Ь","Б","Ю","Ё"
		);
		$str_eng = array(
			"q","w","e","r","t","y","u","i","o","p","[","]",
			"a","s","d","f","g","h","j","k","l",";","'",
			"z","x","c","v","b","n","m",",",".","`",
			"Q","W","E","R","T","Y","U","I","O","P","[","]",
			"A","S","D","F","G","H","J","K","L",";","'",
			"Z","X","C","V","B","N","M",",",".","~"
		);
		if (g_ie($data)) $revert = str_replace($str_eng, $str_rus, $data);
		else $revert = str_replace($str_rus, $str_eng, $data);
		return $revert;
	}
	
	//-----Other functions
	
	//Convert to json
	function g_ctj($data) {
		$rowscount = sizeof($data);
		$json_data = array(
			"draw"            => intval( $_REQUEST['draw'] ), 
			"recordsTotal"    => intval( $rowscount ),
			"recordsFiltered" => intval( $rowscount ), 
			"data"            => $data,
		);
		return json_encode($json_data);
	}
	
	//Custom exception with message for users
	class PublicException extends Exception { }