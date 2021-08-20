function getType(c) {
	if (c === ',') return 2;
	if (c === '.') return 3;
	if (c === ' ') return 4;
	if (!isNaN(c)) return 1;
	
	return 0;
}

function formatAuthorsNew(str) {
	var authors = '';
	
	str = str.replace(/[0-9]/g, '');
	var arr = str.split(',');
	
	for (var i = 0; i < arr.length; i++) {
		var tmp = arr[i].trim();
		if (tmp.length === 0) continue;
		
		tmp = tmp.replace(/[.]/g, '. ');
		
		var arr1 = tmp.split(' ');
		var author = '';
		for (var j = 0; j < arr1.length; j++) {
			if (arr1[j].length === 0) continue;
			
			if (arr1[j].includes('.')) author += arr1[j];
			else author = arr1[j] + ' ' + author;
		}
		if (authors.length > 0) authors += ', ';
		authors += author;
	}
	
	return authors;
}

function recognize(){		
	var str = $('#textArticle').val();
	//Remove double spaces
	while (str.includes('  ')) str = str.replace('  ', ' ');
	
	var array = str.split('\n');
	var i = 0;
	
	//Title
	if (i == array.length) return;
	var title = '';
	while (i < array.length) {
		if (i == 0 || array[i] === array[i].toUpperCase()) {
			title += array[i].trim() + ' ';
			i++;
		}
		else break;
	}
	if (title === title.toUpperCase()) title = title[0] + title.substr(1).toLowerCase();
	title = title.trim();
	$('[name=Title]')[0].value = title;
	//Update language based on title
	if (isEnglish(title)) $('#language').val('E');
	else $('#language').val('R');
	
	//Authors
	var authors = '';
	if (i < array.length) {
		authors = array[i];
		
		//Format authors
		authors = authors.replace(/[^a-zA-Zа-яА-ЯёЁ0-9 .,\-']/g, '');
		while (authors.includes('  ')) authors = authors.replace('  ', ' ');
		authors = authors.replace(' and ', ' ');
		
		authors = formatAuthorsNew(authors);
		
		i++;
	}
	$('[name=Authors]')[0].value = authors;
	
	//Affiliation
	var affiliation = '';
	while (i < array.length) {
		if (i === array.length-1 && !isNaN(array[i])) break;
		affiliation += array[i].trim() + ' ';
		i++;
	}
	$('[name=Affiliation]')[0].value = affiliation;
	
	//PageCount
	var pageCount = '';
	if (i < array.length) {
		pageCount = array[i];
	}
	$('[name=PageCount]')[0].value = pageCount;
}

$(document).ready(function() {
	var pdfFM = new FileManager(FileManagerOptions.Upload, FileManagerOptions.OnlyPdf, FileManagerOptions.Closeable);
	pdfFM.embedObject($('#pdffile'));
	
	var authLoaded = false;
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
							label: expert.Name + ' <' + expert.Mail + '>', 
							name: expert.Name, 
							mail: expert.Mail,
							callname: expert.CallName,
							language: expert.Language,
							value: expert.Name
						});
					}
					resolve(experts);
				}
			});
		},
		select: function (event, ui) {
			$('#mail').val(ui.item.mail);
			$('#callname').val(ui.item.callname);
			$('#language').val(ui.item.language);
			authLoaded = true;
		}
	});
	
	$('#name').on('keyup', function() {
		var fio = $(this).val();
		var callname = '';
		if (fio.indexOf(' ') !== -1) callname = fio.substr(fio.indexOf(' ') + 1);
		$('#callname').val(callname);
	});
	
	$('#mail').on('keyup', function() {
		var str = this.value;
		if (str.indexOf('<') !== -1) str = str.substr(str.indexOf('<') + 1);
		if (str.indexOf('>') !== -1) str = str.substr(0, str.indexOf('>'));
		$(this).val(str);
	});
	
	$('#createForm').submit(function (e) {
		e.preventDefault();
		//Load form data
		var fd = new FormData($('#createForm')[0]);
		//Load PDF file
		if (pdfFM.filesCount === 0) {
			showMsg([2, 'Загрузите PDF-файл статьи']);
			return;
		}
		fd.append('file', pdfFM.file);
		fd.append('action', 'articles_create_json');
		
		$.ajax({
			type: 'POST',
			url: ADMIN_URL,
			data: fd,
			contentType: false,
			processData: false,
			success: function(response){
				var data = JSON.parse(response).data;
				if (data[0] == 2) showMsg(data);
				else window.location.href = SITE_URL + '/articles/view?id=' + data[2];
			}
		});
	});
}); 