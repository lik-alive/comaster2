<?php
	//Get sections
	function tables_get_sections() {
		$result = dbhandler_get_entities('wp_ab_sections');
		
		return $result;
	}
	
	//Get only active sections (with editors assigned)
	function tables_get_sections_active() {
		global $wpdb;
		$result =  $wpdb->get_results(
		"SELECT *
		FROM wp_ab_sections s 
		WHERE s.ID_Editor IS NOT NULL");
		
		return $result;
	}
	
	//Get issues
	function tables_get_issues() {
		global $wpdb;
		$result =  $wpdb->get_results(
		"SELECT *
		FROM wp_ab_issues i");
		
		return $result;
	}
	
	//Get only active issues (currently in work)	
	function tables_get_issues_active() {
		global $wpdb;
		$result =  $wpdb->get_results(
		"SELECT *
		FROM wp_ab_issues i
		WHERE i.IsActive='Y'
		ORDER BY ID_Issue DESC");
		
		return $result;
	}
	
	//Get verdicts
	function tables_get_verdicts() {
		global $wpdb;
		$result =  $wpdb->get_results(
		"SELECT *
		FROM wp_ab_verdicts v");
		
		return $result;
	}
	