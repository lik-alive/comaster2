<?php
	//Get review
	add_action('wp_ajax_reviews_get_review_json', 'reviews_get_review_json');	
	function reviews_get_review_json() {
		$ID_Review = g_si($_GET['id']);
		
		global $wpdb;						
		$result =  $wpdb->get_row(
		"SELECT 
			r.*,
			a.Title as ATitle,
			e.Name as EName
		FROM 
			wp_ab_reviews r 
				INNER JOIN wp_ab_articles a ON r.ID_Article=a.ID_Article
				INNER JOIN wp_ab_experts e ON r.ID_Expert=e.ID_Expert
		WHERE 
			r.ID_Review={$ID_Review}");
		
		echo g_ctj($result);
		exit();
	}
	
	//Get all reviews for the article
	add_action('wp_ajax_reviews_get_article_reviews_json', 'reviews_get_article_reviews_json');	
	function reviews_get_article_reviews_json() {
		$ID_Article = g_si($_GET['id']);
		
		global $wpdb;						
		$result =  $wpdb->get_results(
		"SELECT 
			r.*,
			v.Title as VTitle,
			e.Name as EName
		FROM 
			wp_ab_reviews r 
				LEFT JOIN wp_ab_verdicts v ON r.ID_Verdict=v.ID_Verdict
				INNER JOIN wp_ab_experts e ON r.ID_Expert=e.ID_Expert
		WHERE 
			r.ID_Article={$ID_Article}
		ORDER BY
			r.RevNo DESC;");
			
		//Get file info for Review, Extra and Reply
		foreach ($result as $row) {
			$ID_Review = $row->ID_Review;
			$review = files_get_review_pdf($ID_Article, $ID_Review);
			if ($review) $row->ReviewPdf = files_get_url_path($review['path']);
			$extra = files_get_extra_pdf($ID_Article, $ID_Review);
			if ($extra) $row->ExtraPdf = files_get_url_path($extra['path']);
			$reply = files_get_reply_pdf($ID_Article, $ID_Review);
			if ($reply) $row->ReplyPdf = files_get_url_path($reply['path']);
		}
		
		echo g_ctj($result);
		exit();
	}
	
	//Get last reviews of each reviewer for the article
	function reviews_get_article_reviews_last($ID_Article) {
		global $wpdb;						
		$result =  $wpdb->get_results(
		"SELECT *
		FROM wp_ab_reviews r 
			LEFT JOIN wp_ab_verdicts v ON r.ID_Verdict=v.ID_Verdict
			INNER JOIN (SELECT r.ID_Expert, MAX(r.RevNo) as MaxRevNo
				FROM wp_ab_reviews r
				WHERE r.ID_Article={$ID_Article}
				GROUP BY r.ID_Expert) r1
			ON r.ID_Expert=r1.ID_Expert AND r.RevNo=r1.MaxRevNo 
		WHERE r.ID_Article={$ID_Article}");
			
		return $result;
	}
	
	//Get review from POST-request data
	function getReviewFromPOST() {
		$review = array(
			'ID_Review' => $_POST['ID_Review'],
			'ID_Article' => $_POST['ID_Article'],
			'ID_Expert' => $_POST['ID_Expert'],
			'ToExpDate' => $_POST['ToExpDate'],
			'FromExpDate' => $_POST['FromExpDate'],
			'ID_Verdict' => $_POST['ID_Verdict'],
			'Quality' => $_POST['Quality'],
			'ToAuthDate' => $_POST['ToAuthDate'],
			'FromAuthDate' => $_POST['FromAuthDate'],
			'RemDate' =>  $_POST['RemDate']
		);
		//Remove all null-values (unexisted in POST-request)
		foreach (array_keys($review, null, true) as $key) {
			unset($review[$key]);
		}
		//Change all '' values to null
		foreach (array_keys($review, '', true) as $key) {
			$review[$key] = null;
		}
		
		return $review;
	}
	
	//Add expert to the article
	add_action('wp_ajax_reviews_assign_expert_json', 'reviews_assign_expert_json');	
	function reviews_assign_expert_json() {
		try {
			global $wpdb;
			$wpdb->query('START TRANSACTION');
			
			//Check if the expert already set to the article
			$ID_Article = g_si($_POST['ID_Article']);
			$ID_Expert = g_si($_POST['ID_Expert']);
			$exists = $wpdb->get_var(
				"SELECT r.ID_Review
				FROM wp_ab_reviews r
				WHERE r.ID_Article = {$ID_Article} AND r.ID_Expert = {$ID_Expert}");
			
			//Expert already exists
			if (!is_null($exists))
				throw new PublicException('Данный рецензент уже назначен');
		
			//Add review
			$review = getReviewFromPOST();
			$ID_Review = dbhandler_add_entity_TH('wp_ab_reviews', $review);
			
			$wpdb->query('COMMIT');
			g_lev('Рецензент назначен', __FUNCTION__, $ID_Review);
			
		} catch (Exception $e) {
			$wpdb->query('ROLLBACK');
			
			if (get_class($e) == 'PublicException') 
				echo g_ctj(array(2, $e->getMessage()));
			else
				echo g_ler_j('Ошибка назначения рецензента', __FUNCTION__, $e->getMessage());
			
			exit();
		}
		
		//Additional actions
		if ($_POST['LetterToExpert'] === 'Y') {
			$inlife = $wpdb->get_var(
				"SELECT COUNT(r.ID_Review)
				FROM wp_ab_reviews r
				WHERE r.ID_Expert={$ID_Expert}");
					
			if ($inlife == 1) echo g_ctj(array(3, '', $ID_Review));
			else {
				try {
					letters_create_and_send_TH('toR_A', $ID_Article, $ID_Review);
					g_lev('Письмо рецензенту отправлено', __FUNCTION__);
				} catch(Exception $e) {
					g_ler('Ошибка отправления письма рецензенту', __FUNCTION__, $e->getMessage());
				}
				echo g_ctj(array(1, ''));
			}
		}
		
		exit();
	}
	
	//Edit the review
	add_action('wp_ajax_reviews_edit_json', 'reviews_edit_json');	
	function reviews_edit_json() {
		$review = getReviewFromPOST();
		
		try {
			dbhandler_set_entity_TH('wp_ab_reviews', $review, 'ID_Review', $review['ID_Review']);
			echo g_lev_j('Рецензия обновлена', __FUNCTION__, $review['ID_Review']);
		} catch (Exception $e) {
			echo g_ler_j('Ошибка обновления рецензии', __FUNCTION__, $e->getMessage());
		}
		
		exit();
	}
	
	//Set the expert verdict
	add_action('wp_ajax_reviews_set_expert_verdict_json', 'reviews_set_expert_verdict_json');	
	function reviews_set_expert_verdict_json() {
		$review = getReviewFromPOST();
		
		try {
			global $wpdb;	
			$wpdb->query('START TRANSACTION');
			
			//Edit review
			dbhandler_set_entity_TH('wp_ab_reviews', $review, 'ID_Review', $review['ID_Review']);
			
			//Copy files
			if (!is_null($_FILES['file'])) files_update_TH('review', $_FILES['file'], $review);
			if (!is_null($_FILES['filex'])) files_update_TH('extra', $_FILES['filex'], $review);
			
			$wpdb->query('COMMIT');
			echo g_lev_j('Ответ рецензента сохранён', __FUNCTION__, $review['ID_Review']);
			
		} catch (Exception $e) {
			$wpdb->query('ROLLBACK');
			echo g_ler_j('Ошибка сохранения ответа рецензента', __FUNCTION__, $review['ID_Review'].' '.$e->getMessage());
		}
		
		//Confirmation letter
		if ($_POST['SendConfLetter'] === 'Y') {
			try {
				$type = 'toR_Conf';
				if ($review['Quality'] === '3') $type = 'toR_ConfExc';
				
				letters_create_and_send_TH($type, $review['ID_Article'], $review['ID_Review']);
				g_lev('Письмо рецензенту отправлено', __FUNCTION__);
			} catch(Exception $e) {
				g_ler("Ошибка отправления письма рецензенту", __FUNCTION__, $e->getMessage());
			}
		}
		exit();
	}
	
	add_action('wp_ajax_reviews_set_author_reply_json', 'reviews_set_author_reply_json');	
	function reviews_set_author_reply_json() {
		$review = getReviewFromPOST();
		
		try {
			global $wpdb;	
			$wpdb->query('START TRANSACTION');
			
			//Edit review
			dbhandler_set_entity_TH('wp_ab_reviews', $review, 'ID_Review', $review['ID_Review']);
			
			//Copy reply file
			if (!is_null($_FILES['file'])) files_update_TH('reply', $_FILES['file'], $review);
			
			$wpdb->query('COMMIT');
			echo g_lev_j('Ответ автора сохранён', __FUNCTION__, $review['ID_Review']);
			
		} catch (Exception $e) {
			$wpdb->query('ROLLBACK');
			echo g_ler_j('Ошибка добавления статьи', __FUNCTION__, $e->getMessage());
			exit();
		}
		
		//Create next review step
		if ($_POST['SendExpLetter'] === 'Y') {
			$ID_Expert = $wpdb->get_var(
			"SELECT ID_Expert
			FROM wp_ab_reviews r
			WHERE r.ID_Review={$review['ID_Review']}");
		
			$review = array(
				'ID_Article' => $review['ID_Article'],
				'ID_Expert' => $ID_Expert
			);
			
			try {
				$ID_Review = dbhandler_add_entity_TH('wp_ab_reviews', $review);
				g_lev('Рецензия добавлена', __FUNCTION__, $ID_Review);
			} catch(Exception $e) {
				g_ler('Ошибка добавления рецензии', __FUNCTION__, $e->getMessage());
			}
			
			try {
				letters_create_and_send_TH('toR_A', $review['ID_Article'], $ID_Review);
				g_lev('Письмо рецензенту отправлено', __FUNCTION__);
			} catch(Exception $e) {
				g_ler('Ошибка отправления письма рецензенту', __FUNCTION__, $e->getMessage());
			}
		}
		
		exit();
	}
	
	//Delete the review
	add_action('wp_ajax_reviews_delete_json', 'reviews_delete_json');	
	function reviews_delete_json() {
		$ID_Review = $_POST['ID_Review'];
	
		try {
			$result = dbhandler_delete_entity_TH('wp_ab_reviews', 'ID_Review', $ID_Review);
			echo g_lev_j('Рецения удалена', __FUNCTION__, $ID_Review);
		} catch (Exception $e) {
			echo g_ler_j('Ошибка удаления рецензии', __FUNCTION__, dbhandler_get_last_error());
		}
		exit();
	}
	
	//Send soft reminders (Scheduled event)
	function reviews_send_softreminders() {
		global $wpdb;
		//Reminders to reviewers
		$result =  $wpdb->get_results(
		"SELECT r.ID_Article, r.ID_Review 
		FROM wp_ab_reviews r 
			INNER JOIN wp_ab_articles a ON r.ID_Article=a.ID_Article 
			INNER JOIN wp_ab_issues i ON a.ID_Issue=i.ID_Issue 
			INNER JOIN (SELECT r.ID_Article, MAX(r.RevNo) as MaxRevNo 
				FROM wp_ab_reviews r 
				GROUP BY r.ID_Expert, r.ID_Article) r1 ON r.ID_Article=r1.ID_Article AND r.RevNo=r1.MaxRevNo 
		WHERE i.IsActive = 'Y' AND r.ToExpDate IS NOT NULL AND r.ID_Verdict IS NULL 
			AND DATEDIFF(CURDATE(), r.ToExpDate) >= 10 AND DATEDIFF(CURDATE(), r.ToExpDate) <= 14 
			AND r.RemDate IS NULL");
		
		try {
			foreach ($result as $row) {
				letters_create_and_send_TH('toR_RemS', $row->ID_Article, $row->ID_Review);
				g_lev('Мягкое напоминание рецензенту отправлено', __FUNCTION__, $row->ID_Review);
			}
		} catch(Exception $e) {
			g_ler("Ошибка отправления мягких напоминаний рецензентам", __FUNCTION__, $e->getMessage());
		}
		
		//Reminders to authors
		$result =  $wpdb->get_results(
		"SELECT r.ID_Article
		FROM wp_ab_reviews r
			INNER JOIN wp_ab_articles a ON r.ID_Article=a.ID_Article
			INNER JOIN wp_ab_issues i ON a.ID_Issue=i.ID_Issue
			INNER JOIN (SELECT r.ID_Article, MAX(r.RevNo) as MaxRevNo
				FROM wp_ab_reviews r
				GROUP BY r.ID_Expert, r.ID_Article) r1 ON r.ID_Article=r1.ID_Article AND r.RevNo=r1.MaxRevNo
		WHERE i.IsActive = 'Y' AND r.ToAuthDate IS NOT NULL AND r.ID_Verdict <> 1 AND r.FromAuthDate IS NULL
        	AND DATEDIFF(CURDATE(), r.ToAuthDate) >= 20 AND DATEDIFF(CURDATE(), r.ToAuthDate) <= 30 
			AND a.RemDate IS NULL
        GROUP BY r.ID_Article");
		
		try {
			foreach ($result as $row) {
				letters_create_and_send_TH('toA_RemS', $row->ID_Article);
				g_lev('Мягкое напоминание автору отправлено', __FUNCTION__, $row->ID_Article);
			}
		} catch(Exception $e) {
			g_ler("Ошибка отправления мягких напоминаний авторам", __FUNCTION__, $e->getMessage());
		}
	}
	