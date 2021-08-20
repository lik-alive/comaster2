<?php
$FILESFOLDER = ABSPATH . 'files/';
$FILESURL = 'files/';
$REVIEWFORMRUS = 'ini/reviewRus.pdf';
$REVIEWFORMENG = 'ini/reviewEng.pdf';
$REPLYFORMRUS = 'ini/replyRus.pdf';
$REPLYFORMENG = 'ini/replyEng.pdf';
$TEMPDIR = 'temp/';

//-----General file functions

//Secure file name
function secureFileNaming($filename)
{
	$result = mb_ereg_replace('[ ]', '_', $filename);
	$result = mb_ereg_replace('[^а-яА-ЯёЁa-zA-Z0-9\-_.]', '', $result);
	return $result;
}

//Get filesystem path
function files_get_absolute_path($subpath)
{
	global $FILESFOLDER;
	return $FILESFOLDER . $subpath;
}

//Get url path
function files_get_url_path($subpath)
{
	global $FILESURL;
	return get_site_url(null, $FILESURL . $subpath);
}

//Check is file exists
function isFileExists($filename)
{
	if (is_null($filename) || empty($filename)) return false;

	global $FILESFOLDER;
	return file_exists($FILESFOLDER . $filename);
}

//Get file info
function getFileInfo($filename)
{
	if (isFileExists($filename)) return array(
		'name' => basename($filename),
		'size' => filesize(files_get_absolute_path($filename)),
		'path' => $filename
	);
	else return null;
}

//Get all files
function getAllFiles($subfolder)
{
	global $FILESFOLDER;
	$folder = files_get_absolute_path($subfolder);
	$result = array();
	$files = glob($folder . '*');
	foreach ($files as $file) {
		if (is_file($file)) $result[] = array(
			'name' => basename($file),
			'size' => filesize($file),
			'path' => mb_substr($file, mb_strlen($FILESFOLDER))
		);
	}
	return $result;
}

//Make directory
function files_make_directory_TH($subfolder)
{
	global $FILESFOLDER;
	if (!file_exists($FILESFOLDER . $subfolder)) {
		$res = mkdir($FILESFOLDER . $subfolder, 0777, true);
		if (!$res) throw new Exception("Ошибка создания папки '" . $FILESFOLDER . $subfolder . "'");
	}
}

//Write text to file
function files_write_text_TH($text, $filename, $encoding)
{
	global $FILESFOLDER;
	$result = file_put_contents(
		$FILESFOLDER . $filename,
		mb_convert_encoding($text, $encoding)
	);
	if (!$result) throw new Exception("Ошибка создания файла '{$filename}'");
}

//Copy file
function copyFile_TH($frompath, $topath)
{
	$fr = fopen($frompath, "r");
	if (!$fr) throw new Exception("Ошибка открытия файла '{$frompath}'");
	$contents = fread($fr, filesize($frompath));
	fclose($fr);

	$fw = fopen($topath, "w");
	if (!$fw) throw new Exception("Ошибка создания файла '{$topath}'");
	fwrite($fw, $contents);
	fclose($fw);
}

//Rearrange file array
function files_rearrange($files)
{
	$result = array();
	foreach ($files as $key1 => $value1)
		foreach ($value1 as $key2 => $value2)
			$result[$key2][$key1] = $value2;
	return $result;
}

//Copy uploaded files
function files_copy_uploaded_files_TH($uploaded, $subfolder)
{
	$folder = files_get_absolute_path($subfolder);
	$partCount = sizeof($uploaded['name']);

	files_make_directory_TH($subfolder);

	$files = array();
	for ($partNo = 0; $partNo < $partCount; $partNo++) {
		$filename = secureFileNaming($uploaded['name'][$partNo]);
		copyFile_TH($uploaded['tmp_name'][$partNo], $folder . $filename);
		$files[] = getFileInfo($subfolder . $filename);
	}

	return $files;
}

//Copy uploaded files to temp directory
function files_copy_uploaded_files_to_temp_TH($uploaded)
{
	global $TEMPDIR;
	return files_copy_uploaded_files_TH($uploaded, $TEMPDIR);
}

//Copy uploaded file
function files_copy_uploaded_file_TH($uploaded, $subfolder)
{
	$folder = files_get_absolute_path($subfolder);
	files_make_directory_TH($subfolder);
	copyFile_TH($uploaded['tmp_name'], $folder . secureFileNaming($uploaded['name']));
}

//Update file (delete old version)
function files_update_TH($type, $uploaded, $ids)
{
	switch ($type) {
		case 'version':
			$path = files_get_version_dir_naming($ids['ID_Article'], $ids['ID_Version']);
			$uploaded['name'] = files_get_article_naming($ids['ID_Article']) . '.pdf';
			clearFolder_TH($path);
			files_copy_uploaded_file_TH($uploaded, $path);
			break;
		case 'review':
			$path = files_get_review_dir_naming($ids['ID_Article'], $ids['ID_Review']);
			$uploaded['name'] = 'Review_' . getReviewNaming($ids['ID_Article'], $ids['ID_Review']) . '.pdf';
			clearFolder_TH($path, 'Review_*');
			files_copy_uploaded_file_TH($uploaded, $path);
			break;
		case 'extra':
			$path = files_get_review_dir_naming($ids['ID_Article'], $ids['ID_Review']);
			$uploaded['name'] = 'Extra_' . getReviewNaming($ids['ID_Article'], $ids['ID_Review']) . '.pdf';
			clearFolder_TH($path, 'Extra_*');
			files_copy_uploaded_file_TH($uploaded, $path);
			break;
		case 'reply':
			$path = files_get_review_dir_naming($ids['ID_Article'], $ids['ID_Review']);
			$uploaded['name'] = 'Reply_' . getReviewNaming($ids['ID_Article'], $ids['ID_Review']) . '.pdf';
			clearFolder_TH($path, 'Reply_*');
			files_copy_uploaded_file_TH($uploaded, $path);
			break;
	}
}

//Zip uploaded files
function files_zip_uploaded_files_TH($uploaded, $subfolder, $zipname)
{
	$folder = files_get_absolute_path($subfolder);
	$partCount = sizeof($uploaded['name']);

	files_make_directory_TH($subfolder);

	//Check if the file is already an archive
	$archives = array('rar', 'zip');
	$extension = mb_strtolower(pathinfo($uploaded['name'][0], PATHINFO_EXTENSION));
	if ($partCount === 1 && in_array($extension, $archives)) {
		copyFile_TH($uploaded['tmp_name'][0], $folder . str_replace('zip', $extension, $zipname));
		return;
	}

	$zip = new ZipArchive();
	$res = $zip->open($folder . $zipname, ZipArchive::CREATE);
	if ($res !== true) throw new Exception("Ошибка создания архива '{$folder}{$zipname}'");

	for ($partNo = 0; $partNo < $partCount; $partNo++) {
		$res = $zip->addFile($uploaded['tmp_name'][$partNo], secureFileNaming($uploaded['name'][$partNo]));
		if (!$res) throw new Exception("Ошибка добавления файла '" . secureFileNaming($uploaded['name'][$partNo]) . "' в архив");
	}

	$zip->close();
}

//Delete file
function deleteFile_TH($subfolder, $filename)
{
	$folder = files_get_absolute_path($subfolder);
	$file = $folder . $filename;
	if (is_file($file) && file_exists($file)) {
		$res = unlink($file);
		if (!$res) throw new Exception("Ошибка удаления файла '{$file}'");
	}
}

//Delete all files in the folder
function clearFolder_TH($subfolder, $mask = '*')
{
	$folder = files_get_absolute_path($subfolder);
	$files = glob($folder . $mask);
	foreach ($files as $file) {
		if (is_file($file)) {
			$res = unlink($file);
			if (!$res) throw new Exception("Ошибка удаления файла '{$file}'");
		}
	}
}

//Delete directory
function deleteDirectory_TH($subfolder)
{
	$folder = files_get_absolute_path($subfolder);
	if (file_exists($folder)) innerDeleteAll_TH($folder);
}

//Delete directory inner
function innerDeleteAll_TH($folder)
{
	global $GARR;
	$files = glob($folder . '*');
	foreach ($files as $file) {
		if (is_file($file)) {
			$res = unlink($file);
			if (!$res) throw new Exception("Ошибка удаления файла '{$file}'");
		} else innerDeleteAll_TH($file . '/');
	}
	$res = rmdir($folder);
	if (!$res) throw new Exception("Ошибка удаления папки '{$folder}'");
}

//-----Specialized file functions

//Get directory naming of the article's version
function files_get_version_dir_naming($ID_Article, $ID_Version)
{
	return 'articles/' . $ID_Article . '/versions/' . $ID_Version . '/';
}

//Get directory naming of the review
function files_get_review_dir_naming($ID_Article, $ID_Review)
{
	return 'articles/' . $ID_Article . '/reviews/' . $ID_Review . '/';
}

//Get naming for the article file
function files_get_article_naming($ID_Article)
{
	$article = dbhandler_get_entity('wp_ab_articles', 'ID_Article', $ID_Article);
	if (!$article) return null;
	if (mb_strpos($article->Title, ' ') === false) $title = $article->Title;
	else {
		$arr = explode(' ', $article->Title, 3);
		$title = $arr[0] . ' ' . $arr[1];
	}
	$result = secureFileNaming($ID_Article . '_' . $title);

	return $result;
}

//Get naming for the review file
function getReviewNaming($ID_Article, $ID_Review)
{
	$review = dbhandler_get_entity('wp_ab_reviews', 'ID_Review', $ID_Review);
	if (!$review) return null;
	$article = files_get_article_naming($ID_Article);
	return secureFileNaming("{$review->RevNo}_{$article}");
}

//Get article pdf-file url
add_action('wp_ajax_files_get_article_pdf_url_json', 'files_get_article_pdf_url_json');
function files_get_article_pdf_url_json()
{
	$ID_Article = g_si($_GET['id']);
	$file = files_get_article_pdf($ID_Article);
	if ($file) echo g_ctj(files_get_url_path($file['path']));
	else echo g_ctj(null);
	exit();
}

//Create review form
function files_create_review_form($ID_Article, $ID_Review)
{
	global $wpdb;
	$lang = $wpdb->get_var(
		"SELECT e.Language
			FROM wp_ab_experts e INNER JOIN wp_ab_articles a ON e.ID_Expert=a.ID_CorAuthor
			WHERE a.ID_Article={$ID_Article}"
	);

	global $REVIEWFORMRUS, $REVIEWFORMENG, $TEMPDIR;

	if ($lang === 'R') $form = $REVIEWFORMRUS;
	else $form = $REVIEWFORMENG;

	$fname = 'Review_' . getReviewNaming($ID_Article, $ID_Review) . '.pdf';
	try {
		copyFile_TH(files_get_absolute_path($form), files_get_absolute_path($TEMPDIR . $fname));
	} catch (Exception $e) {
		g_ler("Ошибка копирования файла " . files_get_absolute_path($form) . " в " . files_get_absolute_path($TEMPDIR . $fname), __FUNCTION__, $e->getMessage());
		return null;
	}

	return getFileInfo($TEMPDIR . $fname);
}

//Create reply form
function files_create_reply_form($ID_Article, $ID_Review)
{
	global $wpdb;
	$lang = $wpdb->get_var(
		"SELECT e.Language
			FROM wp_ab_experts e INNER JOIN wp_ab_articles a ON e.ID_Expert=a.ID_CorAuthor
			WHERE a.ID_Article={$ID_Article}"
	);

	global $REPLYFORMRUS, $REPLYFORMENG, $TEMPDIR;

	if ($lang === 'R') $form = $REPLYFORMRUS;
	else $form = $REPLYFORMENG;

	$fname = 'Reply_' . getReviewNaming($ID_Article, $ID_Review) . '.pdf';
	try {
		copyFile_TH(files_get_absolute_path($form), files_get_absolute_path($TEMPDIR . $fname));
	} catch (Exception $e) {
		g_ler("Ошибка копирования файла " . files_get_absolute_path($form) . " в " . files_get_absolute_path($TEMPDIR . $fname), __FUNCTION__, $e->getMessage());
		return null;
	}

	return getFileInfo($TEMPDIR . $fname);
}

//Get article pdf-file
function files_get_article_pdf($ID_Article)
{
	global $wpdb;
	$results = $wpdb->get_results(
		"SELECT v.ID_Version
			FROM wp_ab_versions v
			WHERE v.ID_Article={$ID_Article}
			ORDER BY VerNo DESC"
	);

	foreach ($results as $version) {
		$subfolder = files_get_version_dir_naming($ID_Article, $version->ID_Version);
		$files = getAllFiles($subfolder);

		foreach ($files as $file) {
			if (pathinfo($file['name'], PATHINFO_EXTENSION) === 'pdf') return $file;
		}
	}
	return null;
}

//Get article pdf-file of the specified version
function files_get_article_by_version_pdf($ID_Article, $ID_Version)
{
	$subfolder = files_get_version_dir_naming($ID_Article, $ID_Version);
	$files = getAllFiles($subfolder);

	foreach ($files as $file) {
		if (pathinfo($file['name'], PATHINFO_EXTENSION) === 'pdf') return $file;
	}

	return null;
}

//Get version pdf-file
function files_get_version_pdf($ID_Article, $ID_Version)
{
	$subfolder = files_get_version_dir_naming($ID_Article, $ID_Version);
	$files = getAllFiles($subfolder);

	foreach ($files as $file) {
		if (pathinfo($file['name'], PATHINFO_EXTENSION) === 'pdf') return $file;
	}
	return null;
}

//Get review pdf-file
function files_get_review_pdf($ID_Article, $ID_Review)
{
	$subfolder = files_get_review_dir_naming($ID_Article, $ID_Review);
	$files = getAllFiles($subfolder);

	foreach ($files as $file) {
		if (mb_strpos($file['name'], 'Review') === 0) return $file;
	}
	return null;
}

//Get review-extra pdf-file
function files_get_extra_pdf($ID_Article, $ID_Review)
{
	$subfolder = files_get_review_dir_naming($ID_Article, $ID_Review);
	$files = getAllFiles($subfolder);

	foreach ($files as $file) {
		if (mb_strpos($file['name'], 'Extra') === 0) return $file;
	}
	return null;
}

//Get reply pdf-file
function files_get_reply_pdf($ID_Article, $ID_Review)
{
	$subfolder = files_get_review_dir_naming($ID_Article, $ID_Review);
	$files = getAllFiles($subfolder);

	foreach ($files as $file) {
		if (mb_strpos($file['name'], 'Reply') === 0) return $file;
	}
	return null;
}
