<?php
	/*
		Template Name: Experts
	*/
	get_header(); 
	wp_enqueue_script('experts', get_template_directory_uri().'/js/experts.js');
	?>

<div class='main-container flex-container'>
	<div class='main-central'>
		<div class='panel scrollonoverflow'>
			<table id='datatable' class='mydataTable' style='min-width:800px' >
				<thead>
					<tr>
						<th style='min-width:40px; width:40px'>№</th>
						<th class='hidden'>ID</th>
						<th style='min-width:300px; width:300px'>Имя</th>
						<th>Интересы</th>
						<th style='min-width:85px; width:85px'>В работе</th>
						<th style='min-width:70px; width:70px'>Всего</th>
						<th style='min-width:80px; width:80px'>Ср.срок</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	
	
	<div class='main-widgets'>
		<div class='sticky'>
			<?php include 'tools/widget-quicksearch.php'; ?>
			<div class='panel'>
				<div class='widget-title' ><label>Действия</label></div>
				<button type="button" class="btn btn-warning" style='width:100%' onclick='create()' >
					<span class="glyphicon glyphicon-plus-sign action"></span> Добавить эксперта
				</button>
			</div>
			<?php include 'tools/widget-logs.php'; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
