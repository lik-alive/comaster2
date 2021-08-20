<?php
	/*
		Template Name: Reviews Edit
	*/
	get_header();
	wp_enqueue_script('reviews_edit', get_template_directory_uri().'/js/reviews_edit.js');
?>

<div class='main-container flex-container'>
	<div class='main-central'>
		<div class='panel'>
			<form id='createForm' method='post'>
				<fieldset>
					<legend>Информация о рецензии</legend>
					
					<div class='form-row' >
						<div class='form-name' >
							<label for='ATitle'>Статья:</label>
						</div>
						<div class='form-value' >
							<input type='text' class='long' name='ATitle' disabled />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='EName'>Рецензент:</label>
						</div>
						<div class='form-value' >
							<input type='text' class='long' name='EName' disabled />
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='ToExpDate'>Послано рецензенту:</label>
						</div>
						<div class='form-value' >
							<input type='date' name='ToExpDate' value='' />
							<?php include 'tools/daypicker.php'; ?>
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='FromExpDate'>Ответ от рецензента:</label>
						</div>
						<div class='form-value' >
							<input type='date' name='FromExpDate' value='' />
							<?php include 'tools/daypicker.php'; ?>
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='ID_Verdict'>Вердикт:</label>
						</div>
						<div class='form-value' >
							<select name='ID_Verdict'>
								<?php
									$verdicts = tables_get_verdicts();
									echo "<option value=''>---</option>";
									foreach ($verdicts as $verdict){
										echo "<option value='{$verdict->ID_Verdict}'>{$verdict->Title}</option>";
									}
								?>
							</select>
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='Quality'>Качество:</label>
						</div>
						<div class='form-value' >
							<select name='Quality'>
								<option value=''>---</option>
								<option value='1'>отписка</option>
								<option value='2'>хорошо</option>
								<option value='3'>превосходно</option>
							</select>
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='ToAuthDate'>Послано автору:</label>
						</div>
						<div class='form-value' >
							<input type='date' name='ToAuthDate' value='' />
							<?php include 'tools/daypicker.php'; ?>
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='FromAuthDate'>Ответ от автора:</label>
						</div>
						<div class='form-value' >
							<input type='date' name='FromAuthDate' value='' />
							<?php include 'tools/daypicker.php'; ?>
						</div>
					</div>
					<div class='form-row' >
						<div class='form-name' >
							<label for='RemDate'>Напоминание:</label>
						</div>
						<div class='form-value' >
							<input type='date' name='RemDate' value='' />
							<?php include 'tools/daypicker.php'; ?>
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
				<button id='deleteEntity' class="btn btn-danger action" style='width:100%' type='button' >
					<span class="glyphicon glyphicon-remove"></span> Удалить
				</button>
			</div>
			<?php include 'tools/widget-logs.php'; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
