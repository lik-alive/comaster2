<script type="text/javascript">
	$(document).ready(function() {
		var widget_logtable = $('#widget-logtable').DataTable({
			"bAutoWidth": false,
			"bInfo": false,
			"bLengthChange": false,
			"serverSide": false,
			"processing": false,
			"paging": false,
			"ordering": false,
			"ajax": {
				url: ADMIN_URL + "?action=files_list_20logs_json",
				type: "post",
				dataType: "json",
				contentType: "application/json; charset=utf-8",
			},

			"columns": [{
					"data": "DateTime",
					"render": function(data, type, jrow, meta) {
						return '[' + jrow.DateTime + ']<br/>' +
							'%' + jrow.Name + '%<br/>' +
							jrow.Message;
					}
				},
				{
					"data": "Prefix"
				}
			],

			"columnDefs": [{
				"targets": [1],
				"visible": false
			}],

			"drawCallback": function(settings) {
				var api = this.api();
				var rows = api.rows();

				//Colorize rows
				for (var i = 0; i < api.rows().count(); i++) {
					var status = api.cell(i, 1).data();
					if (status == 'ERROR') $(api.row(i).node()).addClass('alarm');
					else $(api.row(i).node()).addClass('cool');
				}
			}
		});

		//Reload table after 1min
		function widget_logreload() {
			setTimeout(function() {
				widget_logtable.ajax.reload();
				widget_logreload();
			}, 60000);
		};
		widget_logreload();
	});
</script>

<?php if (g_cua('administrator')) { ?>
	<div class='panel'>
		<div class='widget-title'><label>Логи</label></div>

		<div style='height:300px; overflow-y:scroll;'>
			<table id='widget-logtable'></table>
		</div>
	</div>
<?php } ?>