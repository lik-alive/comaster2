$(document).ready(function () {
  var datatable = $("#datatable").DataTable({
    bAutoWidth: false,
    bInfo: false,
    bLengthChange: false,
    serverSide: false,
    processing: false,
    pageLength: 25,
    displayStart: 25 * (listNo - 1),
    pagingType: "numbers",
    language: {
      emptyTable:
        "<div style='text-align: center; font-size:11pt;'>Статей нет</div>"
    },
    ajax: {
      url: ADMIN_URL + "?action=articles_get_json",
      type: "post",
      dataType: "json",
      contentType: "application/json; charset=utf-8"
    },

    order: [[0, "asc"]],

    columns: [
      { defaultContent: "", className: "centered" },
      { data: "ID_Article" },
      { data: "ID_Issue" },
      { data: "ITitle" },
      { data: "ID_Section" },
      { data: "STitle" },
      {
        data: "Authors",
        render: function (data, type, JsonResultRow, meta) {
          if (type === "filter") return data;
          else return "<span class='cropped'>" + data + "</span>";
        }
      },
      {
        data: "Title",
        render: function (data, type, JsonResultRow, meta) {
          if (type === "filter") return data;
          else return "<span class='cropped'>" + data + "</span>";
        }
      },
      { data: "SeqNumber" },
      { data: "Affiliation" }
    ],

    columnDefs: [
      {
        targets: [1, 2, 3, 4, 5, 8, 9],
        visible: false
      },
      {
        targets: [0, 1, 2, 4, 8],
        searchable: false
      },
      {
        targets: [6, 7],
        orderable: false
      }
    ],

    drawCallback: function (settings) {
      var api = this.api();
      var rows = api.rows();
      var frows = api.rows({ filter: "applied" }); //After search apply
      if (rows[0].length == 0) return;

      //Partitioning by sequence
      if (api.settings().order()[0][0] == "0") {
        //Invoke second call of the drawCallback
        api.order([2, "desc"], [4, "asc"], [8, "asc"], [0, "asc"]).draw(false);
        return;
      }

      //Add numeration
      if (api.settings().order()[0][0] == "2") {
        var lastIssue = "";
        var i = 0;
        var start = 0;
        while (i < rows.count()) {
          var initID = rows[0][i]; //Row index in an unsorted table
          var curIssue = api.cell(initID, 3).data();

          if (i === 0 || curIssue !== lastIssue) {
            lastIssue = curIssue;
            start = i;
          }

          api.cell(rows[0][i], 0).node().innerHTML = i - start + 1;
          i++;
        }
      }

      //Insert lines after partitioning
      if (api.settings().order()[0][0] == "2") {
        var lastIssue;
        var lastSection;
        for (var i = 0; i < frows.count(); i++) {
          var initID = frows[0][i]; //Row index in an unsorted table
          var curIssue = api.cell(initID, 3).data();
          var curSection = api.cell(initID, 5).data();

          if (i == 0 || curIssue !== lastIssue) {
            lastIssue = curIssue;
            $(frows.nodes())
              .eq(i)
              .before(
                '<tr class="chapter"><td colspan="3" class="group">СТАТЬИ В ВЫПУСК \'' +
                  curIssue.toUpperCase() +
                  "'</td></tr>"
              );
            lastSection = "";
          }

          if (curSection != lastSection) {
            lastSection = curSection;
            $(frows.nodes())
              .eq(i)
              .before(
                '<tr class="subchapter"><td colspan="3" class="group">' +
                  curSection +
                  "</td></tr>"
              );
          }
        }
      }
    }
  });

  InitMouseClick(datatable, 1, "/articles/view/?id=");
  InitPagingStates(datatable);
});

function create() {
  window.location.href = SITE_URL + "/articles/create";
}
