<?php
	/*
		Template Name: Versions Edit
	*/
	get_header();
	wp_enqueue_script('versions_edit', get_template_directory_uri().'/js/versions_edit.js');
	wp_enqueue_script('file-manager', get_template_directory_uri().'/js/tools/file-manager.js');
?>

<div class='main-container flex-container'>
	<div class='main-central'>
		<div class='panel'>
			<form id='createForm' method='post'>
				<fieldset>
					<legend>Информация о версии</legend>
					<div class='form-row' >
						<div class='form-name' >
							<label for='RecvDate'>Прислана:</label>
						</div>
						<div class='form-value' >
							<input type='date' name='RecvDate' value='' />
							<?php include 'tools/daypicker.php'; ?>
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='ToAuthDate'>Автору:</label>
						</div>
						<div class='form-value' >
							<input type='date' name='ToAuthDate' value='' />
							<?php include 'tools/daypicker.php'; ?>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>Файлы</legend>
					<div class='form-row' >
						<div class='form-name' >
							<label for='PDFFile'>Статья (*.pdf):</label>
						</div>
						<div class='form-value' >
							<div id='pdffile'></div>
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
