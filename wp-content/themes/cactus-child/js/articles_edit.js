var ID_Article = searchParams.get('id');

$(document).ready(function() {
	//Load Article data
	{
		$.ajax({
			type: 'GET',
			url: ADMIN_URL + "?action=articles_get_article_json&id="+ID_Article,
			contentType: false,
			processData: false,
			success: function(response){
				var data = JSON.parse(response).data;
				if (data == null) showMsg([2, "Статья не найдена"]);
				else {
					$('[name=Title]').val(data.Title);
					$('[name=Authors]').val(data.Authors);
					$('[name=Affiliation]').val(data.Affiliation);
					$('[name=PageCount]').val(data.PageCount);
					$('[name=ID_Issue]').val(data.ID_Issue);
					$('[name=ID_Section]').val(data.ID_Section);
					$('[name=HasPriority]').attr('checked', data.HasPriority === 'Y');
					$('[name=RemDate]').val(data.RemDate).change();
					$('[name=FinalVerdictDate]').val(data.FinalVerdictDate).change();
					$('[name=CorAuthor]').val(data.CorName + ' <' + data.CorMail + '>');
					$('[name=ID_CorAuthor]').val(data.ID_CorAuthor);
				}
			}
		});
	}
	
	//Search by name or email
	$('#name').autocomplete({
		minLength: 3,
		source: function (request, resolve) {
			$.ajax({
				type: 'POST',
				url: ADMIN_URL + '?action=experts_search_json&kw='+encodeURIComponent(request.term),
				contentType: false,
				processData: false,
				success: function(response){
					var experts = [];
					var res = JSON.parse(response).data;
					for (var i = 0; i < res.length; i++) {
						var expert = res[i];
						experts.push({
							id: expert.ID_Expert,
							value: expert.Name + ' <' + expert.Mail + '>'
						});
					}
					resolve(experts);
				}
			});
		},
		select: function (event, ui) {
			$('[name=ID_CorAuthor]').val(ui.item.id);
		}
	});
	
	
	$('#createForm').submit(function (e) {
		e.preventDefault();
		//Load form data
		var fd = new FormData($('#createForm')[0]);
		fd.append('ID_Article', ID_Article);
		fd.append('action', 'articles_edit_json');
		
		$.ajax({
			type: 'POST',
			url: ADMIN_URL,
			data: fd,
			contentType: false,
			processData: false,
			success: function(response){
				var data = JSON.parse(response).data;
				if (data[0] == 2) showMsg(data);
				else window.location.href = SITE_URL + '/articles/view?id=' + ID_Article;
			}
		});
	});
}); 