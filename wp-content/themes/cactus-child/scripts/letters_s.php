<?php
$BATEXEC = 'C:/Program Files/The Bat!/thebat64.exe';
$BATMAILOUTBOX = '//ko@smr.ru/Outbox';
$LETTERSFOLDER = 'letters/';
$LETTERTEMPLATE = 'ini/template.txt';

//Create subject for a letter
function createSubject($ID_Article)
{
	global $wpdb;
	return $wpdb->get_var(
		"SELECT CONCAT(a.Authors, ' ', a.Title)
				FROM wp_ab_articles a
				WHERE a.ID_Article={$ID_Article}"
	);
}

//Get letter header
function getHeader($lang, $name)
{
	if ($lang == 'R') {
		return "Здравствуйте, {$name}!";
	} else {
		return "Dear {$name},";
	}
}

//Get letter header
function getHeader2($lang, $name)
{
	if ($lang == 'R') {
		return "Уважаемый {$name}!";
	} else {
		return "Dear {$name},";
	}
}

//Get letter footer
function getFooter($lang)
{
	if ($lang == 'R') {
		return
			"--\n" .
			"С уважением,\n" .
			"ответственный секретарь журнала \"Компьютерная оптика\"\n" .
			"Дмитрий Викторович Кирш,\n" .
			"ko@smr.ru";
	} else {
		return
			"--\n" .
			"Sincerely yours,\n" .
			"Dmitriy Kirsch,\n" .
			"Executive Secretary of Computer Optics\n" .
			"ko@smr.ru";
	}
}

//Create a letter
function letters_create_letter($type, $ID_Article, $ID_Review = null, $ID_Version = null)
{
	global $wpdb;

	//Get email-receiver
	$ToWhom = null;
	if (mb_strpos($type, 'toE') === 0) { //ToEditor
		$ToWhom = $wpdb->get_row(
			"SELECT e.*
				FROM wp_ab_articles a 
					INNER JOIN wp_ab_sections s ON a.ID_Section=s.ID_Section
					INNER JOIN wp_ab_experts e ON s.ID_Editor=e.ID_Expert
				WHERE a.ID_Article={$ID_Article}"
		);
	} else if (mb_strpos($type, 'toR') === 0) { //ToReviewer
		$ToWhom = $wpdb->get_row(
			"SELECT 
					e.*,
					r.ID_Article
				FROM wp_ab_reviews r 
					INNER JOIN wp_ab_experts e ON r.ID_Expert=e.ID_Expert
				WHERE r.ID_Review={$ID_Review}"
		);
	} else if (mb_strpos($type, 'toA') === 0) { //ToAuthor
		$ToWhom = $wpdb->get_row(
			"SELECT e.*
				FROM wp_ab_experts e INNER JOIN wp_ab_articles a ON a.ID_CorAuthor=e.ID_Expert
				WHERE a.ID_Article={$ID_Article}"
		);
	}

	//Identify toReviewer true-type (first in life, first or second)
	if (mb_strpos($type, 'toR_A') === 0) {
		//Check if this is the first review in expert's life
		$inlife = $wpdb->get_var(
			"SELECT COUNT(r.ID_Review)
				FROM wp_ab_reviews r
				WHERE r.ID_Expert={$ToWhom->ID_Expert}"
		);
		//Check if this is the first review for this article
		$revno = $wpdb->get_var(
			"SELECT r.RevNo
				FROM wp_ab_reviews r
				WHERE r.ID_Review={$ID_Review}"
		);

		if ($inlife == 1) $type = 'toR_Inv';
		else if (mb_substr($revno, -2) === '.1') $type = 'toR_NewA';
		else $type = 'toR_SecA';
	}

	//Get letter template
	$template =  $wpdb->get_row(
		"SELECT *
		FROM wp_ab_letters l
		WHERE l.Type=\"{$type}\" AND l.Language=\"{$ToWhom->Language}\""
	);

	$text = $template->Text;
	$attachments = array();

	switch ($type) {
			//To editor new article
		case 'toE_NewA':
			$text = str_replace('%DATE%', date('d-m-Y', strtotime('+3 days')), $text);
			$text = str_replace('%LINK%', get_site_url() . '/articles/view/?id=' . $ID_Article, $text);

			$attachments[] = files_get_article_pdf($ID_Article);
			break;

			//To reviewer first review (or invitation)
		case 'toR_Inv':
		case 'toR_NewA':
			$text = str_replace('%DATE1%', date('d-m-Y', strtotime('+14 days')), $text);
			$text = str_replace('%DATE2%', date('d-m-Y', strtotime('+3 days')), $text);

			$attachments[] = files_get_article_pdf($ID_Article);
			$attachments[] = files_create_review_form($ID_Article, $ID_Review);
			break;

			//To reviewer second review
		case 'toR_SecA':
			$text = str_replace('%DATE%', date('d-m-Y', strtotime('+14 days')), $text);

			$attachments[] = files_get_article_pdf($ID_Article);

			//Get previous ID_Review
			$prevID = $wpdb->get_var(
				"SELECT r.ID_Review
					FROM wp_ab_reviews r
					WHERE r.ID_Expert={$ToWhom->ID_Expert} AND r.ID_Article={$ID_Article} AND r.ID_Review<>{$ID_Review}
					ORDER BY r.RevNo DESC"
			);

			$attachments[] = files_get_reply_pdf($ID_Article, $prevID);
			$attachments[] = files_create_review_form($ID_Article, $ID_Review);
			break;

			//To reviewer soft and hard reminders
		case 'toR_RemS':
		case 'toR_RemH':
			$toExpDate = $wpdb->get_var(
				"SELECT r.ToExpDate
					FROM wp_ab_reviews r
					WHERE r.ID_Review={$ID_Review}"
			);

			$text = str_replace('%DATE1%', date('d-m-Y', strtotime($toExpDate)), $text);
			$text = str_replace('%DATE2%', date('d-m-Y', strtotime($toExpDate . '+14 days')), $text);
			break;

			//To author receive first confirmation
		case 'toA_NewA':
			$lastVersionDate = $wpdb->get_var(
				"SELECT v.RecvDate
					FROM wp_ab_versions v
					WHERE v.ID_Article={$ID_Article}
					ORDER BY v.VerNo DESC"
			);
			$text = str_replace('%DATE%', date('d-m-Y', strtotime($lastVersionDate)), $text);
			break;

			//Send all sci/tech reviews
		case 'toA_Coms':
			if (sizeof($ID_Review) === 0) {
				$text = preg_replace('/{SCICOM}.*{SCICOM}/s', '', $text);
			} else {
				$text = str_replace('{SCICOM}', '', $text);

				$text = str_replace('%DATE%', date('d-m-Y', strtotime('+31 days')), $text);

				foreach ($ID_Review as $val) {
					$attachments[] = files_get_review_pdf($ID_Article, $val);
					$attachments[] = files_get_extra_pdf($ID_Article, $val);
					$attachments[] = files_create_reply_form($ID_Article, $val);
				}
			}

			if (is_null($ID_Version)) {
				$text = preg_replace('/{TECHCOM}.*{TECHCOM}/s', '', $text);
			} else {
				$comments = '';

				$TechComments = versions_get_version($ID_Version)->TechComments;

				$arr = explode("\n", $TechComments);

				if ($arr[0] === '' || strpos($arr[0], '%CompletelyOK%') !== false) { //If TechComments = null then $arr = array('')
					$text = preg_replace('/{TECHCOM}.*{TECHCOM}/s', '', $text);
				} else if (strpos($arr[0], '%CompletelyWrong%') !== false) {
					$comments .= '1. ' . getTechComString('%CompletelyWrong%', $ToWhom->Language);
				} else {
					global $COMTYPES;
					$impcomments = array_fill(0, sizeof($COMTYPES), '');
					$i = 0;
					for (; $i < sizeof($arr); $i++) {
						$line = $arr[$i];
						if (empty($line)) continue;

						for ($j = 0; $j < sizeof($COMTYPES); $j++) {
							$ctype = $COMTYPES[$j];
							if (strpos($line, $ctype) !== false) {
								$impcomments[$j] = getTechComString($ctype, $ToWhom->Language) . "\n";

								//If requires next line
								$doubleline = array('%NeedVector%', '%SeparateFiles%', '%BlackPictures%');
								if (in_array($ctype, $doubleline)) {
									$impcomments[$j] = getFigString($arr[$i + 1], $ToWhom->Language) . $impcomments[$j];
									$i++;
								}
								break;
							}
						}
						if (strpos($line, '%Others%') !== false) {
							$i++;
							break;
						}
					}
					//Order by importance
					$index = 1;
					for ($j = 0; $j < sizeof($COMTYPES); $j++)
						if (!empty($impcomments[$j])) {
							$comments .= $index . '. ' . $impcomments[$j];
							$index++;
						}

					for (; $i < sizeof($arr); $i++) {
						if (empty($arr[$i])) continue;
						$comments .= $index . '. ' . $arr[$i];
						$index++;
					}
				}

				$text = str_replace('%COMMENTS%', $comments, $text);
				$text = str_replace('{TECHCOM}', '', $text);
			}

			break;

			//To author reviews
		case 'toA_SciCom':
			$text = str_replace('%DATE%', date('d-m-Y', strtotime('+31 days')), $text);

			$attachments[] = files_get_review_pdf($ID_Article, $ID_Review);
			$attachments[] = files_get_extra_pdf($ID_Article, $ID_Review);
			$attachments[] = files_create_reply_form($ID_Article, $ID_Review);
			break;

			//To author soft and hard reminders
		case 'toA_RemS':
		case 'toA_RemH':
			$lastSentDate = $wpdb->get_var(
				"SELECT r.ToAuthDate as d
					FROM wp_ab_reviews r
                    WHERE r.ID_Article={$ID_Article}
                    UNION ALL
                    SELECT v.ToAuthDate as d
                    FROM wp_ab_versions v
                    WHERE v.ID_Article={$ID_Article}
					ORDER BY d DESC"
			);
			if (!is_null($lastSentDate))
				$text = str_replace('%DATE%', date('d-m-Y', strtotime($lastSentDate)), $text);
			break;

			//To author paper sci-accepted 
		case 'toA_SciApp':
			break;

		case 'toA_CamR':
			$text = str_replace('%DATE%', date('d-m-Y', strtotime('+2 days')), $text);

			$issue = $wpdb->get_var(
				"SELECT i.Title
					FROM wp_ab_issues i INNER JOIN wp_ab_articles a ON i.ID_Issue=a.ID_Issue
					WHERE a.ID_Article={$ID_Article}"
			);
			$text = str_replace('%ISSUE%', $issue, $text);

			break;

			//To author rejected
		case 'toA_Rej':
			$title = $wpdb->get_var(
				"SELECT a.Title
					FROM wp_ab_articles a
					WHERE a.ID_Article={$ID_Article}"
			);
			$text = str_replace('%TITLE%', $title, $text);

			//Add all last reviews
			$reviews = reviews_get_article_reviews_last($ID_Article);
			foreach ($reviews as $review) {
				$attachments[] = files_get_review_pdf($ID_Article, $review->ID_Review);
				$attachments[] = files_get_extra_pdf($ID_Article, $review->ID_Review);
			}
			break;

			//To author confirmation
		case 'toA_Conf':
			break;

			//To reviewer confirmation
		case 'toR_Conf':
			break;

			//To reviewer confirmation (exceptional)
		case 'toR_ConfExc':
			break;
	}

	//Remove all null-values from array
	$clearattachs = array();
	foreach ($attachments as $val) {
		if (!is_null($val)) $clearattachs[] = $val;
	}

	$text = str_replace('%HEADER%', getHeader($ToWhom->Language, $ToWhom->CallName), $text);
	$text = str_replace('%FOOTER%', getFooter($ToWhom->Language), $text);

	$letter = array();
	$letter['ToName'] = $ToWhom->Name;
	$letter['ToMail'] = $ToWhom->Mail;
	$letter['Subject'] = createSubject($ID_Article);
	$letter['Title'] = $template->Title;
	$letter['Text'] = $text;
	$letter['Attachments'] = $clearattachs;
	$letter['Type'] = $type;
	$letter['ID_Article'] = $ID_Article;
	$letter['ID_Review'] = $ID_Review;
	$letter['ID_Version'] = $ID_Version;

	return $letter;
}

//Get letter
add_action('wp_ajax_letters_get_letter_json', 'letters_get_letter_json');
function letters_get_letter_json()
{
	//Decode reviews if several
	$ID_Review = json_decode(stripslashes($_POST['ID_Review']), true);

	$letter = letters_create_letter(g_si($_POST['Type']), g_si($_POST['ID_Article']), $ID_Review, g_si($_POST['ID_Version']));
	echo g_ctj($letter);
	exit();
}

//Send letter ajax
add_action('wp_ajax_letters_send_json', 'letters_send_json');
function letters_send_json()
{
	//Decode reviews if several
	$_POST['ID_Review'] = json_decode(stripslashes($_POST['ID_Review']), true);

	//Upload new files
	$files = files_copy_uploaded_files_to_temp_TH($_FILES['files']);
	//Decode previously saved files from POST-string
	foreach ($_POST['Attachments'] as $file) {
		$files[] = json_decode(stripslashes($file), true);
	}
	$_POST['Attachments'] = $files;
	$_POST['Text'] = stripslashes($_POST['Text']);

	try {
		letters_send_TH($_POST);
		echo g_lev_j('Письмо отправлено', __FUNCTION__, $_POST['ToName'] . '_' . $_POST['Subject']);
	} catch (Exception $e) {
		echo g_ler_j('Ошибка отправления письма', __FUNCTION__, $e->getMessage());
	}
	exit();
}

//Create and send letter
function letters_create_and_send_TH($type, $ID_Article, $ID_Review = null, $ID_Version = null)
{
	letters_send_TH(letters_create_letter($type, $ID_Article, $ID_Review, $ID_Version));
}

//Send letter
function letters_send_TH($letter)
{
	global $LETTERTEMPLATE;
	$letter['TemplateFile'] = $LETTERTEMPLATE;

	$textFileName = getLetterTextFilename_TH();
	$letter['Text'] = stripslashes($letter['Text']); //Delete extra slashes from POST-transmission
	files_write_text_TH($letter['Text'], $textFileName, 'windows-1251');
	$letter['TextFile'] = $textFileName;

	addLetterToTheBAT_TH($letter);

	registerLetter($letter);
}

//Register letter sending
function registerLetter($letter)
{
	switch ($letter['Type']) {
			//To editor new article
		case 'toE_NewA':
			break;

			//To reviewer first review (or invitation)
		case 'toR_Inv':
		case 'toR_NewA':
			//To reviewer second review
		case 'toR_SecA':
			dbhandler_set_entity_TH('wp_ab_reviews', array('ToExpDate' => date('Y-m-d')), 'ID_Review', $letter['ID_Review']);
			g_lev('Статья на рецензию отправлена', __FUNCTION__, $letter['ID_Review']);
			break;

			//To reviewer soft and hard reminders
		case 'toR_RemS':
		case 'toR_RemH':
			dbhandler_set_entity_TH('wp_ab_reviews', array('RemDate' => date('Y-m-d')), 'ID_Review', $letter['ID_Review']);
			g_lev('Напоминание рецензенту направлено', __FUNCTION__, $letter['ID_Review']);
			break;

			//To author receive first confirmation
		case 'toA_NewA':
			break;

			//To author all reviews
		case 'toA_Coms':
			foreach ($letter['ID_Review'] as $val) {
				dbhandler_set_entity_TH('wp_ab_reviews', array('ToAuthDate' => date('Y-m-d')), 'ID_Review', $val);
			}
			dbhandler_set_entity_TH('wp_ab_versions', array('ToAuthDate' => date('Y-m-d')), 'ID_Version', $letter['ID_Version']);
			g_lev('Рецензии/техзамечания автору направлены', __FUNCTION__, $letter['ID_Article']);
			break;

			//To author review
		case 'toA_SciCom':
			dbhandler_set_entity_TH('wp_ab_reviews', array('ToAuthDate' => date('Y-m-d')), 'ID_Review', $letter['ID_Review']);
			g_lev('Рецензия автору направлена', __FUNCTION__, $letter['ID_Review']);
			break;

			//To author soft and hard reminders
		case 'toA_RemS':
		case 'toA_RemH':
			dbhandler_set_entity_TH('wp_ab_articles', array('RemDate' => date('Y-m-d')), 'ID_Article', $letter['ID_Article']);
			g_lev('Напоминание автору направлено', __FUNCTION__, $letter['ID_Article']);
			break;

			//To author paper sci-accepted 
		case 'toA_SciApp':
			break;

			//To author rejected
		case 'toA_Rej':
			dbhandler_set_entity_TH('wp_ab_articles', array('ID_Issue' => 1, 'FinalVerdictDate' => date('Y-m-d')), 'ID_Article', $letter['ID_Article']);
			g_lev('Статья отклонена', __FUNCTION__, $letter['ID_Article']);
			break;

			//To author confirmation
		case 'toA_Conf':
			break;

			//To reviewer confirmation
		case 'toR_Conf':
			break;

			//To reviewer confirmation (exceptional)
		case 'toR_ConfExc':
			break;
	}
}

//Get letter text file name and create its subfolder
function getLetterTextFilename_TH()
{
	global $LETTERSFOLDER;

	$subfolder = $LETTERSFOLDER . date('Y-m-d') . '/';
	files_make_directory_TH($subfolder);
	return $subfolder . date('H-i-s') . '_' . round(microtime(true) * 1000) . '.txt';
}

//Secure command parameter
function secureParamStr($str)
{
	$stops = array("\"", "'", "\\");
	return str_replace($stops, "", $str);
}

//Add letter to TheBat
function addLetterToTheBAT_TH($letter)
{

	//Template file
	if (isset($letter['Delay'])) {
		$filepath = "ini/template_{$letter['Delay']}.txt";
		if (!file_exists(files_get_absolute_path($filepath))) {
			$template = '%POSTPONE="' . $letter['Delay'] . 'm"';
			file_put_contents(files_get_absolute_path($filepath), mb_convert_encoding($template, 'windows-1251'));
		}
		$letter['TemplateFile'] = $filepath;
	}

	global $BATEXEC, $BATMAILOUTBOX;
	$command = "\"{$BATEXEC}\" /MAILF=\"{$BATMAILOUTBOX}\";";

	$command .= "TO=\"" . secureParamStr($letter['ToName']) . " <" . secureParamStr($letter['ToMail']) . ">\";";

	$command .=
		"S=\"" . secureParamStr($letter['Subject']) . "\";" .
		"C=\"" . files_get_absolute_path(secureParamStr($letter['TextFile'])) . "\";" .
		"T=\"" . files_get_absolute_path(secureParamStr($letter['TemplateFile'])) . "\";";


	for ($i = 0; $i < sizeof($letter['Attachments']); $i++) {
		$filepath = secureParamStr($letter['Attachments'][$i]['path']);
		if (isFileExists($filepath)) $command .= "A=\"" . files_get_absolute_path($filepath) . "\";";
		else throw new Exception("Файл не найден " . $filepath);
	}

	shell_exec($command);
}
