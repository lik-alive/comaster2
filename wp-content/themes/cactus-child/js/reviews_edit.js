var ID_Review = searchParams.get('id');

$(document).ready(function() {
	//Load Review data
	{
		$.ajax({
			type: 'GET',
			url: ADMIN_URL + "?action=reviews_get_review_json&id="+ID_Review,
			contentType: false,
			processData: false,
			success: function(response){
				var data = JSON.parse(response).data;
				if (data == null) showMsg([2, "Рецензия не найдена"]);
				else {
					$('[name=ATitle]').val(data.ATitle);
					$('[name=EName]').val(data.EName);
					$('[name=ToExpDate]').val(data.ToExpDate).change();
					$('[name=FromExpDate]').val(data.FromExpDate).change();
					$('[name=ID_Verdict]').val(data.ID_Verdict);
					$('[name=Quality]').val(data.Quality);
					$('[name=ToAuthDate]').val(data.ToAuthDate).change();
					$('[name=FromAuthDate]').val(data.FromAuthDate).change();
					$('[name=RemDate]').val(data.RemDate).change();
				}
			}
		});
	}
	
	$('#createForm').submit(function (e) {
		e.preventDefault();
		//Load form data
		var fd = new FormData($('#createForm')[0]);
		fd.append('ID_Review', ID_Review);
		fd.append('action', 'reviews_edit_json');
		
		$.ajax({
			type: 'POST',
			url: ADMIN_URL,
			data: fd,
			contentType: false,
			processData: false,
			success: function(response){
				var data = JSON.parse(response).data;
				if (data[0] == 2) showMsg(data);
				else window.history.back();
			}
		});
	});
	
	$('#deleteEntity').click(function () {
		var fd = new FormData();
		fd.append('ID_Review', ID_Review);
		fd.append('action', 'reviews_delete_json');
		
		$.ajax({
			type: 'POST',
			url: ADMIN_URL,
			data: fd,
			contentType: false,
			processData: false,
			success: function(response){
				var data = JSON.parse(response).data;
				if (data[0] == 2) showMsg(data);
				else window.history.back();
			}
		});
	});
});