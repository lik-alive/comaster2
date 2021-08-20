<?php
	//------Statistic
	//Get current user visible section name
	function main_get_user_section() {
		$ID_Section = g_cusi();
		
		if ($ID_Section == 1) {
			return 'раздела<br/>"ДО и ОТ"';
		}
		else if ($ID_Section == 2) {
			return 'раздела<br/>"ОИ и РО"';
		}
		else if ($ID_Section == 3) {
			return 'раздела<br/>"ЧМ и АД"';
		}
		
		return 'портфеля';
	}
	
	//Get number of articles in the nest with conditions
	function getnstcount($cond) {
		$wsc = g_cusc();
		global $wpdb;						
		$result = $wpdb->get_var(
			"SELECT 
				COUNT(a.ID_Article)
			FROM
				wp_ab_articles a 
					INNER JOIN wp_ab_issues i ON a.ID_Issue = i.ID_Issue
					INNER JOIN wp_ab_versions v ON a.ID_Article = v.ID_Article
			WHERE
				(v.ID_Article, v.VerNo) IN (
					SELECT v.ID_Article, MAX(v.VerNo) as VerNo
					FROM wp_ab_versions v
					GROUP BY v.ID_Article)
				AND i.IsActive = 'Y' {$cond} {$wsc}");
		
		return $result;
	}
	
	//Get number of articles in the nest 
	function main_get_nst_articles_count() {
		return getnstcount("");
	}
	
	//Get number of articles in the nest having both approves
	function main_get_nst_articles_approved_count() {
		return getnstcount("AND a.FinalVerdictDate IS NOT NULL AND v.TechComments LIKE '%CompletelyOK%'");
	}
	
	//Get number of articles in the nest having science approve
	function main_get_nst_articles_sciapproved_count() {
		return getnstcount("AND a.FinalVerdictDate IS NOT NULL");
	}
	
	//Get number of articles in the nest having technical approve
	function main_get_nst_articles_techapproved_count() {
		return getnstcount("AND v.TechComments LIKE '%CompletelyOK%'");
	}
	
	//------Main Table
	//Set status and priority for the secretary
	function addsecprior($article, $version, $reviews) {
		//Unsent reviews
		foreach ($reviews as $review) {
			if (is_null($review->ToExpDate)) {
				$article->Status = 'Статья не выслана';
				$article->PIndex = 37;
				return;
			}
		}
		//No reviewers
		if (sizeof($reviews) === 0) {
			$article->Status = 'Рецензенты не назначены';
			$article->PIndex = 36;
			return;
		}
		//All reviewers has canceled
		$count = 0;
		foreach($reviews as $review) {
			if (is_null($review->ID_Verdict) || $review->ID_Verdict < 5) $count++;
		}
		if ($count === 0)  {
			$article->Status = 'Все рецензенты отказались';
			$article->PIndex = 35;
			return;
		}
		//Controversal verdicts
		$count_notreceived = 0;
		$count_hascomms = 0;
		$count_sent = 0;
		$verdicts = "";
		foreach($reviews as $review) {
			if (is_null($review->FromExpDate)) $count_notreceived++;
			if (!is_null($review->FromExpDate) && $review->ID_Verdict > 1 && $review->ID_Verdict < 5) $count_hascomms++;
			if (!is_null($review->ToAuthDate) && $review->ID_Verdict > 1 && $review->ID_Verdict < 5) $count_sent++;
			if (!is_null($review->FromExpDate) && $review->ID_Verdict < 5) $verdicts = $verdicts." '".$review->Title."'";
		}
		if (is_null($article->FinalVerdictDate) && $count_notreceived === 0 && $count_sent < $count_hascomms)  {
			$article->Status = "Невысланные вердикты: {$verdicts}";
			$article->PIndex = 34;
			return;
		}
		//New tech comments
		if ($count_notreceived === 0
			&& !is_null($version->TechComments) 
			&& false === mb_strpos($version->TechComments, '%CompletelyOK%')
			&& is_null($version->ToAuthDate))  {
			$article->Status = "Невысланные тех-замечания";
			$article->PIndex = 33;
			return;
		}
		//Decision is required
		$count_positive = 0;
		foreach($reviews as $review) {
			if (!is_null($review->FromExpDate) && $review->ID_Verdict == 1) $count_positive++;
		}
		if (is_null($article->FinalVerdictDate) && $count_positive > 0 && $count_sent == 0 && $count_notreceived == 0)  {
			$article->Status = "Требуется решение по статье";
			$article->PIndex = 32;
			return;
		}
		//Reviewers are late
		$maxdays = 0;
		foreach($reviews as $review) {
			if (!is_null($review->ToExpDate) && is_null($review->FromExpDate)) {
				$sent = new DateTime($review->ToExpDate);
				$today = new DateTime();
				$interval = $today->diff($sent)->days;
				if ($interval > $maxdays) $maxdays = $interval;
			}
		}
		if ($maxdays > 14) {
			$article->Status = "Рецензент молчит {$maxdays} дней";
			$article->PIndex = 23;
			return;
		}
		//Authors are late
		$mindays = -1;
		foreach($reviews as $review) {
			if (!is_null($review->ToAuthDate) && $review->ID_Verdict > 1 && is_null($review->FromAuthDate)) {
				$sent = new DateTime($review->ToAuthDate);
				$today = new DateTime();
				$interval = $today->diff($sent)->days;
				if ($mindays === -1 || $interval < $mindays) $mindays = $interval;
			}
		}
		if (!is_null($version->ToAuthDate)) { 
			$sent = new DateTime($version->ToAuthDate);
			$today = new DateTime();
			$interval = $today->diff($sent)->days;
			if ($mindays === -1 || $interval < $mindays) $mindays = $interval;
		}
		
		if ($mindays > 30) {
			$article->Status = "Авторы молчат {$mindays} дней";
			$article->PIndex = 22;
			return;
		}		
		//Sci and Tech accepted
		if (!is_null($article->FinalVerdictDate) && false !== mb_strpos($version->TechComments, '%CompletelyOK%')) {
			$article->Status = 'Одобрена всеми';
			$article->PIndex = 13;
			return;
		}
		
		//Only sci accepted
		if (!is_null($article->FinalVerdictDate)) {
			$article->Status = 'Одобрена рецензентами';
			$article->PIndex = 12;
			return;
		}
		
		$article->Status = 'Всё идёт по плану';
		$article->PIndex = 2;
	}
	
	//Set status and priority for the editor
	function addsciprior($article, $version, $reviews) {
		//No reviewers
		if (sizeof($reviews) === 0) {
			$article->Status = 'Рецензенты не назначены';
			$article->PIndex = 35;
			return;
		}
		//All reviewers has canceled
		$count = 0;
		foreach($reviews as $review) {
			if (is_null($review->ID_Verdict) || $review->ID_Verdict < 5) $count++;
		}
		if ($count === 0)  {
			$article->Status = 'Все рецензенты отказались';
			$article->PIndex = 34;
			return;
		}
		//Controversal verdicts
		$count_notreceived = 0;
		$count_hascomms = 0;
		$count_sent = 0;
		$verdicts = "";
		foreach($reviews as $review) {
			if (is_null($review->FromExpDate)) $count_notreceived++;
			if (!is_null($review->FromExpDate) && $review->ID_Verdict > 1 && $review->ID_Verdict < 5) $count_hascomms++;
			if (!is_null($review->ToAuthDate) && $review->ID_Verdict > 1 && $review->ID_Verdict < 5) $count_sent++;
			if (!is_null($review->FromExpDate) && $review->ID_Verdict < 5) $verdicts = $verdicts." '".$review->Title."'";
		}
		if (is_null($article->FinalVerdictDate) && $count_notreceived === 0 && $count_sent < $count_hascomms)  {
			$article->Status = "Невысланные вердикты: {$verdicts}";
			$article->PIndex = 33;
			return;
		}
		//Decision is required
		$count_positive = 0;
		foreach($reviews as $review) {
			if (!is_null($review->FromExpDate) && $review->ID_Verdict == 1) $count_positive++;
		}
		if (is_null($article->FinalVerdictDate) && $count_positive > 0 && $count_sent == 0 && $count_notreceived == 0)  {
			$article->Status = "Требуется решение по статье";
			$article->PIndex = 32;
			return;
		}
		//Reviewers are late
		$maxdays = 0;
		foreach($reviews as $review) {
			if (!is_null($review->ToExpDate) && is_null($review->FromExpDate)) {
				$sent = new DateTime($review->ToExpDate);
				$today = new DateTime();
				$interval = $today->diff($sent)->days;
				if ($interval > $maxdays) $maxdays = $interval;
			}
		}
		if ($maxdays > 14) {
			$article->Status = "Рецензент молчит {$maxdays} дней";
			$article->PIndex = 23;
			return;
		}
		//Authors are late
		$mindays = -1;
		foreach($reviews as $review) {
			if (!is_null($review->ToAuthDate) && $review->ID_Verdict > 1 && is_null($review->FromAuthDate)) {
				$sent = new DateTime($review->ToAuthDate);
				$today = new DateTime();
				$interval = $today->diff($sent)->days;
				if ($mindays === -1 || $interval < $mindays) $mindays = $interval;
			}
		}
		if (!is_null($version->ToAuthDate)) { 
			$sent = new DateTime($version->ToAuthDate);
			$today = new DateTime();
			$interval = $today->diff($sent)->days;
			if ($mindays === -1 || $interval < $mindays) $mindays = $interval;
		}
		
		if ($mindays > 30) {
			$article->Status = "Авторы молчат {$mindays} дней";
			$article->PIndex = 22;
			return;
		}		
		//Sci and Tech accepted
		if (!is_null($article->FinalVerdictDate) && false !== mb_strpos($version->TechComments, '%CompletelyOK%')) {
			$article->Status = 'Одобрена всеми';
			$article->PIndex = 13;
			return;
		}
		
		//Only sci accepted
		if (!is_null($article->FinalVerdictDate)) {
			$article->Status = 'Одобрена рецензентами';
			$article->PIndex = 12;
			return;
		}
		
		$article->Status = 'Всё идёт по плану';
		$article->PIndex = 2;
	}
	
	//Set status and priority for the tech
	function addtechprior($article, $version, $reviews) {
		//Reviews already received
		$unsent = 0;
		foreach($reviews as $review) {
			if (!is_null($review->FromExpDate) && $review->ID_Verdict < 5) $unsent++;
		}
		if (is_null($version->TechComments) && $unsent > 0) {
			$article->Status = 'Срочно нужна тех.рецензия';
			$article->PIndex = 32;
			return;
		}
		//Waiting for tech review
		if (is_null($version->TechComments)) {
			$article->Status = 'Ожидается тех.рецензия';
			$article->PIndex = 22;
			return;
		}
		//Rev and Tech accepted
		if (!is_null($article->FinalVerdictDate) && false !== mb_strpos($version->TechComments, '%CompletelyOK%')) {
			$article->Status = 'Одобрена всеми';
			$article->PIndex = 13;
			return;
		}
		//Only Tech accepted
		if (false !== mb_strpos($version->TechComments, '%CompletelyOK%')) {
			$article->Status = 'Одобрена тех.отделом';
			$article->PIndex = 12;
			return;
		}
		
		$article->Status = 'Всё идёт по плану';
		$article->PIndex = 2;
	}
	
	//Get all articles in the nest with status remarks
	add_action('wp_ajax_main_get_nst_json', 'main_get_nst_json');	
	function main_get_nst_json(){
		$wsc = g_cusc();
		
		global $wpdb;						
		$result =  $wpdb->get_results(
		"SELECT 
			a.*,
			i.Title as ITitle,
			s.Title as STitle,
			v.RecvDate
		FROM 
			wp_ab_articles a 
			INNER JOIN wp_ab_issues i ON a.ID_Issue = i.ID_Issue
			INNER JOIN wp_ab_sections s ON a.ID_Section = s.ID_Section
			INNER JOIN wp_ab_versions v ON a.ID_Article = v.ID_Article
		WHERE 
			i.IsActive = 'Y' AND v.VerNo = 1 {$wsc}");
		
		foreach ($result as $article) {
			$version = versions_get_article_version_last($article->ID_Article);
			$reviews = reviews_get_article_reviews_last($article->ID_Article);
			
			$end = time();
			$recv = strtotime($article->RecvDate);
			
			$article->Days = floor(($end - $recv) / (60 * 60 * 24));
			
			if (g_cun() === 'secret') addsecprior($article, $version, $reviews);
			else if (g_cun() === 'tech' || g_cun() === 'teditor') addtechprior($article, $version, $reviews);
			else addsciprior($article, $version, $reviews);
			
			
			if ($article->ID_Issue != 3) {
				$article->Status = "[{$article->ITitle}] ".$article->Status;
				if ($article->PIndex > 20) $article->PIndex += 1;
				else if ($article->PIndex > 0) $article->PIndex -= 1;
			}
		}
		
		echo g_ctj($result);
		exit();
	}
	
	//Reorder articles
	add_action('wp_ajax_main_article_reorder_json', 'main_article_reorder_json');	
	function main_article_reorder_json(){
		$ID_Article = $_GET['id'];
		$newnumber = $_GET['val'];
		
		try {
			global $wpdb;
			$wpdb->query('START TRANSACTION');
			$article = dbhandler_get_entity('wp_ab_articles', 'ID_Article', $ID_Article);
			if (!$article)
				throw new Exception('Статья не найдена: '.$ID_Article);
		
			$result = $wpdb->get_results(
				"SELECT *
				FROM wp_ab_articles a
				WHERE a.ID_Issue={$article->ID_Issue} 
					AND a.ID_Section={$article->ID_Section}
				ORDER BY a.SeqNumber");
			
			$corrnumber = $result[$newnumber-1]->SeqNumber; //SeqNumber may differ from its position in the section (1,5,6,7)
				
			if ($article->SeqNumber > $corrnumber) {
				$result = $wpdb->query(
				"UPDATE wp_ab_articles a
				SET a.SeqNumber = a.SeqNumber+1
				WHERE a.ID_Issue={$article->ID_Issue} 
					AND a.ID_Section={$article->ID_Section} 
					AND a.SeqNumber>={$corrnumber} AND a.SeqNumber<{$article->SeqNumber}");
			} else {
				$result = $wpdb->query(
				"UPDATE wp_ab_articles a
				SET a.SeqNumber = a.SeqNumber-1
				WHERE a.ID_Issue={$article->ID_Issue} 
					AND a.ID_Section={$article->ID_Section} 
					AND a.SeqNumber>{$article->SeqNumber} AND a.SeqNumber<={$corrnumber}");
			}
			if (!$result)
				throw new Exception('Ошибка перемещения статей: '.dbhandler_get_last_error());
			
			$article->SeqNumber = $corrnumber;
			dbhandler_set_entity_TH('wp_ab_articles', $article, 'ID_Article', $ID_Article);
			
			$wpdb->query('COMMIT');
			echo g_lev_j('Статья успешно перемещена', __FUNCTION__, $ID_Article);
			
		} catch (Exception $e) {
			$wpdb->query('ROLLBACK');
			echo g_ler_j('Ошибка перемещения статьи', __FUNCTION__, $e->getMessage());
		}
		exit();
	}
	
	//Add issue
	add_action('wp_ajax_main_issue_create_json', 'main_issue_create_json');	
	function main_issue_create_json(){
		global $wpdb;
		$wpdb->query('START TRANSACTION');
			
		$title =  $wpdb->get_var(
			"SELECT i.Title
			FROM wp_ab_issues i
			ORDER BY i.ID_Issue DESC");
		
		//Increment issueNo
		$arr = explode('-',$title);
		if ($arr[1] === '6') {
			$arr[0] = $arr[0] + 1;
			$arr[1] = '1';
		} else {
			$arr[1] = $arr[1] + 1;
		}
		
		$issue = array('Title' => $arr[0].'-'.$arr[1]);
		
		try {
			$ID_Issue = dbhandler_add_entity_TH('wp_ab_issues', $issue);
			
			$wpdb->query('COMMIT');
			echo g_lev_j("Выпуск {$issue['Title']} добавлен", __FUNCTION__, $ID_Issue);
			
		} catch(Exception $e) {
			$wpdb->query('ROLLBACK');
			echo g_ler_j('Ошибка добавления выпуска', __FUNCTION__, $e->getMessage());
		}
		
		exit();
	}
	
	//Archive issue
	add_action('wp_ajax_main_issue_archive_json', 'main_issue_archive_json');	
	function main_issue_archive_json(){
		global $wpdb;
		$ID_Issue =  $wpdb->get_var(
			"SELECT i.ID_Issue
			FROM wp_ab_issues i
			WHERE i.IsActive = 'Y' AND i.ID_Issue <> 3
			ORDER BY i.ID_Issue");
		
		$issue = array(
			'IsActive' => 'N'
			);
		
		try {
			dbhandler_set_entity_TH('wp_ab_issues', $issue, 'ID_Issue', $ID_Issue);
			echo g_lev_j("Выпуск {$issue['Title']} заархивирован", __FUNCTION__);
		} catch (Exception $e) {
			echo g_ler_j('Ошибка архивирования выпуска', __FUNCTION__, $e->getMessage());
		}
		exit();
	}
	
	function main_send_NYletter($pars) {
		$arr = explode("\n", $pars);
		
		global $wpdb;
		$no = 0;
		foreach ($arr as $id) {
			$no++;
			$result =  $wpdb->get_results(
			"SELECT *
			FROM wp_ab_experts e
			WHERE e.ID_Expert = {$id}");
			
			if (sizeof($result) !== 1) {
				echo g_ctj($result);
				exit();
			}
			
			if ($result[0]->Language != 'R') continue;
			
			$letter = array();
			$letter['ToName'] = $result[0]->Name;
			$letter['ToMail'] = $result[0]->Mail;
			$letter['Subject'] = 'Ветер перемен';
			$letter['Delay'] = 30 + (int)($no / 10);
			
			$text = "\n".
					"\n".
					"Спасибо большое, что были с нами в 2019 году и помогли его пережить! Редакция крайне \n".
					"признательна за то, что может всецело положиться на Ваши знания и опыт даже в такие \n".
					"(ну... не самые простые) времена.\n".
					"Именно благодаря Вашей неизменной поддержке и активному участию, журнал стоит на \n".
					"пороге вхождения в 1-й квартиль по версии Scimago Journal & Country Rank (SJR) и \n".
					"получения импакт-фактора по версии Web of Science (WoS).\n".
					"\n".
					"От имени редакции журнала «Компьютерная оптика» хочу поздравить Вас с наступающим \n".
					"2020 годом! Пусть в Новом Году самые дерзкие идеи находят признание, эксперименты \n".
					"не противоречат теории, а Ваши статьи чаще появляются на страницах нашего журнала ;-)\n".
					"\n".
					"Всего самого светлого в Новом Году! Ура! =D\n".
					"\n".
					"\n";
			$text = getHeader2('R', $result[0]->CallName).$text;
			$text = $text.getFooter('R');
			$letter['Text'] = $text;
			
			letters_send_TH($letter);
		}
		echo g_ctj('OK');
		exit();
	}
	
	//Service action
	add_action('wp_ajax_main_service_json', 'main_service_json');	
	function main_service_json(){
		$pars = $_POST['Pars'];
		
		//main_send_NYletter($pars);
		//echo g_ctj('OK');
		//exit();
		
		//To reviewers
		$arr = explode("\n", $pars);
		
		global $wpdb;
		foreach ($arr as $line) {
			$tmp = explode("\t", $line);
			$name = $tmp[0];
			$sum = $tmp[1];
			$sum = str_replace("\n", "", $sum);
			$sum = str_replace("\r", "", $sum);
			
			$result =  $wpdb->get_results(
			"SELECT *
			FROM wp_ab_experts e
			WHERE e.ID_Expert IN (SELECT r.ID_Expert
				FROM wp_ab_reviews r
				GROUP BY r.ID_Expert) 
				AND e.Name='{$name}'");
			
			if (sizeof($result) !== 1) {
				echo "ERROR with ".$name;
				exit();
			}
			
			$letter = array();
			$letter['ToName'] = $result[0]->Name;
			$letter['ToMail'] = $result[0]->Mail;
			$letter['Subject'] = 'Надбавки за рецензирование';
			
			$text = "\n".
					"\n".
					"Редакция журнала \"Компьютерная оптика\" благодарит Вас за активное участие в рецензировании.\n".
					"\n".
					"В соответствии с регламентом, за написание рецензий для нашего журнала Вам полагается денежное \n".
					"вознаграждение в размере {$sum} рублей, которое выплачено Вам в виде надбавки за ноябрь 2019 \n".
					"по Институту информатики, математики и электроники.\n".
					"\n".
					"\n";
			$text = getHeader('R', $result[0]->CallName).$text;
			$text = $text.getFooter('R');
			$letter['Text'] = $text;
			
			letters_send_TH($letter);
		}
		echo g_ctj('OK');
		exit();
	}
	
	//Service action
	add_action('wp_ajax_main_service2_json', 'main_service2_json');	
	function main_service2_json(){
		$pars = $_POST['Pars'];
		
		//main_send_NYletter($pars);
		//echo g_ctj('OK');
		//exit();
		
		//To reviewers
		$arr = explode("\n", $pars);
		
		global $wpdb;
		foreach ($arr as $line) {
			$tmp = explode("\t", $line);
			$name = $tmp[0];
			$phone = $tmp[1];
			$phone = str_replace("\n", "", $phone);
			$phone = str_replace("\r", "", $phone);
			
			$result =  $wpdb->get_results(
			"SELECT *
			FROM wp_ab_experts e
			WHERE e.ID_Expert IN (SELECT r.ID_Expert
				FROM wp_ab_reviews r
				GROUP BY r.ID_Expert) 
				AND e.Name='{$name}'");
			
			if (sizeof($result) !== 1) {
				echo "ERROR with ".$name;
				exit();
			}
			
			$letter = array();
			$letter['ToName'] = $result[0]->Name;
			$letter['ToMail'] = $result[0]->Mail;
			$letter['Subject'] = 'Выплата за рецензирование';
			
			if (empty($phone)) {
				$laststr = "Пришлите, пожалуйста, Ваш номер для перевода средств.";
			} else {
				$laststr = "Подтвердите, пожалуйста, Ваш номер: +7-$phone.";
			}
			
			$text = "\n".
					"\n".
					"Редакция журнала \"Компьютерная оптика\" благодарит Вас за активное участие в рецензировании.\n".
					"\n".
					"В соответствии с регламентом, за написание рецензий для нашего журнала Вам полагается денежное \n".
					"вознаграждение, которое выплачивается на номер мобильного телефона. \n".
					"$laststr\n".
					"\n".
					"\n";
			$text = getHeader('R', $result[0]->CallName).$text;
			$text = $text.getFooter('R');
			$letter['Text'] = $text;
			
			letters_send_TH($letter);
		}
		echo g_ctj('OK');
		exit();
	}
