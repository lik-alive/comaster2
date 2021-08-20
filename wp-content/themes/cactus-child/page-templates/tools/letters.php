<script type="text/javascript">
	var attachFM = null;
	var letter = null;
	
	function showLettersDialog(pars) {
		//Clear form
		$('#letterForm').find("input[type=text], textarea").val('');
		attachFM.clear();
		//Dissable send button
		$('#letterSendButton').attr('disabled', true);
	
		//Load form data
		var fd = new FormData();
		//Attach all single data
		for (var key in pars) {
			fd.append(key, pars[key]);
		}
		fd.append('action', 'letters_get_letter_json');
		
		//Load Letter data
		$.ajax({
			type: 'POST',
			url: ADMIN_URL,
			data: fd,
			contentType: false,
			processData: false,
			success: function(response){
				letter = JSON.parse(response).data;
				if (letter == null) showMsg([2, 'Форма письма не найдена']);
				else {
					$('#letterDialog .modal-title').html(letter.Title);
					$('#letterDialog [name=To]').val(letter.ToName + '<' + letter.ToMail + '>');
					$('#letterDialog [name=Subject]').val(letter.Subject);
					$('#letterDialog [name=Text]').val(letter.Text);
					
					attachFM.addFiles(letter.Attachments);
					
					//Enable send button
					$('#letterSendButton').attr('disabled', false);
					
					$('#letterDialog').modal({backdrop: 'static', keyboard: false});
				}
			}
		});
	}
	
	$(document).ready(function() {
		attachFM = new FileManager(FileManagerOptions.Multiple, FileManagerOptions.Upload, FileManagerOptions.Closeable);
		attachFM.embedObject($('#files'));
		
		$('#letterForm').submit(function (e) {
			e.preventDefault();
			
			//Load form data
			var fd = new FormData();
			//Attach all single data
			for ( var key in letter ) {
				if (typeof(letter[key]) !== 'object') {
					fd.append(key, letter[key]);
				} else if (key !== 'Attachments') {
					fd.append(key, JSON.stringify(letter[key]));
				}
			}
			//Replace text
			var text = $('[name=Text]').val();
			if (text.includes('%%')) {
				showMsg([2, 'Заполните все поля, содержащие %%%%%%%%%%']);
				return;
			}
			fd.set('Text', text);
			//Attach files
			for (var value of attachFM.files) {
				//Uploaded file
				if (value instanceof File)
					fd.append('files[]', value);
				//Previously saved file
				else 
					fd.append('Attachments[]', JSON.stringify(value));
			}
			fd.append('action', 'letters_send_json');
			
			$.ajax({
				type: 'POST',
				url: ADMIN_URL,
				data: fd,
				contentType: false,
				processData: false,
				success: function(response){
					showMsg(JSON.parse(response).data);
					$('#letterDialog').modal('toggle');
				}
			});
		});
	});
</script>

<div id='letterDialog' class='modal'>
	<div class='modal-content' style='width:80%'>
		<div class='modal-header'>
			<h4 class='modal-title' style='text-align: center' >Заголовок</h4>
		</div>
		<div class='modal-container flex-container'>
			<div class='main-central'>
				<div class='panel'>
					<form id='letterForm' method='post'>
						<fieldset>
							<div class='form-row' >
								<div class='form-name' >
									<label for='To'>Адресат:</label>
								</div>
								<div class='form-value' >
									<input type='text' class='long' name='To' value='' autocomplete='off' disabled />
								</div>
							</div>
							<div class='form-row' >
								<div class='form-name' >
									<label for='Subject'>Тема:</label>
								</div>
								<div class='form-value' >
									<textarea rows='3' name='Subject' disabled ></textarea>
								</div>
							</div>
							<textarea name='Text' rows='30'></textarea>
						</fieldset>
						<fieldset>
							<legend>Файлы</legend>
							<div class='form-row' >
								<div id='files'></div>
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
		<div class='modal-footer' style='text-align: center'>													
			<button id='letterSendButton' class="btn btn-success" style='width:120px' type='submit' form='letterForm' >
				<span class="glyphicon glyphicon-ok"></span> Отправить
			</button>
			<button class="btn btn-secondary" style='width:120px' type='button' data-dismiss='modal' >
				<span class="glyphicon glyphicon-remove"></span> Отмена
			</button>
		</div>
	</div>
</div>