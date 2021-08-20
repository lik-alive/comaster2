<?php
	//-----General DB functions
	
	//Get the last error from DB
	function dbhandler_get_last_error(){
		global $wpdb;
		return $wpdb->last_error;
	}
	
	//-----Table DB functions
	
	//Add entity to the specified table
	function dbhandler_add_entity_TH($tablename, $entity) {
		//Convert object to array
		if (is_object($entity)) $entity = get_object_vars($entity);
		
		global $wpdb;
		$result = $wpdb->insert($tablename, $entity);
		
		if (false === $result) throw new Exception("Ошибка добавления записи в таблицу {$tablename} ".dbhandler_get_last_error());
		
		return $wpdb->insert_id;
	}
	
	//Update entity from the specified table by id
	function dbhandler_set_entity_TH($tablename, $entity, $idname, $id) {
		//Convert object to array
		if (is_object($entity)) $entity = get_object_vars($entity);
		
		global $wpdb;
		$result = $wpdb->update($tablename, $entity, array ($idname => $id));
		
		if (false === $result) throw new Exception("Ошибка обновления записи {$id} в таблице {$tablename} ".dbhandler_get_last_error());
		
		return $result;
	}
	
	//Delete entity from the specified table by id
	function dbhandler_delete_entity_TH($tablename, $idname, $id) {
		global $wpdb;
		$result = $wpdb->delete($tablename, array ($idname => $id));
		
		if (false === $result) throw new Exception("Ошибка удаления записи {$id} в таблице {$tablename} ".dbhandler_get_last_error());
		
		return $result;
	}
	
	//Get all entities from the specified table
	function dbhandler_get_entities($tablename) {
		global $wpdb;
		$result =  $wpdb->get_results(
		"SELECT *
		FROM {$tablename} t");
			
		return $result;
	}
	
	//Get single entity from the specified table by id
	function dbhandler_get_entity($tablename, $idname, $id) {
		global $wpdb;						
		$result =  $wpdb->get_row(
		"SELECT *
		FROM {$tablename} t
		WHERE t.{$idname} = {$id}");
			
		return $result;
	}
	
	//-----Personal Table Functions
	
	//Get last 20 log messages
	add_action('wp_ajax_dbhandler_get_20logs_json', 'dbhandler_get_20logs_json');
	function dbhandler_get_20logs_json() {
		global $wpdb;						
		$result =  $wpdb->get_results(
		"SELECT l.*, wu.display_name
		FROM wp_ab_logs l INNER JOIN wp_users wu ON l.ID_User=wu.ID
		ORDER BY DateTime DESC 
		LIMIT 20");
			
		echo g_ctj($result);
		exit();
	}
	
	//Get last 20 chat messages
	add_action('wp_ajax_dbhandler_get_20chats_json', 'dbhandler_get_20chats_json');
	function dbhandler_get_20chats_json() {
		global $wpdb;						
		$result =  $wpdb->get_results(
		"SELECT c.*, wu.display_name
		FROM wp_ab_chats c INNER JOIN wp_users wu ON c.ID_User=wu.ID
		ORDER BY DateTime DESC 
		LIMIT 20");
			
		echo g_ctj($result);
		exit();
	}
	