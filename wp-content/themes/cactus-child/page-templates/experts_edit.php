<?php
	/*
		Template Name: Experts Edit
	*/
	get_header();
	wp_enqueue_script('experts_edit', get_template_directory_uri().'/js/experts_edit.js');
?>

<div class='main-container flex-container'>
	<div class='main-central'>
		<div class='panel'>
			<form id='createForm' method='post'>
				<fieldset>
					<legend>Информация об эксперте</legend>
					<div class='form-row' >
						<div class='form-name' >
							<label for='Name'><span class='required'>*</span>ФИО:</label>
						</div>
						<div class='form-value' >
							<input id='name' type='text' class='long' name='Name' value='' placeholder='Фамилия Имя Отчество' autocomplete='off' required />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='CallName'><span class='required'>*</span>Обращение:</label>
						</div>
						<div class='form-value' >
							<input type='text' name='CallName' value='' placeholder='Уважаемый, ...' autocomplete='off' required />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='Mail'><span class='required'>*</span>E-Mail:</label>
						</div>
						<div class='form-value' >
							<input type='text' name='Mail' value='' autocomplete='off' required />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='Language'><span class='required'>*</span>Язык переписки:</label>
						</div>
						<div class='form-value' >
							<select id='language' name='Language' required ><option value='R'>Русский</option><option value='E'>Английский</option></select>
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='IsActive'>Действующий:</label>
						</div>
						<div class='form-value' >
							<input type='hidden' name='IsActive' value='N' />
							<input type='checkbox' name='IsActive' value='Y' />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='Interests'>Интересы:</label>
						</div>
						<div class='form-value' >
							<input type='text' class='long' name='Interests' value='' autocomplete='off' />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='Position'>Об эксперте:</label>
						</div>
						<div class='form-value' >
							<input type='text' class='long' name='Position' value='' autocomplete='off' placeholder='Уч. степень, организация, должность' />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='Phone'>Сотовый:</label>
						</div>
						<div class='form-value' >
							<input id='phone' type='text' name='Phone' value='' autocomplete='off' pattern='+[0-9]{11}' />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='Comments'>Комментарии:</label>
						</div>
						<div class='form-value' >
							<input type='text' class='long' name='Comments' value='' autocomplete='off' />
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
