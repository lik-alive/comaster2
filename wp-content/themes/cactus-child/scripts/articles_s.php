<?php
	//Get all articles
	add_action('wp_ajax_articles_get_json', 'articles_get_json');	
	function articles_get_json(){
		global $wpdb;						
		$result =  $wpdb->get_results(
		"SELECT 
			a.*,
			i.Title as ITitle,
			s.Title as STitle
		FROM 
			wp_ab_articles a 
			INNER JOIN wp_ab_issues i ON a.ID_Issue = i.ID_Issue
			INNER JOIN wp_ab_sections s ON a.ID_Section = s.ID_Section");
		
		echo g_ctj($result);
		exit();
	}
	
	//Get article info
	add_action('wp_ajax_articles_get_article_json', 'articles_get_article_json');	
	function articles_get_article_json() {
		$ID_Article = g_si($_GET['id']);
		global $wpdb;						
		$result =  $wpdb->get_row(
		"SELECT 
			a.*,
			s.Title as STitle,
			i.Title as ITitle,
			e.Name as CorName,
			e.Mail as CorMail,
			v.MinRecvDate
		FROM 
			wp_ab_articles a 
				INNER JOIN wp_ab_issues i ON a.ID_Issue=i.ID_Issue
				INNER JOIN wp_ab_sections s ON a.ID_Section=s.ID_Section
				INNER JOIN wp_ab_experts e ON a.ID_CorAuthor=e.ID_Expert
				INNER JOIN 
					(SELECT v.ID_Article, MIN(v.RecvDate) as MinRecvDate
					FROM wp_ab_versions v 
					WHERE v.ID_Article={$ID_Article}
					GROUP BY v.ID_Article) v ON a.ID_Article=v.ID_Article
		WHERE 
			a.ID_Article={$ID_Article}");
		
		echo g_ctj($result);
		exit();
	}
	
	//Get versions for the article
	add_action('wp_ajax_articles_get_article_versions_json', 'articles_get_article_versions_json');	
	function articles_get_article_versions_json() {
		$ID_Article = g_si($_GET['id']);
		
		global $wpdb;						
		$result =  $wpdb->get_results(
		"SELECT *			
		FROM 
			wp_ab_versions v			
		WHERE 
			v.ID_Article={$id}
		ORDER BY
			v.RecvDate;");
		
		echo g_ctj($result);
		exit();
	}
	
	//Get article from POST-request data
	function getArticleFromPOST() {
		$article = array(
			'ID_Article' => $_POST['ID_Article'],
			'Title' => $_POST['Title'],
			'Authors' => $_POST['Authors'],
			'Affiliation' => $_POST['Affiliation'],
			'PageCount' => $_POST['PageCount'],
			'ID_Issue' => $_POST['ID_Issue'],
			'ID_Section' => $_POST['ID_Section'],
			'HasPriority' => $_POST['HasPriority'],
			'RemDate' =>  $_POST['RemDate'],
			'FinalVerdictDate' =>  $_POST['FinalVerdictDate'],
			'ID_CorAuthor' => $_POST['ID_CorAuthor']
		);
		//Remove all null-values (unexisted in POST-request)
		foreach (array_keys($article, null, true) as $key) {
			unset($article[$key]);
		}
		//Change all '' values to null
		foreach (array_keys($article, '', true) as $key) {
			$article[$key] = null;
		}
		
		return $article;
	}
	
	//Create an article
	add_action('wp_ajax_articles_create_json', 'articles_create_json');	
	function articles_create_json() {
		try {
			global $wpdb;
			$wpdb->query('START TRANSACTION');
		
			//Add corauthor (before - check if exists)
			$mail = g_si($_POST['CorMail']);
			$result =  $wpdb->get_row(
				"SELECT *
				FROM wp_ab_experts e
				WHERE e.Mail='{$mail}'");
		
			if (!$result) {
				$expert = array(
					'Name' => g_nsd($_POST['CorName']),
					'CallName' => g_nsd($_POST['CorCallName']),
					'Mail' => g_nsd($_POST['CorMail']),
					'Language' => g_nsd($_POST['CorLanguage'])
				);
				$ID_CorAuthor = dbhandler_add_entity_TH('wp_ab_experts', $expert);
			}
			else {
				$ID_CorAuthor = $result->ID_Expert;
				if ($_POST['CorName'] !== $result->Name || $_POST['CorCallName'] !== $result->CallName || $_POST['CorLanguage'] !== $result->Language) 
					throw new PublicException('Автор с таким E-mail уже существует: '.$mail);
			}
			
			//Add article
			$article =  getArticleFromPOST();
			$article['ID_Issue'] = 3;
			$article['ID_CorAuthor'] = $ID_CorAuthor;
			$ID_Article = dbhandler_add_entity_TH('wp_ab_articles', $article);
			
			//Add version
			$version = array(
				'ID_Article' => $ID_Article,
				'RecvDate' => g_nsd($_POST['RecvDate'])
			);
			$ID_Version = dbhandler_add_entity_TH('wp_ab_versions', $version);
			
			//Load files
			files_update_TH('version', $_FILES['file'], array('ID_Article' => $ID_Article, 'ID_Version' => $ID_Version));
			
			$wpdb->query('COMMIT');
			g_lev('Статья добавлена', __FUNCTION__, $ID_Article);
			
		} catch (Exception $e) {
			$wpdb->query('ROLLBACK');
			if (get_class($e) == 'PublicException') 
				echo g_ctj(array(2, $e->getMessage()));
			else
				echo g_ler_j('Ошибка добавления статьи', __FUNCTION__, $e->getMessage());
			exit();
		}
		
		//Additional actions
		if ($_POST['LetterToAuthor'] === 'Y') {
			try {
				letters_create_and_send_TH('toA_NewA', $ID_Article);
				g_lev('Письмо автору отправлено', __FUNCTION__);
			} catch(Exception $e) {
				g_ler("Ошибка отправления письма автору", __FUNCTION__, $e->getMessage());
			}
		}
		
		if ($_POST['LetterToEditor'] === 'Y') {
			try {
				letters_create_and_send_TH('toE_NewA', $ID_Article);
				g_lev('Письмо редактору отправлено', __FUNCTION__);
			} catch(Exception $e) {
				g_ler("Ошибка отправления письма редактору", __FUNCTION__, $e->getMessage());
			}
		}
		
		echo g_ctj(array(1, '', $ID_Article));
		exit();
	}
	
	//Edit the article
	add_action('wp_ajax_articles_edit_json', 'articles_edit_json');	
	function articles_edit_json() {
		$article = getArticleFromPOST();
		
		try {
			dbhandler_set_entity_TH('wp_ab_articles', $article, 'ID_Article', $article['ID_Article']);
			echo g_lev_j('Статья обновлена', __FUNCTION__, $article['ID_Article']);
		} catch (Exception $e) {
			echo g_ler_j('Ошибка обновления статьи', __FUNCTION__, $e->getMessage());
		}
		
		exit();
	}
	
	//Set scientific approve
	add_action('wp_ajax_articles_sciapp_json', 'articles_sciapp_json');	
	function articles_sciapp_json() {
		$article = array('ID_Article' => g_si($_GET['id']));
		$article['FinalVerdictDate'] = date('Y-m-d');
		
		try {
			dbhandler_set_entity_TH('wp_ab_articles', $article, 'ID_Article', $article['ID_Article']);
			letters_create_and_send_TH('toA_SciApp', $article['ID_Article']);
			echo g_lev_j('Статья одобрена научно', __FUNCTION__, $article['ID_Article']);
		} catch(Exception $e) {
			echo g_ler_j('Ошибка обновления статьи', __FUNCTION__, $e->getMessage());
		}
		exit();
	}
	
	/*****Chat*****/
	add_action('wp_ajax_articles_get_chat_json', 'articles_get_chat_json');	
	function articles_get_chat_json() {
		$ID_Article = g_si($_GET['id']);
		
		global $wpdb;						
		$result =  $wpdb->get_results(
		"SELECT c.*, wu.display_name
		FROM wp_ab_chats c INNER JOIN wp_users wu ON c.ID_User=wu.ID
		WHERE c.ID_Article={$ID_Article}
		ORDER BY c.DateTime DESC");
		
		echo g_ctj($result);
		exit();
	}
	
	add_action('wp_ajax_articles_create_comment_json', 'articles_create_comment_json');	
	function articles_create_comment_json() {
		$chat = array(
			'ID_Article' => $_POST['ID_Article'],
			'ID_User' => wp_get_current_user()->ID,
			'DateTime' => date('Y-m-d H:i:s'),
			'Message' => g_si($_POST['Message'])
		);
		
		try {
			$ID_Chat = dbhandler_add_entity_TH('wp_ab_chats', $chat);
			echo g_lev_j('Сообщение добавлено', __FUNCTION__, g_si($_POST['ID_Article']));
		} catch(Exception $e) {
			echo g_ler_j('Ошибка добавления сообщения', __FUNCTION__, $e->getMessage());
		}
		exit();
	}
	
	