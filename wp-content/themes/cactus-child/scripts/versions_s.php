<?php
	
	$COMTYPES = array(
		'%CompletelyWrong%',
		'%NeedVector%',
		'%SeparateFiles%',
		'%BlackPictures%',
		'%AutoNumbering%',
		'%NoGRNTI%',
		'%WrongLinking%',
		'%ColorPictures%',
		'%PictureTables%',
		'%MSEquation%',
		'%Fractions%');
	
	//Return language-based string representation for tech comments
	function getTechComString($type, $lang) {
		if ($type === '%CompletelyWrong%') {
			if ($lang === 'R') return 'Статья полностью не соответствует требованиям к оформлению статей.';
			else return 'The article does not correspond to the Guidelines.';
		}
		else if ($type === '%WrongLinking%') {
			if ($lang === 'R') return 'Иллюстрации должны быть прилинкованны, а не встроены в файл. Для корректной линковки иллюстраций необходимо сначала поместить рисунки и DOC-файл в одну папку. После этого рисунки должны быть вставлены в текст, используя опцию "Связать с файлом".  Корректная линковка невозможна в MS Word 2007/2010 и DOCX-формате (только DOC).';
			else return 'The illustrations should be linked with the DOC document, not embedded into it. For correct linking, you need to place the figures and the DOC file in the same folder. Then the figures should be inserted into the document with the option "link to file". Correct linking is impossible in MS Word 2007/2010 and DOCX-format (only DOC).';
		} 
		else if ($type === '%ColorPictures%') {
			if ($lang === 'R') return 'В статье присутствуют цветные изображения. Публикация в цвете является платной услугой - стоимость печати одной цветной страницы на весь тираж составляет 3000 руб. Если Вам не требуется публикация в цвете, то просим прислать варианты рисунков в градациях серого.';
			else return 'The article contains pictures in color. Color printing in our journal is the payable service, the cost of printing one color page is 3000 rub. If you have no need in color pictures, please, provide their grayscale versions.';
		} 
		else if ($type === '%NoGRNTI%') {
			if ($lang === 'R') return 'Отсутствует ГРНТИ.';
			else return 'No GRNTI presented.';
		} 
		else if ($type === '%AutoNumbering%') {
			if ($lang === 'R') return 'Автонумерация формул, рисунков, таблиц, источников не используется в журнале.';
			else return 'Auto-numbering of formulae, figures, tables, references in the journal is prohibited.';
		} 
		else if ($type === '%NeedVector%') {
			if ($lang === 'R') return 'Данные изображения должны быть предоставлены в векторном формате. Векторные изображения (схемы, диаграммы, графики) рисуются в одной из программ: CorelDraw! версий 11-13 (файлы формата CDR) или Inkscape (SVG) – и экспортируются в файлы формата Windows Metafile (WMF). Каждое из указанных изображений должно быть предоставлено в ДВУХ файлах: оригинал (CDR, SVG) и экспортированный (WMF).';
			else return 'Must be provided in vector form. Raster pictures or traced ones, inserted in a vector file, are not accepted. Vector images (charts, diagrams, graphs) need to be drawn in one of the following programs: CorelDraw versions 11-13 (CDR format files) or Inkscape (SVG) - and exported to Windows Metafile (WMF) format files. Please, provide BOTH files of each illustration: original (CDR, SVG) and exported (WMF).';
		} 
		else if ($type === '%SeparateFiles%') {
			if ($lang === 'R') return 'Содержат по нескольку изображений. Каждое из них необходимо предоставить отдельным файлом.';
			else return 'Contain several illustration in one figure. All illustrations should be submitted as separate files.';
		} 
		else if ($type === '%MSEquation%') {
			if ($lang === 'R') return 'Формулы набираются в редакторе формул MathType. Не допускается набирать формулы в MS Equation 2007/2010/2013.';
			else return 'MS Equations are not accepted. All equations should be created in MathType editor.';
		} 
		else if ($type === '%PictureTables%') {
			if ($lang === 'R') return 'Таблица не должна быть рисунком. Используйте инструмент "Создать таблицу" MS Word.';
			else return 'The tables should be created using MS-Word "Create Table" tool.';
		}
		else if ($type === '%BlackPictures%') {
			if ($lang === 'R') return 'Для типографии, с которой мы работаем, очень нежелательны чёрные и очень тёмные иллюстрации. При печати возникают полосатости и артефакты. Рекомендуем инвертировать эти иллюстрации. А если негативность так уж существенна для Вас, то для инвертированной иллюстрации можно в подрисуночной подписи это специально указать.';
			else return 'For our printing house, large dark illustrations are extremely undesirable (artifacts, print banding). We recommend you to invert these illustrations. If negativity is so crucial, you can specify the fact of inversion in the figure caption.';
		}
		else if ($type === '%Fractions%') {
			if ($lang === 'R') return 'В статьях на русском языке в десятичных дробных числах дробная часть отделяется запятой (например – 4,27).';
		}
		
		return '';
	}
	
	//Return language-based string representation for figure numbers
	function getFigString($figs, $lang) {
		if ($lang === 'R') return 'Рис. '.$figs.': ';
		else return 'Fig. '.$figs.': ';
	}
	
	//Get version
	function versions_get_version($ID_Version) {
		global $wpdb;						
		$result =  $wpdb->get_row(
		"SELECT 
			v.*,
			a.Title as ATitle
		FROM 
			wp_ab_versions v 
				INNER JOIN wp_ab_articles a ON v.ID_Article=a.ID_Article
		WHERE 
			v.ID_Version={$ID_Version}");
		return $result;
	}
	
	//Get version
	add_action('wp_ajax_versions_get_version_json', 'versions_get_version_json');	
	function versions_get_version_json() {
		$version = versions_get_version(g_si($_GET['id']));
		$files = array();
		$pdf = files_get_article_by_version_pdf($version->ID_Article, $version->ID_Version);
		if (!is_null($pdf)) $files[] = $pdf;
		$version->ArticlePdf = $files;
		echo g_ctj($version);
		exit();
	}
	
	//Get all versions for the article
	add_action('wp_ajax_versions_get_article_versions_json', 'versions_get_article_versions_json');	
	function versions_get_article_versions_json() {
		$ID_Article = g_si($_GET['id']);
		
		global $wpdb;						
		$result =  $wpdb->get_results(
		"SELECT 
			v.*
		FROM 
			wp_ab_versions v
		WHERE 
			v.ID_Article={$ID_Article}
		ORDER BY
			v.VerNo DESC;");
			
		//Get file info for Article
		foreach ($result as $row) {
			$ID_Article = $row->ID_Article;
			$ID_Version = $row->ID_Version;
			$article = files_get_version_pdf($ID_Article, $ID_Version);
			if ($article) $row->ArticlePdf = files_get_url_path($article['path']);
		}
		
		echo g_ctj($result);
		exit();
	}
	
	//Get last version
	function versions_get_article_version_last($ID_Article) {
		global $wpdb;						
		$result =  $wpdb->get_row(
		"SELECT 
			v.*
		FROM 
			wp_ab_versions v
		WHERE 
			v.ID_Article={$ID_Article}
		ORDER BY
			v.VerNo DESC;");
		return $result;
	}
	
	//Get version from POST-request data
	function getVersionFromPOST() {
		$version = array(
			'ID_Version' => $_POST['ID_Version'],
			'ID_Article' => $_POST['ID_Article'],
			'RecvDate' => $_POST['RecvDate'],
			'TechComments' => $_POST['TechComments'],
			'ToAuthDate' => $_POST['ToAuthDate']
		);
		//Remove all null-values (unexisted in POST-request)
		foreach (array_keys($version, null, true) as $key) {
			unset($version[$key]);
		}
		//Change all '' values to null
		foreach (array_keys($version, '', true) as $key) {
			$version[$key] = null;
		}
		
		return $version;
	}
	
	//Edit the version
	add_action('wp_ajax_versions_edit_json', 'versions_edit_json');	
	function versions_edit_json() {
		$version = getVersionFromPOST();
		
		try {
			dbhandler_set_entity_TH('wp_ab_versions', $version, 'ID_Version', $version['ID_Version']);
			$version = versions_get_version($version['ID_Version']);
			
			if (!is_null($_FILES['file']))
				files_update_TH('version', $_FILES['file'], array('ID_Article' => $version->ID_Article, 'ID_Version' => $version->ID_Version));
			
			echo g_lev_j('Версия обновлена', __FUNCTION__, $version->ID_Version.', статья: '.$version->ID_Article);
		} catch (Exception $e) {
			echo g_ler_j('Ошибка обновления версии', __FUNCTION__, $e->getMessage());
		}
		exit();
	}
	
	//Set tech comments
	add_action('wp_ajax_versions_set_techcomments_json', 'versions_set_techcomments_json');	
	function versions_set_techcomments_json() {
		$ID_Version = g_nsd($_POST['ID_Version']);
		
		$comments = '';
		if ($_POST['Overall'] === '0') $comments = '%CompletelyWrong%';
		else if ($_POST['Overall'] === '1') $comments = '%CompletelyOK%';
		else {
			if ($_POST['WrongLinking'] === 'Y') $comments .= '%WrongLinking%'."\n";
			if ($_POST['ColorPictures'] === 'Y') $comments .= '%ColorPictures%'."\n";
			if ($_POST['NoGRNTI'] === 'Y') $comments .= '%NoGRNTI%'."\n";
			if ($_POST['AutoNumbering'] === 'Y') $comments .= '%AutoNumbering%'."\n";
			if ($_POST['PictureTables'] === 'Y') $comments .= '%PictureTables%'."\n";
			if ($_POST['MSEquation'] === 'Y') $comments .= '%MSEquation%'."\n";
			if ($_POST['Fractions'] === 'Y') $comments .= '%Fractions%'."\n";
			if ($_POST['WrongSubject'] === 'Y') $comments .= '%WrongSubject%'."\n";
			if ($_POST['NeedVector'] !== '') $comments .= '%NeedVector%'."\n".g_nsd($_POST['NeedVector'])."\n";
			if ($_POST['SeparateFiles'] !== '') $comments .= '%SeparateFiles%'."\n".g_nsd($_POST['SeparateFiles'])."\n";
			if ($_POST['BlackPictures'] !== '') $comments .= '%BlackPictures%'."\n".g_nsd($_POST['BlackPictures'])."\n";
			if ($_POST['Others'] !== '') $comments .= '%Others%'."\n".stripslashes(g_nsd($_POST['Others']));
		}
		
		global $wpdb;
		$version = array('TechComments' => g_nsd($comments));
		
		try {
			dbhandler_set_entity_TH('wp_ab_versions', $version, 'ID_Version', $ID_Version);
			$version = versions_get_version($ID_Version);
			echo g_lev_j('Техзамечания обновлены', __FUNCTION__, $ID_Version.', статья: '.$version->ID_Article);
		} catch (Exception $e) {
			echo g_ler_j('Ошибка обновления техзамечаний', __FUNCTION__, $e->getMessage());
		}
		exit();
	}
	
	//Add new version
	add_action('wp_ajax_versions_create_json', 'versions_create_json');	
	function versions_create_json() {
		$version = getVersionFromPOST();
		
		try {
			global $wpdb;
			$wpdb->query('START TRANSACTION');
				
			$ID_Version = dbhandler_add_entity_TH('wp_ab_versions', $version);
			
			//Copy files
			if (!is_null($_FILES['file'])) 
				files_update_TH('version', $_FILES['file'], array('ID_Article' => $version['ID_Article'], 'ID_Version' => $ID_Version));
			
			$wpdb->query('COMMIT');
			echo g_lev_j('Версия добавлена', __FUNCTION__, $ID_Version);
			
		} catch (Exception $e) {
			$wpdb->query('ROLLBACK');
			echo g_ler_j('Ошибка добавления версии', __FUNCTION__, $e->getMessage());
			exit();
		}
		
		//Confirmation letter
		if ($_POST['SendConfLetter'] === 'Y') {
			try {
				letters_create_and_send_TH('toA_Conf', $version['ID_Article']);
				g_lev('Письмо автору отправлено', __FUNCTION__);
			} catch(Exception $e) {
				g_ler("Ошибка отправления письма автору", __FUNCTION__, $e->getMessage());
			}
		}
		
		exit();
	}