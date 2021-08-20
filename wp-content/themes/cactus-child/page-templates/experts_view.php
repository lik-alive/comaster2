<?php
	/*
		Template Name: Experts View
	*/
	get_header(); 
	wp_enqueue_script('experts_view', get_template_directory_uri().'/js/experts_view.js');
?>

<div class='main-container flex-container'>
	<div class='main-central'>
		<div class='panel'>
			<div class='flex-container' >
				<div class='panel-title flex-float'>
					<label name='Name'/>
				</div>
				<div class='panel-status'>
					<label name='IsActive'/>
				</div>
			</div>
			
			<div class='form-row' >
				<div class='form-name'>E-Mail:</div>
				<div class='form-value'><label name='Mail'/></div>
			</div>
			<div class='form-row'>
				<div class='form-name'>Обращение:</div>
				<div class='form-value'><label name='CallName'/></div>
			</div>
			<div class='form-row' >
				<div class='form-name'>Язык переписки:</div>
				<div class='form-value'><label name='Language'/></div>
			</div>
			<div class='form-row'>
				<div class='form-name'>Интересы:</div>
				<div class='form-value'><label name='Interests'/></div>
			</div>
			<div class='form-row'>
				<div class='form-name'>Об эксперте:</div>
				<div class='form-value'><label name='Position'/></div>
			</div>
			<div class='form-row'>
				<div class='form-name'>Сотовый:</div>
				<div class='form-value'><label name='Phone'/></div>
			</div>
			<div class='form-row'>
				<div class='form-name'>Комментарии:</div>
				<div class='form-value'><label name='Comments'/></div>
			</div>
		</div>
		
		<div class='panel'>
			<div class='panel-title'><label>Статистика работы рецензента</label></div>
			
			<div class='form-row' >
				<div class='form-name'>В работе:</div>
				<div class='form-value'><label name='ActiveCount'></label></div>
			</div>
			<div class='form-row'>
				<div class='form-name'>За всё время:</div>
				<div class='form-value'><label name='TotalCount'></label></div>
			</div>
			<div class='form-row' >
				<div class='form-name'>Среднее время:</div>
				<div class='form-value'><label name='AvgDays'></label></div>
			</div>
		</div>
		
		<div class='panel'>
			<div class='panel-title'><label>Статьи на рецензию</label></div>
			
			<div class='scrollonoverflow'>
				<table id='articlestable' class='mydataTable' >
					<thead>
						<tr>
							<th style='width: 40px'>№</th>
							<th class='hidden'>IDA</th>
							<th style='width: 30%'>Авторы</th>
							<th>Название</th>
							<th class='hidden'>IsActive</th>
							<th class='hidden'>IDV</th>
							<th style='width: 80px'>Вердикт</th>
						</tr>
					</thead>										
				</table>
			</div>
		</div>
	</div>
	
	
	<div class='main-widgets'>
		<div class='sticky'>
			<div class='panel'>
				<div class='widget-title' ><label>Действия</label></div>
				<button type="button" class="btn btn-warning action" style='width:100%' onclick='create()' >
					<span class="glyphicon glyphicon-plus-sign"></span> Добавить эксперта
				</button>
				<button type="button" class="btn btn-primary action" style='width:100%' onclick='edit()' >
					<span class="glyphicon glyphicon-edit"></span> Редактировать
				</button>
			</div>
			<?php include 'tools/widget-logs.php'; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
