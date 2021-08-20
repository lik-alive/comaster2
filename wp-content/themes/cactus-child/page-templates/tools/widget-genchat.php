<script type="text/javascript">
	$(document).ready(function() {
		var widget_genchattable = $('#widget-genchattable').DataTable( {
			"bAutoWidth": false,
			"bInfo" : false,
			"bLengthChange": false,
			"serverSide": false,
			"processing": false,
			"paging": false,
			"ordering": false,
			"ajax":{
				url: ADMIN_URL + "?action=dbhandler_get_20chats_json",
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
					+ JsonResultRow.Message + '<br/>'
					+ "<a href='articles/view/?id=" + JsonResultRow.ID_Article + "'><b><i>Перейти к статье</i></b></a>";
					}
			}
			]
		});
		
		//Reload table after 1min
		function widget_genchatreload(){
			setTimeout(function(){ 
				widget_genchattable.ajax.reload();
				widget_genchatreload();
			}, 60000);
		};
		widget_genchatreload();
	});
</script>

<div class='panel'>
	<div class='widget-title'><label>Последние сообщения</label></div>
	
	<div style='height:300px; overflow-y:scroll;'>
		<table id='widget-genchattable'></table>
	</div>
</div>