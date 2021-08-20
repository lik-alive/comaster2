<?php
	function expertsquery($sels = ''){
		$query = 
		"SELECT 
			e.*,
			e1.ActiveCount,			
			e2.TotalCount,
			e3.AvgDays {$sels}
		FROM 
			wp_ab_experts e
				LEFT JOIN 
				(SELECT r.ID_Expert, COUNT(r.ID_Article) as ActiveCount
				FROM 
					(SELECT r.ID_Expert, r.ID_Article
					FROM wp_ab_reviews r 
						INNER JOIN wp_ab_articles a ON r.ID_Article=a.ID_Article
						INNER JOIN wp_ab_issues i ON a.ID_Issue=i.ID_Issue
						INNER JOIN (SELECT r.ID_Article, MAX(r.RevNo) as MaxRevNo
							FROM wp_ab_reviews r
							GROUP BY r.ID_Expert, r.ID_Article) r1 ON r.ID_Article=r1.ID_Article AND r.RevNo=r1.MaxRevNo
					WHERE (r.ID_Verdict IS NULL OR (r.ID_Verdict>1 AND r.ID_Verdict<5)) AND i.IsActive='Y'
					GROUP BY r.ID_Expert, r.ID_Article) r
				GROUP BY r.ID_Expert) e1 ON e.ID_Expert=e1.ID_Expert
				
				LEFT JOIN
				(SELECT r.ID_Expert, COUNT(r.ID_Article) as TotalCount
				FROM 
					(SELECT r.ID_Expert, r.ID_Article
					FROM wp_ab_reviews r INNER JOIN wp_ab_articles a ON r.ID_Article=a.ID_Article
					WHERE r.ID_Verdict<5
					GROUP BY r.ID_Expert, r.ID_Article) r
				GROUP BY r.ID_Expert) e2 ON e.ID_Expert=e2.ID_Expert
				
				LEFT JOIN
				(SELECT r.ID_Expert, AVG(DATEDIFF(r.FromExpDate, r.ToExpDate)) as AvgDays
				FROM wp_ab_reviews r
				WHERE r.ToExpDate IS NOT NULL AND r.FromExpDate IS NOT NULL AND r.ID_Verdict<5
				GROUP BY r.ID_Expert) e3 ON e.ID_Expert=e3.ID_Expert";
		return $query;
	}
	
	//Get all experts
	add_action('wp_ajax_experts_get_json', 'experts_get_json');	
	function experts_get_json(){
		global $wpdb;						
		$result =  $wpdb->get_results(expertsquery());
		
		echo g_ctj($result);
		exit();
	}
	
	//Get expert info
	add_action('wp_ajax_experts_get_expert_json', 'experts_get_expert_json');	
	function experts_get_expert_json() {
		$ID_Expert = g_si($_GET['id']);
		
		global $wpdb;						
		$result =  $wpdb->get_row(expertsquery()." WHERE e.ID_Expert={$ID_Expert}");
		
		echo g_ctj($result);
		exit();
	}
	
	//Search expert info by name or email
	add_action('wp_ajax_experts_search_json', 'experts_search_json');	
	function experts_search_json() {
		$search = g_si($_GET['kw']);
		$searchI = g_ckl($search);
		
		//Extract mail from NAME <MAIL> notation
		if (mb_strpos($search, '<') !== false) $search = mb_substr($search, mb_strpos($search, '<') + 1);
		if (mb_strpos($search, '>') !== false) $search = mb_substr($search, 0, mb_strpos($search, '>'));
		
		
		global $wpdb;						
		$result =  $wpdb->get_results(expertsquery()." 
			WHERE 
				e.Name LIKE '%{$search}%' OR e.Mail LIKE '%{$search}%' 
				OR e.Name LIKE '%{$searchI}%' OR e.Mail LIKE '%{$searchI}%' 
			ORDER BY e2.TotalCount DESC");
		
		echo g_ctj($result);
		exit();
	}
	
	//Advanced search expert info by name or email including recommendation from previous reviews
	add_action('wp_ajax_experts_search_advanced_json', 'experts_search_advanced_json');	
	function experts_search_advanced_json() {
		$search = g_si(g_nsd($_GET['kw']));
		$searchI = g_ckl($search);
		$ID_Article = g_si($_GET['id']);
		
		$result = dbhandler_get_entity('wp_ab_articles', 'ID_Article', $ID_Article);
		if (!$result) {
			echo g_ctj(null);			
			exit();
		}
		$ID_CorAuthor = $result->ID_CorAuthor;
		
		$sels = ', MAX(e4.PrevExp) as PrevExp';
		$cond = '';
		if (!is_null($search)) {
			$cond = " OR e.Name LIKE '%{$search}%' OR e.Interests LIKE '%{$search}%' 
						OR e4.ATitle LIKE '%{$search}%'  
					OR e.Name LIKE '%{$searchI}%' OR e.Interests LIKE '%{$searchI}%' 
						OR e4.ATitle LIKE '%{$searchI}%' ";
		}
		
		global $wpdb;						
		$result =  $wpdb->get_results(expertsquery($sels)." 
				LEFT JOIN (
                    SELECT r.ID_Expert, a.Title as ATitle, (a.ID_CorAuthor={$ID_CorAuthor}) as PrevExp
                    FROM wp_ab_reviews r INNER JOIN wp_ab_articles a ON r.ID_Article=a.ID_Article
                    WHERE r.ID_Verdict<5) e4 ON e.ID_Expert=e4.ID_Expert
			WHERE (e4.PrevExp=1 {$cond}) AND e.IsActive='Y' AND e.ID_Expert NOT IN (
				SELECT r.ID_Expert
				FROM wp_ab_reviews r
				WHERE r.ID_Article={$ID_Article}
			)
			GROUP BY e.ID_Expert 
			ORDER BY e2.TotalCount DESC, e1.ActiveCount DESC");
		
		echo g_ctj($result);
		exit();
	}
	
	//Get expert from POST-request data
	function getExpertFromPOST() {
		$expert = array(
			'ID_Expert' => $_POST['ID_Expert'],
			'Name' => $_POST['Name'],
			'CallName' => $_POST['CallName'],
			'Mail' => $_POST['Mail'],
			'Language' => $_POST['Language'],
			'IsActive' => g_nsd($_POST['IsActive']),
			'Interests' => $_POST['Interests'],
			'Position' => $_POST['Position'],
			'Phone' => $_POST['Phone'],
			'Comments' =>  $_POST['Comments']
		);
		//Remove all null-values (unexisted in POST-request)
		foreach (array_keys($expert, null, true) as $key) {
			unset($expert[$key]);
		}
		//Trim all strings
		foreach ($expert as $key => $val) {
			$expert[$key] = trim($val);
		}
		//Change all '' values to null
		foreach (array_keys($expert, '', true) as $key) {
			$expert[$key] = null;
		}
		
		return $expert;
	}
	
	//Create an expert
	add_action('wp_ajax_experts_create_json', 'experts_create_json');	
	function experts_create_json() {
		$expert = getExpertFromPOST();
		
		try {
			$ID_Expert = dbhandler_add_entity_TH('wp_ab_experts', $expert);
			g_lev('Эксперт добавлен', __FUNCTION__, $ID_Expert);
			echo g_ctj(array(1, '', $ID_Expert));
		} catch(Exception $e) {
			echo g_ler_j('Ошибка добавления эксперта', __FUNCTION__, $e->getMessage());
		}
		exit();
	}
	
	//Edit the expert
	add_action('wp_ajax_experts_edit_json', 'experts_edit_json');	
	function experts_edit_json() {
		$expert = getExpertFromPOST();
		
		try {
			dbhandler_set_entity_TH('wp_ab_experts', $expert, 'ID_Expert', $expert['ID_Expert']);
			g_lev('Эксперт обновлён', __FUNCTION__, $expert['ID_Expert']);
			echo g_ctj(array(1, '', $expert['ID_Expert']));
		} catch (Exception $e) {
			echo g_ler_j('Ошибка обновления эксперта', __FUNCTION__, $e->getMessage());
		}
		exit();
	}
	
	//Get expert's articles
	add_action('wp_ajax_experts_get_expert_articles_json', 'experts_get_expert_articles_json');	
	function experts_get_expert_articles_json() {
		$ID_Expert = g_si($_GET['id']);
		
		global $wpdb;						
		$result =  $wpdb->get_results(
		"SELECT 
			r.*, 
			r1.MaxRevNo,
			a.Authors as AAuthors, a.Title as ATitle,
			i.IsActive as IIsActive,
			v.Title as VTitle
		FROM wp_ab_reviews r 
			LEFT JOIN wp_ab_verdicts v ON r.ID_Verdict=v.ID_Verdict
			INNER JOIN wp_ab_articles a ON r.ID_Article=a.ID_Article
			INNER JOIN wp_ab_issues i ON a.ID_Issue=i.ID_Issue
			INNER JOIN (SELECT r.ID_Article, MAX(r.RevNo) as MaxRevNo
				FROM wp_ab_reviews r
				WHERE r.ID_Expert={$ID_Expert}
				GROUP BY r.ID_Article) r1
			ON r.ID_Article=r1.ID_Article AND r.RevNo=r1.MaxRevNo 
		WHERE r.ID_Expert={$ID_Expert}");
		
		echo g_ctj($result);
		exit();
	}
	