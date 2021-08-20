<script type="text/javascript">
	$(document).ready(function() {
		var widget_chattable = $('#widget-chattable').DataTable( {
			"bAutoWidth": false,
			"bInfo" : false,
			"bLengthChange": false,
			"serverSide": false,
			"processing": false,
			"paging": false,
			"ordering": false,
			"language": {
				'emptyTable': "<div style='text-align: center;'>Сообщений нет</div>"
			} ,
			"ajax":{
				url: ADMIN_URL + "?action=articles_get_chat_json&id=" + ID_Article,
				type: "post",
				dataType : "json",
				contentType: "application/json; charset=utf-8",
			},
			
			"columns": [
			{ "data": "DateTime", 
				"render": function (data, type, JsonResultRow, meta) {
					var name = JsonResultRow.display_name;
					var arr = name.split(' ');
					
					if (arr.length === 3) 
						name = arr[0][0] + '.' + arr[1][0] + '. ' + arr[2];
				
					return '[' + JsonResultRow.DateTime + ']<br/>'
					+ '<i> '+ name + '</i>:<br/>'
					+ JsonResultRow.Message;
				}
			}
			],
			
			"drawCallback": function ( settings ) {
				var api = this.api();
				var rows = api.rows();
			}
		});
		
		//Reload table after 1min
		function widget_chatreload(){
			setTimeout(function(){ 
				widget_chattable.ajax.reload();
				widget_chatreload();
			}, 60000);
		};
		widget_chatreload();
		
		$('#widget-chatsave').click(function() {
			var fd = new FormData($('#widget-chatmessage')[0]);
			fd.append('ID_Article', ID_Article);
			fd.append('action', 'articles_create_comment_json');
			
			$.ajax({
				type: 'POST',
				url: ADMIN_URL,
				data: fd, contentType: false, processData: false,
				success: function(response){
					var data = JSON.parse(response).data;
					showMsg(JSON.parse(response).data);
					if (data[0] == 1) {
						$('#widget-chatmessage [name=Message]').val('');
						widget_chattable.ajax.reload();
					}
				}
			});
		});
	});
</script>

<div class='panel'>
	<div class='widget-title'><label>Чат</label></div>
	
	<div style='min-height:100px; max-height:300px; overflow-y:auto;'>
		<table id='widget-chattable'></table>
	</div>
	<br/>
	<form id='widget-chatmessage'>
		<textarea rows='5' style='font-size:10pt;line-height:1.2' name='Message' value='' autocomplete='off'></textarea>
		
		<button id='widget-chatsave' type='button' class='btn btn-success action long'>
			<span class="glyphicon glyphicon-floppy-disk"></span> Сохранить
		</button>
	</form>
</div>