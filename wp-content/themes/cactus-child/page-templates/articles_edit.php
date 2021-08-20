<?php
	/*
		Template Name: Articles Edit
	*/
	get_header();
	wp_enqueue_script('articles_edit', get_template_directory_uri().'/js/articles_edit.js');
?>

<div class='main-container flex-container'>
	<div class='main-central'>
		<div class='panel'>
			<form id='createForm' method='post'>
				<fieldset>
					<legend>Информация о статье</legend>
					
					<div class='form-row' >
						<div class='form-name' >
							<label for='Title'><span class='required'>*</span>Название:</label>
						</div>
						<div class='form-value' >
							<input type='text' class='long' name='Title' value='' autocomplete='off' required />
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
							<input type='number' name='PageCount' value='' />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='ID_Issue'><span class='required'>*</span>Выпуск:</label>
						</div>
						<div class='form-value' >
							<select id='issues' name='ID_Issue' required >
							<?php
								$issues = tables_get_issues();
								echo "<option value=''>---</option>";
								foreach ($issues as $issue){
									echo "<option value='{$issue->ID_Issue}'>{$issue->Title}</option>";
								}
							?>
							</select>
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='ID_Section'><span class='required'>*</span>Раздел:</label>
						</div>
						<div class='form-value' >
							<select id='sections' class='long' name='ID_Section' required >
							<?php
								$sections = tables_get_sections();
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
							<label for='RemDate'>Напоминание:</label>
						</div>
						<div class='form-value' >
							<input type='date' name='RemDate' />
							<?php include 'tools/daypicker.php'; ?>
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='FinalVerdictDate'>Утверждено редактором:</label>
						</div>
						<div class='form-value' >
							<input type='date' name='FinalVerdictDate' value='' />
							<?php include 'tools/daypicker.php'; ?>
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='CorAuthor'><span class='required'>*</span>ФИО:</label>
						</div>
						<div class='form-value' >
							<input id='name' type='text' class='long' name='CorAuthor' value='' placeholder='Поиск по ФИО или email...' autocomplete='off' required />
							<input type='hidden' name='ID_CorAuthor' value='' />
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
					<span class="glyphicon glyphicon-ok"></span> Сохранить
				</button>
				<?php include 'tools/cancel-button.php'; ?>
			</div>
			<?php include 'tools/widget-logs.php'; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
