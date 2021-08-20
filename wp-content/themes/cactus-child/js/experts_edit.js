var ID_Expert = searchParams.get('id');

$(document).ready(function() {
	//Load Expert data
	{	
		$.ajax({
			type: 'GET',
			url: ADMIN_URL + "?action=experts_get_expert_json&id="+ID_Expert,
			contentType: false,
			processData: false,
			success: function(response){
				var data = JSON.parse(response).data;
				if (data == null) showMsg([2, "Эксперт не найден"]);
				else {			
					$('[name=Name]').val(data.Name);
					$('[name=CallName]').val(data.CallName);
					$('[name=Mail]').val(data.Mail);
					$('[name=Language]').val(data.Language);
					$('[name=IsActive]').attr('checked', data.IsActive === 'Y');
					$('[name=Interests]').val(data.Interests);
					$('[name=Position]').val(data.Position);
					$('[name=Phone]').val(data.Phone);
					$('[name=Comments]').val(data.Comments);
				}
			}
		});
	}
	
	$('#name').on('keyup', function() {
		if (isEnglish(this.value)) $('#language').val('E');
		else $('#language').val('R');
	});
	
	$('#phone').on('keyup', function() {
		var val = this.value;
		if (val.length > 0) {
			if (val[0] === '8') val = '+7' + val.substring(1);
		}
		this.value = val;
	});
	
	$('#createForm').submit(function (e) {
		e.preventDefault();
		
		//Load form data
		var fd = new FormData($('#createForm')[0]);
		fd.append('ID_Expert', ID_Expert);
		fd.append('action', 'experts_edit_json');
		
		$.ajax({
			type: 'POST',
			url: ADMIN_URL,
			data: fd,
			contentType: false,
			processData: false,
			success: function(response){
				var data = JSON.parse(response).data;
				if (data[0] == 2) showMsg(data);
				else window.location.href = SITE_URL + '/experts/view/?id=' + data[2];
			}
		});
	});
}); 