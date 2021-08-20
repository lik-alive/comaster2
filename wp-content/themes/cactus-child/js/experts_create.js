$(document).ready(function() {
	
	$('#name').on('keyup', function() {
		if (isEnglish(this.value)) $('#language').val('E');
		else $('#language').val('R');
	});
	
	$('#phone').on('keyup', function() {
		var val = this.value;
		if (val.length > 0) {
			if (val[0] === '+') val=val.substring(2);
			if (val[0] === '7') val=val.substring(1);
			if (val[0] === '8') val=val.substring(1);
		}
		this.value = val;
	});
	
	$('#createForm').submit(function (e) {
		e.preventDefault();
		
		//Load form data
		var fd = new FormData($('#createForm')[0]);
		fd.append('action', 'experts_create_json');
		
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