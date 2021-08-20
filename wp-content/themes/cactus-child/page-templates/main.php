<?php
/*
		Template Name: Main
	*/
get_header();
wp_enqueue_script('main', get_template_directory_uri() . '/js/main.js');
?>

<div class='main-container flex-container'>
	<div class='main-central'>
		<div class='panel scrollonoverflow'>
			<table id='datatable' class='mydataTable' style='min-width: 400px'>
				<thead>
					<tr>
						<th style='min-width:52px; width:52px'>№</th>
						<th class='hidden'>ID</th>
						<th class='hidden'>IDi</th>
						<th class='hidden'>Iss</th>
						<th class='hidden'>IDs</th>
						<th class='hidden'>Sec</th>
						<th width='20%'>Авторы</th>
						<th width='20%'>Название</th>
						<th class='hidden' style='min-width:40px; width:40px'>Стр.</th>
						<th class='hidden' style='min-width:40px; width:40px'>Срок</th>
						<th width='30%'>Статус</th>
						<th class='hidden'>Важность</th>
						<th class='hidden'>SeqNo</th>
						<th class='hidden'>Priority</th>
						<th class='hidden'>Affiliation</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>


	<div class='main-widgets'>
		<div class='sticky'>
			<?php include 'tools/widget-quicksearch.php'; ?>
			<?php include 'tools/widget-stat.php'; ?>
			<?php if (g_cua('administrator', 'jeditor', 'jteditor', 'jtech')) { ?>
				<div class='panel'>
					<div class='widget-title'><label>Отображение</label></div>
					<button id='statusView' type='button' class='btn btn-primary action' style='width:100%'>
						<span class="glyphicon glyphicon-exclamation-sign"></span> По статусу
					</button>
					<button id='orderView' type='button' class='btn btn-primary action' style='width:100%'>
						<span class="glyphicon glyphicon-th-list"></span> По номерам
					</button>
				</div>
			<?php } ?>
			<?php if (g_cua('administrator', 'jeditor')) { ?>
				<div class='panel'>
					<div class='widget-title'><label>Действия</label></div>
					<?php if (g_cua('administrator')) { ?>
						<button id='addArticleButton' type='button' class='btn btn-warning action' style='width:100%'>
							<span class="glyphicon glyphicon-plus-sign"></span> Добавить статью
						</button>
					<?php } ?>
					<button id='addIssueButton' type='button' class='btn btn-warning action' style='width:100%'>
						<span class="glyphicon glyphicon-plus-sign"></span> Добавить выпуск
					</button>
					<button id='archiveIssueButton' type='button' class='btn btn-danger action' style='width:100%'>
						<span class="glyphicon glyphicon-remove"></span> Архивировать выпуск
					</button>
				</div>
			<?php } ?>
			<?php if (g_cua('administrator')) { ?>
				<div class='panel'>
					<div class='widget-title'><label>Сервис</label></div>
					<button id='serviceAction' type="button" class="btn btn-primary action" style='width:100%'>
						<span class="glyphicon glyphicon-ok"></span> Сервис_1
					</button>
					<button id='service2Action' type="button" class="btn btn-primary action" style='width:100%'>
						<span class="glyphicon glyphicon-ok"></span> Сервис_2
					</button>
				</div>
			<?php } ?>

			<?php include 'tools/widget-genchat.php'; ?>
			<?php include 'tools/widget-logs.php'; ?>

		</div>
	</div>
</div>

<div id='addIssueDialog' class='modal'>
	<div class='modal-content' style='width:600px'>
		<div class='modal-header'>
			<h4 class='modal-title' style='text-align: center'>Добавление нового выпуска</h4>
		</div>
		<div class='modal-body'>
			<form id='addIssueForm'>
				<div class='form-row'>
					<div class='form-name'>
						<label for='Title'><span class='required'>*</span>Название:</label>
					</div>
					<div class='form-value'>
						<input type='text' class='long' name='Title' value='' autocomplete='off' required />
					</div>
				</div>
			</form>
		</div>
		<div class='modal-footer'>
			<button class="btn btn-success" style='width:120px' type='submit' form='addIssueForm'>
				<span class="glyphicon glyphicon-ok"></span> Создать
			</button>
			<button class="btn btn-secondary" style='width:120px' type='button' data-dismiss='modal'>
				<span class="glyphicon glyphicon-remove"></span> Отмена
			</button>
		</div>
	</div>
</div>

<?php include 'tools/confirm-dialog.php'; ?>

<div id='serviceDialog' class='modal'>
	<div class='modal-content' style='width:600px'>
		<div class='modal-header'>
			<h4 class='modal-title' style='text-align: center'>Сервис</h4>
		</div>
		<div class='modal-body'>
			<form id='serviceForm'>
				<textarea name='Pars' rows='7'></textarea>
			</form>
		</div>
		<div class='modal-footer'>
			<button class="btn btn-success" style='width:120px' type='submit' form='serviceForm'>
				<span class="glyphicon glyphicon-ok"></span> Сохранить
			</button>
			<button class="btn btn-secondary" style='width:120px' type='button' data-dismiss='modal'>
				<span class="glyphicon glyphicon-remove"></span> Отмена
			</button>
		</div>
	</div>
</div>

<div id='service2Dialog' class='modal'>
	<div class='modal-content' style='width:600px'>
		<div class='modal-header'>
			<h4 class='modal-title' style='text-align: center'>Сервис</h4>
		</div>
		<div class='modal-body'>
			<form id='service2Form'>
				<textarea name='Pars' rows='7'></textarea>
			</form>
		</div>
		<div class='modal-footer'>
			<button class="btn btn-success" style='width:120px' type='submit' form='service2Form'>
				<span class="glyphicon glyphicon-ok"></span> Сохранить
			</button>
			<button class="btn btn-secondary" style='width:120px' type='button' data-dismiss='modal'>
				<span class="glyphicon glyphicon-remove"></span> Отмена
			</button>
		</div>
	</div>
</div>

<?php get_footer(); ?>