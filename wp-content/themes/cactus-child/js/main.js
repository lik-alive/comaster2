var ptype = searchParams.get("type");

//-----Main table
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
      url: ADMIN_URL + "?action=main_get_nst_json",
      type: "post",
      dataType: "json",
      contentType: "application/json; charset=utf-8"
    },

    //"order": [[ 0, "asc" ]], order
    //"order": [[ 8, "asc" ]], importance

    columns: [
      { defaultContent: "", className: "centered" },
      { data: "ID_Article" },
      {
        data: "ID_Issue",
        render: function (data, type, JsonResultRow, meta) {
          if (type === "sort") {
            //Портфель - в конец
            if (data == 3) return Number.MAX_SAFE_INTEGER;
            else return data;
          } else {
            return data;
          }
        }
      },
      { data: "ITitle" },
      { data: "ID_Section" },
      { data: "STitle" },
      {
        data: "Authors",
        render: function (data, type, JsonResultRow, meta) {
          if (type === "filter") return data;
          else {
            if (JsonResultRow.HasPriority == "Y")
              data =
                "<span class='glyphicon glyphicon-fire priority' title='Особый приоритет'></span> " +
                data;
            return "<span class='cropped'>" + data + "</span>";
          }
        }
      },
      {
        data: "Title",
        render: function (data, type, JsonResultRow, meta) {
          if (type === "filter") return data;
          else {
            return "<span class='cropped'>" + data + "</span>";
          }
        }
      },
      { data: "PageCount" },
      { data: "Days" },
      { data: "Status" },
      {
        data: "PIndex",
        render: function (data, type, JsonResultRow, meta) {
          if (type === "sort") {
            //Для порядка (совещания) принятые статьи - вперёд
            if (ptype === "reorder" && data > 10 && data <= 20)
              return data + 100;
          }
          return data;
        }
      },
      { data: "SeqNumber" },
      { data: "HasPriority" },
      { data: "Affiliation" }
    ],

    columnDefs: [
      {
        targets: [1, 2, 3, 4, 5, 8, 9, 11, 12, 13, 14],
        visible: false
      },
      {
        targets: [0, 1, 2, 3, 4, 5, 8, 9, 11, 12, 13],
        searchable: false
      },
      {
        targets: [0, 6, 7, 8, 9, 10],
        orderable: false
      },
      {
        targets: [0, 10],
        orderSequence: ["asc"]
      }
    ],

    drawCallback: function (settings) {
      var api = this.api();
      var rows = api.rows();
      var frows = api.rows({ filter: "applied" }); //After search apply
      if (rows[0].length == 0) return;

      //Autoopen on a single entry
      if (frows[0].length == 1)
        window.location.href =
          SITE_URL + "/articles/view/?id=" + api.cell(frows[0][0], 1).data();

      //Repartition
      if (api.settings().order().length === 1) {
        //Partitioning by importance
        if (api.settings().order()[0][0] === 10) {
          api
            .order(
              [11, "desc"],
              [2, "asc"],
              [4, "asc"],
              [13, "desc"],
              [10, "asc"]
            )
            .draw(false);
          return;
        }
        //Partitioning by sequence
        if (api.settings().order()[0][0] === 0) {
          //Invoke second call of the drawCallback
          api
            .order(
              [2, "asc"],
              [4, "asc"],
              [12, "asc"],
              [11, "desc"],
              [9, "desc"],
              [0, "asc"]
            )
            .draw(false);
          return;
        }
      }

      //Add numeration
      if (api.settings().order()[0][0] == "2") {
        var lastIssue = "";
        var lastSection = "";
        var sectionLen = 0;
        var i = 0;
        var offset = 0;
        while (i < rows.count()) {
          var initID = rows[0][i]; //Row index in an unsorted table
          var curIssue = api.cell(initID, 3).data();
          var curSection = api.cell(initID, 5).data();

          if (i === 0 || curIssue !== lastIssue) {
            lastIssue = curIssue;
            offset = 0;
          }

          for (var j = i + 1; j < rows.count(); j++) {
            if (
              api.cell(rows[0][j], 5).data() != curSection ||
              api.cell(rows[0][j], 3).data() != curIssue
            )
              break;
          }
          sectionLen = j - i;
          for (var j = 0; j < sectionLen; j++) {
            if (
              curIssue != "портфель" &&
              (USER_ROLE === "administrator" || USER_ROLE === "jeditor")
            )
              $(api.cell(rows[0][i + j], 0).node()).html(
                createSelect(j, sectionLen, offset)
              );
            else api.cell(rows[0][i + j], 0).node().innerHTML = offset + j + 1;
          }
          offset += sectionLen;
          i += j;
        }
      } else {
        api
          .column(0)
          .nodes()
          .each(function (cell, i) {
            cell.innerHTML = i + 1;
          });
      }

      //Insert lines after partitioning
      if (api.settings().order()[0][0] == "11") {
        var minProblemLevel = 40;
        var problemName = "";
        for (var i = 0; i < frows.count(); i++) {
          var initID = frows[0][i]; //Row index in the initial table
          var curProblemIndex = api.cell(initID, 11).data();
          if (i == 0 || curProblemIndex <= minProblemLevel) {
            if (curProblemIndex > 30) {
              problemName = "СРОЧНАЯ ПРОБЛЕМА";
              minProblemLevel = 30;
            } else if (curProblemIndex > 20) {
              problemName = "ТРЕБУЕТСЯ ВНИМАНИЕ";
              minProblemLevel = 20;
            } else if (curProblemIndex > 10) {
              problemName = "СТАТЬИ ГОТОВЫ";
              minProblemLevel = 10;
            } else {
              problemName = "ВМЕШАТЕЛЬСТВО ПОКА НЕ ТРЕБУЕТСЯ";
              minProblemLevel = -1;
            }

            $(frows.nodes())
              .eq(i)
              .before(
                '<tr class="chapter"><td colspan="' +
                  api.columns(":visible").count() +
                  '" class="group">' +
                  problemName +
                  "</td></tr>"
              );
          }
        }
      } else if (api.settings().order()[0][0] == "2") {
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
                '<tr class="chapter"><td colspan="' +
                  api.columns(":visible").count() +
                  '" class="group">СТАТЬИ В ВЫПУСК \'' +
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
                '<tr class="subchapter"><td colspan="' +
                  api.columns(":visible").count() +
                  '" class="group">' +
                  curSection +
                  "</td></tr>"
              );
          }
        }
      }

      //Colorize rows
      for (var i = 0; i < api.rows().count(); i++) {
        var pri = api.cell(i, 11).data();
        if (pri > 30) $(api.row(i).node()).addClass("alarm");
        else if (pri > 20) $(api.row(i).node()).addClass("problem");
        else if (pri > 10) $(api.row(i).node()).addClass("cool");
      }
    }
  });

  //Create select-element for sequnce number of the article
  function createSelect(i, len, offset) {
    var select = $("<select>").attr("style", "width: 42px");
    for (var j = 0; j < len; j++) {
      var option = $("<option>")
        .attr("value", j + 1)
        .text(j + 1 + offset);
      if (j === i) option.attr("selected", true);
      select.append(option);
    }

    select.change(function () {
      var ID_Article = datatable.row($(this).closest("tr")).data().ID_Article;
      var newval = this.value;

      $.ajax({
        type: "POST",
        url:
          ADMIN_URL +
          "?action=main_article_reorder_json&id=" +
          ID_Article +
          "&val=" +
          newval,
        contentType: false,
        processData: false,
        success: function (response) {
          var data = JSON.parse(response).data;
          if (data[0] == 2) showMsg(data);
          datatable.ajax.reload();
        }
      });
    });
    return select[0];
  }

  InitMouseClick(datatable, 1, "/articles/view/?id=");
  InitPagingStates(datatable);

  //-----Actions

  //Add article
  $("#addArticleButton").click(function () {
    window.location.href = SITE_URL + "/articles/create";
  });

  //Add issue
  $("#addIssueButton").click(function () {
    showConfirmDialog("Создать новый выпуск?", function () {
      $.ajax({
        url: ADMIN_URL + "?action=main_issue_create_json",
        success: function (response) {
          showMsg(JSON.parse(response).data);
        }
      });
    });
  });

  //Archive issue
  $("#archiveIssueButton").click(function () {
    showConfirmDialog("Отправить выпуск в архив?", function () {
      $.ajax({
        url: ADMIN_URL + "?action=main_issue_archive_json",
        success: function (response) {
          showMsg(JSON.parse(response).data);
        }
      });
    });
  });

  //Change between order-first and importance-first models
  if (ptype === "reorder") {
    datatable.order([0, "asc"]);
    datatable.column(8).visible(true);
    datatable.column(9).visible(true);
    var pageCount = $("#datatable thead tr").children().get(3);
    $(pageCount).removeClass("hidden");
    var days = $("#datatable thead tr").children().get(4);
    $(days).removeClass("hidden");
  } else {
    datatable.order([10, "asc"]);
  }

  $("#statusView").click(function () {
    window.location.href = SITE_URL;
  });

  $("#orderView").click(function () {
    window.location.href = SITE_URL + "/?type=reorder";
  });

  //Service Action
  $("#serviceAction").click(function () {
    $("#serviceDialog").modal("toggle");
  });

  $("#serviceForm").submit(function (e) {
    e.preventDefault();
    //Load form data
    var fd = new FormData($("#serviceForm")[0]);
    fd.append("action", "main_service_json");

    $.ajax({
      type: "POST",
      url: ADMIN_URL,
      data: fd,
      contentType: false,
      processData: false,
      success: function (response) {
        $("#serviceDialog").modal("toggle");
      }
    });
  });

  //Service2 Action
  $("#service2Action").click(function () {
    $("#service2Dialog").modal("toggle");
  });

  $("#service2Form").submit(function (e) {
    e.preventDefault();
    //Load form data
    var fd = new FormData($("#service2Form")[0]);
    fd.append("action", "main_service2_json");

    $.ajax({
      type: "POST",
      url: ADMIN_URL,
      data: fd,
      contentType: false,
      processData: false,
      success: function (response) {
        $("#service2Dialog").modal("toggle");
      }
    });
  });
});
