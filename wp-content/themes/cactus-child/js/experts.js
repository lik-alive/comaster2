$(document).ready(function() {
	var datatable = $('#datatable').DataTable( {
		"bAutoWidth": false,
		"bInfo" : false,
		"bLengthChange": false,
		"serverSide": false,
		"processing": false,	
		"pageLength": 25,
		"displayStart": 25 * (listNo - 1),
		"pagingType": 'numbers',
		"language": {
			'emptyTable': "<div style='text-align: center; font-size:11pt;'>Статей нет</div>"
		} ,
		"ajax":{
			url: ADMIN_URL + "?action=experts_get_json",
			type: "post",
			dataType : "json",
			contentType: "application/json; charset=utf-8",
		} ,
		
		"order": [[ 4, "desc" ]],
		
		"columns": [
			{ "defaultContent": '', "className": 'centered' },
			{ "data": "ID_Expert"},
			{ "data": "Name"},
			{ "data": "Interests",
				"render": function (data, type, JsonResultRow, meta) {
					if (type === 'filter') return data;
					else {
						if (data !== null) return "<span class='cropped'>" + data + "</span>";
						return data;
					}
				}
			}, 
			{ "data": "ActiveCount", 'class': 'centered',
				"render": function (data, type, JsonResultRow, meta) {
					return Math.round(data);
				}
			},
			{ "data": "TotalCount", 'class': 'centered',
				"render": function (data, type, JsonResultRow, meta) {
					return Math.round(data);
				}
			},
			{ "data": "AvgDays", 'class': 'centered',
				"render": function (data, type, JsonResultRow, meta) {
					return Math.round(data);
				}
			}
		],
		
		"columnDefs": [
			{
				"targets":  [1],
				"visible": false
			},
			{
				"targets":  [0,1,4,5,6],
				"searchable": false
			},
			{
				"targets":  [0,3],
				"orderable": false
			},
			{
				"targets": [4,5,6],
				"orderSequence": ["desc", 'asc']
			}
		],
		
		"drawCallback": function ( settings ) {					
			var api = this.api();
			var rows = api.rows();
			if (rows[0].length == 0) return;
			
			//Add numeration
			api.column(0).nodes().each( function (cell, i) { cell.innerHTML = i+1; } );
		}
	});
	
	InitMouseClick(datatable, 1, '/experts/view/?id=');
	InitPagingStates(datatable);
});

function create() {
	window.location.href = SITE_URL + '/experts/create';
}