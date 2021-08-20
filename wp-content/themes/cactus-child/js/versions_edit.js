var ID_Version = searchParams.get('id');
var pdfFM = null;

$(document).ready(function() {
	pdfFM = new FileManager(FileManagerOptions.OnlyPdf, FileManagerOptions.Upload, FileManagerOptions.Closeable);
	pdfFM.embedObject($('#pdffile'));
		
	//Load Version data
	{
		$.ajax({
			type: 'GET',
			url: ADMIN_URL + "?action=versions_get_version_json&id="+ID_Version,
			contentType: false,
			processData: false,
			success: function(response){
				var data = JSON.parse(response).data;
				if (data == null) showMsg([2, "Версия не найдена"]);
				else {
					$('[name=RecvDate]').val(data.RecvDate).change();
					$('[name=ToAuthDate]').val(data.ToAuthDate).change();
					pdfFM.addFiles(data.ArticlePdf);
				}
			}
		});
	}
	
	$('#createForm').submit(function (e) {
		e.preventDefault();
		//Load form data
		var fd = new FormData($('#createForm')[0]);
		fd.append('ID_Version', ID_Version);
		//Attach file
		//Uploaded file
		if (pdfFM.file instanceof File)
			fd.append('file', pdfFM.file);
		//Previously saved file
		//else 
			//fd.append('Saved[]', JSON.stringify(value));
		
		fd.append('action', 'versions_edit_json');
		
		$.ajax({
			type: 'POST',
			url: ADMIN_URL,
			data: fd, contentType: false, processData: false,
			success: function(response){
				var data = JSON.parse(response).data;
				if (data[0] == 2) showMsg(data);
				else window.history.back();
			}
		});
	});
});