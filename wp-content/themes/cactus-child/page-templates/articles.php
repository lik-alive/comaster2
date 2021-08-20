<?php
	/*
		Template Name: Articles
	*/
	get_header(); 
	wp_enqueue_script('articles', get_template_directory_uri().'/js/articles.js');
?>

<div class='main-container flex-container'>
	<div class='main-central'>
		<div class='panel scrollonoverflow'>
			<table id='datatable' class='mydataTable' >
				<thead>
					<tr>
						<th style='min-width:40px; width:40px'>№</th>
						<th class='hidden'>ID</th>
						<th class='hidden'>IDi</th>
						<th class='hidden'>Iss</th>
						<th class='hidden'>IDs</th>
						<th class='hidden'>Sec</th>
						<th width='30%' >Авторы</th>
						<th>Название</th>
						<th class='hidden'>SeqNo</th>
						<th class='hidden'>Affiliation</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	
	
	<div class='main-widgets'>
		<div class='sticky'>
			<?php include 'tools/widget-quicksearch.php'; ?>
			<?php if (g_cua('administrator')) { ?>
			<div class='panel'>
				<div class='widget-title' ><label>Действия</label></div>
				<button type="button" class="btn btn-warning action" style='width:100%' onclick='create()' >
					<span class="glyphicon glyphicon-plus-sign"></span> Добавить статью
				</button>
			</div>
			<?php } ?>
			<?php include 'tools/widget-logs.php'; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
