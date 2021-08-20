<script type="text/javascript">
	function actCancelclick() {
		if (window.history.length > 1)
			window.history.back();
		else 
			window.close();
	}
</script>

<button class="btn btn-secondary action" style='width:100%' type='button' onclick='actCancelclick()' >
	<span class="glyphicon glyphicon-arrow-left"></span> Отменить
</button>