<script>
	var confirmAction = null;
	
	//Show confirm dialog with the specified title
	function showConfirmDialog(title, action = null) {
		confirmAction = action;
		$('#confirmDialog .modal-title').html(title);
		$('#confirmDialog').modal('toggle');
	}
	
	//Autoclose modal window on form submit
	$(document).ready(function() {
		$('#confirmForm').submit(function(e) {
			e.preventDefault();
			if (confirmAction !== null) confirmAction();
			$('#confirmDialog').modal('toggle');
		});
	});
</script>

<div id='confirmDialog' class='modal'>
	<div class='modal-content' style='width:300px'>
		<div class='modal-header'>
			<h4 class='modal-title' style='text-align: center' >Заголовок</h4>
		</div>
		<form id='confirmForm'>
		</form>
		<div class='modal-footer' style='text-align: center'>													
			<button class="btn btn-success" style='width:120px' type='submit' form='confirmForm' >
				<span class="glyphicon glyphicon-ok"></span> Да
			</button>
			<button class="btn btn-secondary" style='width:120px' type='button' data-dismiss='modal' >
				<span class="glyphicon glyphicon-remove"></span> Нет
			</button>
		</div>
	</div>
</div>