<?php
	/*
		Template Name: Articles Create
	*/
	get_header();
	wp_enqueue_script('articles_create', get_template_directory_uri().'/js/articles_create.js');
	wp_enqueue_script('file-manager', get_template_directory_uri().'/js/tools/file-manager.js');
?>

<div class='main-container flex-container'>
	<div class='main-central'>
		<div class='panel'>
			<form id='createForm' method='post'>
				<fieldset>
					<legend>Информация о статье</legend>
					<textarea id='textArticle' rows='5' placeholder='Вставьте сюда шапку статьи для автораспознавания...' onkeyup='recognize();'></textarea>
					<div class='form-row' >
						<div class='form-name' >
							<label for='Title'><span class='required'>*</span>Название:</label>
						</div>
						<div class='form-value' >
							<textarea rows='3' type='text' class='long nolinebreaks' style='line-height:1.2' name='Title' value='' required ></textarea>
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='Authors'><span class='required'>*</span>Авторы:</label>
						</div>
						<div class='form-value' >
							<input type='text' class='long' name='Authors' value='' autocomplete='off' required />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='Affiliation'><span class='required'>*</span>Аффилиация:</label>
						</div>
						<div class='form-value' >
							<input type='text' class='long' name='Affiliation' value='' autocomplete='off' />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='PageCount'><span class='required'>*</span>Число страниц:</label>
						</div>
						<div class='form-value' >
							<input type='number' name='PageCount' value='' required />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='ID_Section'><span class='required'>*</span>Раздел:</label>
						</div>
						<div class='form-value' >
							<select id='sections' class='long' name='ID_Section' required >
							<?php
								$sections = tables_get_sections_active();
								echo "<option value=''>---</option>";
								foreach ($sections as $section){
									echo "<option value='{$section->ID_Section}'>{$section->Title}</option>";
								}
							?>
							</select>
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='HasPriority'>Приоритет:</label>
						</div>
						<div class='form-value' >
							<input type='hidden' name='HasPriority' value='N' />
							<input type='checkbox' name='HasPriority' value='Y' />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='RecvDate'><span class='required'>*</span>Дата получения:</label>
						</div>
						<div class='form-value' >
							<input type='date' name='RecvDate' value='' required />
							<?php include 'tools/daypicker.php'; ?>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>Информация об авторе для связи</legend>
					<div class='form-row' >
						<div class='form-name' >
							<label for='CorName'><span class='required'>*</span>ФИО:</label>
						</div>
						<div class='form-value' >
							<input id='name' type='text' class='long' name='CorName' value='' placeholder='Поиск по ФИО или e-mail...' autocomplete='off' required />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='CorMail'><span class='required'>*</span>E-mail:</label>
						</div>
						<div class='form-value' >
							<input id='mail' type='text' name='CorMail' value='' autocomplete='off'  required />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='CorCallName'><span class='required'>*</span>Обращение:</label>
						</div>
						<div class='form-value' >
							<input id='callname' type='text' name='CorCallName' value='' placeholder='Имя Отчество' autocomplete='off' required />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='CorLanguage'><span class='required'>*</span>Язык переписки:</label>
						</div>
						<div class='form-value' >
							<select id='language' name='CorLanguage' required ><option value='R'>Русский</option><option value='E'>Английский</option></select>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>Файлы</legend>
					<div class='form-row' >
						<div class='form-name' >
							<label for='PDFFile'><span class='required'>*</span>Статья (*.pdf):</label>
						</div>
						<div class='form-value' >
							<div id='pdffile'></div>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>Прочее</legend>
					<div class='form-row' >
						<div class='form-name' >
							<label for='IsToAuthor'>Письмо автору:</label>
						</div>
						<div class='form-value' >
							<input type='hidden' name='LetterToAuthor' value='N' />
							<input type='checkbox' name='LetterToAuthor' value='Y' checked />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='IsToEditor'>Письмо редактору:</label>
						</div>
						<div class='form-value' >
							<input type='hidden' name='LetterToEditor' value='N' />
							<input type='checkbox' name='LetterToEditor' value='Y' checked />
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
	
	<div class='main-widgets'>
		<div class='sticky'>
			<div class='panel'>
				<div class='widget-title' ><label>Действия</label></div>
				<button class="btn btn-success action" style='width:100%' type='submit' form='createForm' >
					<span class="glyphicon glyphicon-ok"></span> Добавить
				</button>
				<?php include 'tools/cancel-button.php'; ?>
			</div>
			<?php include 'tools/widget-logs.php'; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
