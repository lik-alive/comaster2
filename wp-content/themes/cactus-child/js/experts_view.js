var ID_Expert = searchParams.get('id');

$(document).ready(function() {
	//Load Expert data
	{	
		$.ajax({
			type: 'GET',
			url: ADMIN_URL + "?action=experts_get_expert_json&id="+ID_Expert,
			contentType: false,
			processData: false,
			success: function(response){
				var data = JSON.parse(response).data;
				if (data == null) showMsg([2, 'Эксперт не найден']);
				else {
					$('[name=Name]').html(data.Name);
					$('[name=CallName]').html(data.CallName);
					$('[name=Mail]').html(data.Mail);
					if (data.IsActive === 'Y') $('.panel-status').addClass('cool');
					else $('.panel-status').addClass('alarm');
					$('[name=IsActive]').html(data.IsActive === 'Y' ? 'Действующий' : 'Исключён');
					$('[name=Language]').html(data.Language === 'R' ? 'Русский' : 'Английский');
					$('[name=Interests]').html(data.Interests);
					$('[name=Comments]').html(data.Comments);
					$('[name=Position]').html(data.Position);
					$('[name=Phone]').html(data.Phone);
					$('[name=ActiveCount]').html(Math.round(data.ActiveCount) + ' статей'); //Null changes to 0 ^_^
					$('[name=TotalCount]').html(Math.round(data.TotalCount) + ' статей'); //Null changes to 0 ^_^
					$('[name=AvgDays]').html(Math.round(data.AvgDays) + ' дней');
				}
			}
		});
	}
	
	//Load scientific reviews data
	var articlestable = $('#articlestable').DataTable( {
		"bAutoWidth": false,
		"bInfo" : false,
		"bLengthChange": false,
		"serverSide": false,
		"processing": false,
		"pageLength": 25,	
		"pagingType": 'numbers',
		"language": {
			'emptyTable': "<div style='text-align: center; font-size:11pt;'>Статей нет</div>"
		} ,
		"ajax":{
			url: ADMIN_URL + "?action=experts_get_expert_articles_json&id="+ID_Expert,
			type: "post",
			dataType : "json",
			contentType: "application/json; charset=utf-8",
		} ,
		
		"order": [[ 0, "asc" ]],
		
		"columns": [
			{ "defaultContent": '', 'class': 'centered' },
			{ "data": "ID_Article" },
			{ "data": "AAuthors" },
			{ "data": "ATitle" },
			{ "data": "IIsActive" },
			{ "data": "ID_Verdict",
				"render": function (data, type, JsonResultRow, meta) {
					if ( type === 'sort') {
						//Разделение на актив и архив
						if (JsonResultRow.IIsActive === 'Y') {
							if (data == null) return 0;
							else if (data > 1 && data < 5) return data;
						}
						return data+100;
					}
					else {
						return data;
					}
				}
			},
			{ "data": "VTitle", 'class': 'centered' }
		],
		
		"columnDefs": [
			{
				"targets":  [1,4,5],
				"visible": false
			},
			{
				"targets":  [0,2,3,4,6],
				"orderable": false
			}
		],
		
		"drawCallback": function ( settings ) {			
			var api = this.api();
			var rows = api.rows();
			if (rows[0].length == 0) return;
			
			//Partitioning by sequence
			if (api.settings().order()[0][0] == '0') {
				//Invoke second call of the drawCallback
				api.order([5, 'asc'], [0, 'asc' ]).draw();
				return;
			}
			
			if (api.settings().order()[0][0] == '5') {
				var flag1 = true;
				var flag2 = true;
				for (var i = 0; i < rows.count(); i++) {
					var initID = rows[0][i]; //Row index in an unsorted table
					var curIsActive = api.cell(initID,4).data();
					var curIDV = api.cell(initID,5).data();
					
					if (curIsActive === 'Y' && (curIDV == null || (curIDV > 1 && curIDV < 5))) {
						if (flag1) {
							$(rows.nodes()).eq(i).before('<tr class="chapter"><td colspan="4" class="group">СТАТЬИ В РАБОТЕ</td></tr>');
							flag1 = false;
						}
						} else {
						if (flag2) {
							$(rows.nodes()).eq(i).before('<tr class="chapter"><td colspan="4" class="group">СТАТЬИ ОТРАБОТАННЫЕ</td></tr>');
							flag2 = false;
						}
					}
					
					//Colorize rows
					if (curIDV > 4) $(api.row(initID).node()).addClass('disabled');
				}
			}
			
			//Add numeration
			api.column(0).nodes().each( function (cell, i) { cell.innerHTML = i+1; } );
		}
	});
	
	InitMouseClick(articlestable, 1, '/articles/view/?id=');
});

function create() {
	window.location.href = SITE_URL + '/experts/create';
}
	
function edit() {
	window.location.href = SITE_URL + '/experts/edit/?id=' + ID_Expert;
}