<div class='panel' >
	<div class='widget-title'><label>Статистика <?php echo main_get_user_section(); ?></label></div>
	<table class='borderless'>
		<tr><td>Всего статей:</td><td><b><?php echo main_get_nst_articles_count() ?></b></td></tr>
		<tr><td>Одобрено:</td><td><b><?php echo main_get_nst_articles_approved_count() ?></b></td></tr>
		<tr><td>Только науч.:</td><td><b><?php echo main_get_nst_articles_sciapproved_count() ?></b></td></tr>
		<tr><td>Только техн.:</td><td><b><?php echo main_get_nst_articles_techapproved_count() ?></b></td></tr>
	</table>
</div>