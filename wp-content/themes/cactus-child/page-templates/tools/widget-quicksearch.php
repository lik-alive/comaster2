<script type="text/javascript">
	$(document).ready(function() {
		$('#widget-quicksearch-input').on('focus', function () {
			$(this).parent().css({'border': '1px solid var(--action-active)'});
			$('#qsicon').attr("src", TEMPLATE_URL + '/resources/search_blue.png');
		} );
		
		$('#widget-quicksearch-input').on('blur', function () {
			$(this).parent().css({'border': '1px solid var(--panel-border)'});
			$('#qsicon').attr("src", TEMPLATE_URL + '/resources/search.png');
		} );
		
		$('#widget-quicksearch-button').on('click', function () {
			$('#widget-quicksearch-input').focus();
		} );
		
		//Autofocus on the field
		$('#widget-quicksearch-input').focus();
		
		//Layout Switcher
		$('#widget-quicksearch-input').on('keyup', function () {
			var datatable = $('#datatable').DataTable();
			var val = this.value;
			var valC = changeKeyboardLayout(val);
			
			datatable.search(escapeRegExp(val) + '|' + escapeRegExp(valC), true, false);
			datatable.draw();
		} );
		
		//Autoclear after history-back
		if ($('#widget-quicksearch-input').val() !== '') $('#widget-quicksearch-input').val('');
	});
</script>

<div class='panel'>
	<div class='widget-title'><label>Быстрый поиск</label></div>
	
	<div id='widget-quicksearch' >
		<button id='widget-quicksearch-button'><img id='qsicon' src='<?php echo get_template_directory_uri() ?>/resources/search.png'/></button>
		<input id='widget-quicksearch-input' type="text" placeholder="Search...">
	</div>
</div>