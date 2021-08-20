var ID_Article = searchParams.get("id");

$(document).ready(function () {
  //[Load] Resources
  var pdfImage =
    "<img src='" +
    TEMPLATE_URL +
    "/resources/pdficon.png" +
    "' height=30px class='icon'/>";

  //[Load] File-upload managers
  var pdfRevFM = new FileManager(
    FileManagerOptions.Upload,
    FileManagerOptions.OnlyPdf,
    FileManagerOptions.Closeable
  );
  pdfRevFM.embedObject($("#pdfRevFile"));

  var extraRevFM = new FileManager(
    FileManagerOptions.Upload,
    FileManagerOptions.OnlyPdf,
    FileManagerOptions.Closeable
  );
  extraRevFM.embedObject($("#extraRevFile"));

  var pdfArtFM = new FileManager(
    FileManagerOptions.Upload,
    FileManagerOptions.OnlyPdf,
    FileManagerOptions.Closeable
  );
  pdfArtFM.embedObject($("#pdfArtFile"));

  var pdfRepFM = new FileManager(
    FileManagerOptions.Upload,
    FileManagerOptions.OnlyPdf,
    FileManagerOptions.Closeable
  );
  pdfRepFM.embedObject($("#pdfRepFile"));

  var pdfVerFM = new FileManager(
    FileManagerOptions.Upload,
    FileManagerOptions.OnlyPdf,
    FileManagerOptions.Closeable
  );
  pdfVerFM.embedObject($("#pdfVerFile"));

  //[Load] Article data
  function loadInfo() {
    $.ajax({
      url: ADMIN_URL + "?action=articles_get_article_json&id=" + ID_Article,
      success: function (response) {
        var data = JSON.parse(response).data;
        if (data == null) showMsg([2, "Статья не найдена"]);
        else {
          if (data.HasPriority === "Y")
            data.Title =
              "<span class='glyphicon glyphicon-fire priority' title='Особый приоритет'></span> " +
              data.Title;
          $("[name=ID_Article]").html(ID_Article);
          $("[name=Title]").html(data.Title);
          $("[name=Authors]").html(data.Authors);
          $("#infoStatus").removeClass("alarm");
          $("#infoStatus").removeClass("highlight");
          $("#infoStatus").removeClass("cool");
          if (data.ID_Issue == 1) $("#infoStatus").addClass("alarm");
          else if (data.ID_Issue == 3) $("#infoStatus").addClass("highlight");
          else $("#infoStatus").addClass("cool");
          $("[name=ITitle]").html(data.ITitle);
          $("[name=PageCount]").html(data.PageCount);
          $("[name=RecvDate]").html(revertDate(data.MinRecvDate));
          $("[name=STitle]").html(data.STitle);
          $("[name=Affiliation]").html(data.Affiliation);
          $("[name=CorAuthor]").html(
            data.CorName + " &lt;" + data.CorMail + "&gt;"
          );
          $("[name=RemDate]").html(revertDate(data.RemDate));
          $("[name=FinalVerdictDate]").html(revertDate(data.FinalVerdictDate));
          if (data.FinalVerdictDate == null) {
            $("#sciappaction").show();
            $("#sciAppStatus").addClass("highlight");
            $("[name=SATitle]").html("в работе");
          } else if (data.ID_Issue == 1) {
            $("#sciappaction").hide();
            $("#sciAppStatus").addClass("alarm");
            $("#techAppStatus").removeClass("cool");
            $("#techAppStatus").removeClass("highlight");
            $("[name=SATitle]").html("отклонено");
          } else {
            $("#sciappaction").hide();
            $("#sciAppStatus").addClass("cool");
            $("#techAppStatus").removeClass("alarm");
            $("#techAppStatus").removeClass("highlight");
            $("[name=SATitle]").html("принято");
          }
          $("[name=Priority]").html(
            data.HasPriority === "Y" ? "Особый" : "Нет"
          );
        }
      }
    });

    //Load pdffile data
    $.ajax({
      url:
        ADMIN_URL + "?action=files_get_article_pdf_url_json&id=" + ID_Article,
      success: function (response) {
        var data = JSON.parse(response).data;
        if (data != null) {
          $("#pdfbutton").show();
          $("#pdffile").attr("data", data);
        }
      }
    });
  }
  loadInfo();

  //[Info] Open pdf text
  $("#collapse2").on("shown.bs.collapse", function () {
    $("html,body").animate({ scrollTop: $("#pdfbutton").offset().top }, "slow");
  });

  //[Reviews] Load scientific reviews data
  var scitable = $("#scitable").DataTable({
    bAutoWidth: false,
    bInfo: false,
    bLengthChange: false,
    serverSide: false,
    processing: false,
    paging: false,
    language: {
      emptyTable:
        "<div style='text-align: center; font-size:11pt;'>Рецензентов нет</div>"
    },
    ajax: {
      url:
        ADMIN_URL + "?action=reviews_get_article_reviews_json&id=" + ID_Article,
      type: "post",
      dataType: "json",
      contentType: "application/json; charset=utf-8"
    },

    order: [[0, "desc"]],

    columns: [
      { data: "RevNo" },
      { data: "ID_Review" },
      {
        data: "EName",
        render: function (data, type, JsonResultRow, meta) {
          return contractName(data);
        }
      },
      {
        data: "ToExpDate",
        render: function (data, type, JsonResultRow, meta) {
          var control = "";
          if (USER_ROLE === "administrator")
            control =
              "<br/><input type='button' value='Послать' class='btn btn-info toexp' />";
          return revertDate(data) + control;
        }
      },
      {
        data: "FromExpDate",
        render: function (data, type, JsonResultRow, meta) {
          var control = "";
          if (USER_ROLE === "administrator")
            control =
              "<br/><input type='button' value='Записать' class='btn btn-warning fromexp' />";
          return revertDate(data) + control;
        }
      },
      { data: "VTitle" },
      {
        data: "ToAuthDate",
        render: function (data, type, JsonResultRow, meta) {
          var control = "";
          if (USER_ROLE === "administrator")
            control =
              "<br/><input type='button' value='Послать' class='btn btn-info toauth' />";
          return revertDate(data) + control;
        }
      },
      {
        data: "FromAuthDate",
        render: function (data, type, JsonResultRow, meta) {
          var control = "";
          if (USER_ROLE === "administrator")
            control =
              "<br/><input type='button' value='Записать' class='btn btn-warning fromauth' />";
          return revertDate(data) + control;
        }
      },
      {
        defaultContent: "",
        render: function (data, type, JsonResultRow, meta) {
          var result = "";
          if (JsonResultRow.ReviewPdf)
            result +=
              "<a href='" +
              JsonResultRow.ReviewPdf +
              "' target='_blank'>" +
              pdfImage +
              "Рецензия</a><br/>";
          if (JsonResultRow.ExtraPdf)
            result +=
              "<a href='" +
              JsonResultRow.ExtraPdf +
              "' target='_blank'>" +
              pdfImage +
              "Экстра</a><br/>";
          if (JsonResultRow.ReplyPdf)
            result +=
              "<a href='" +
              JsonResultRow.ReplyPdf +
              "' target='_blank'>" +
              pdfImage +
              "Ответ</a><br/>";
          return result;
        }
      },
      { data: "Quality" },
      {
        data: "RemDate",
        render: function (data, type, JsonResultRow, meta) {
          if (USER_ROLE === "administrator")
            return (
              (data == null ? "" : revertDate(data)) +
              "<br/><input type='button' value='Послать' class='btn btn-info toexprem' />"
            );
          return revertDate(data);
        }
      }
    ],

    columnDefs: [
      {
        targets: [1],
        visible: false
      },
      {
        targets: [3, 4, 5, 6, 7, 9, 10],
        class: "centered"
      },
      {
        targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        orderable: false
      }
    ],

    drawCallback: function (settings) {
      var api = this.api();
      var rows = api.rows();
      if (rows[0].length == 0) return;

      //Show sci status
      $("#statusListSci").empty();
      var lastReviewer = "";
      var row = $("<div class='status-row'></div>");
      for (var i = 0; i < api.rows().count(); i++) {
        if (api.cell(i, 2).data() !== lastReviewer) {
          lastReviewer = api.cell(i, 2).data();

          row = row.clone();
          row.empty();
          //Set canceled reviewers to the end
          if (
            api.cell(i, 5).data() === "снят" ||
            api.cell(i, 5).data() === "отказался"
          )
            $("#statusListSci").append(row);
          else $("#statusListSci").prepend(row);

          var checkbox = $(
            "<input type='checkbox' style='visibility:hidden'/>"
          );
          //Enable or disable mass button
          checkbox.on("change", function () {
            $("#massaction").attr("disabled", true);
            $(
              "#statusListSci input[type=checkbox], #statusListTech input[type=checkbox]"
            ).each(function () {
              if ($(this).prop("checked"))
                $("#massaction").attr("disabled", false);
            });
          });
          if (USER_ROLE === "administrator") row.append(checkbox);
          row.append(
            "<input type='hidden' value='" + api.cell(i, 1).data() + "'/>"
          );
          row.append(
            createStatusName(
              contractName(api.cell(i, 2).data()),
              api.cell(i, 5).data()
            )
          );

          //Show actual actions
          if (api.cell(i, 4).data() == null) {
            row.append(
              createStatusStage(
                "у рецензента",
                revertDate(api.cell(i, 3).data())
              )
            );
            //Show reminder status
            if (api.cell(i, 10).data() != null)
              row.append(
                createStatusStage(
                  "напоминание",
                  revertDate(api.cell(i, 10).data())
                )
              );

            var button = createStatusAction("Записать");
            var link = api.cell(i, 4).nodes().to$().find("input");
            button.click({ btn: link }, function (event) {
              event.data.btn.trigger("click");
            });
            if (USER_ROLE === "administrator") row.append(button);

            //Reminder button
            var today = new Date();
            var sent = new Date(api.cell(i, 3).data());
            var days = Math.ceil(
              (today.getTime() - sent.getTime()) / (1000 * 60 * 60 * 24)
            );
            if (days > 14) {
              var button = createStatusAction("Напомнить");
              var link = api.cell(i, 10).nodes().to$().find("input");
              button.click({ btn: link }, function (event) {
                event.data.btn.trigger("click");
              });
              if (USER_ROLE === "administrator") row.append(button);
            }
          } else if (api.cell(i, 6).data() == null) {
            if (
              api.cell(i, 5).data() !== "снят" &&
              api.cell(i, 5).data() !== "отказался"
            ) {
              var button = createStatusAction("Послать");
              var link = api.cell(i, 6).nodes().to$().find("input");
              button.click({ btn: link }, function (event) {
                event.data.btn.trigger("click");
              });
              if (api.cell(i, 5).data() !== "добро")
                checkbox.attr("checked", true).trigger("change");
              checkbox.css("visibility", "visible");
              if (USER_ROLE === "administrator") row.append(button);
            }
          } else if (api.cell(i, 7).data() == null) {
            row.append(
              createStatusStage("у автора", revertDate(api.cell(i, 6).data()))
            );
            var button = createStatusAction("Записать");
            var link = api.cell(i, 7).nodes().to$().find("input");
            button.click({ btn: link }, function (event) {
              event.data.btn.trigger("click");
            });
            if (USER_ROLE === "administrator") row.append(button);
          }
        }

        //Show verdict
        if (api.cell(i, 5).data() != null) {
          row
            .find(".status-el")
            .first()
            .after(
              createStatusStage(
                api.cell(i, 5).data(),
                revertDate(api.cell(i, 4).data())
              )
            );
        }
      }
    }
  });
  if (USER_ROLE === "administrator")
    InitMouseClick(scitable, 1, "/reviews/edit/?id=");

  //[Reviews] Create name label
  function createStatusName(name, verdict) {
    var name = $(
      "<div class='status-el status-name'><div style='padding-top:12px'><label style='font-size:10pt'>" +
        name +
        "</label></div></div>"
    );
    colorizeStatus(name, verdict);
    return name;
  }

  //[Reviews] Create status label
  function createStatusStage(verdict, date) {
    var status = $(
      "<div class='status-el status-back'><div style='text-align:center; padding-top:4px'><label style='display:grid'>" +
        verdict +
        "</label><label style='display:grid'>" +
        date +
        "</label></div></div>"
    );
    colorizeStatus(status, verdict);
    return status;
  }

  //[Reviews] Colorize status according to verdict
  function colorizeStatus(obj, verdict) {
    if (verdict === null) return;

    //Scientific
    if (verdict === "добро") obj.addClass("cool");
    else if (verdict === "подправить" || verdict === "переделать")
      obj.addClass("warning");
    else if (verdict === "отклонить" || verdict === "напоминание")
      obj.addClass("error");
    else if (verdict === "снят" || verdict === "отказался")
      obj.addClass("disabled");

    //Tech
    if (verdict.includes("%CompletelyOK%")) obj.addClass("cool");
    else if (verdict === "замечания" || verdict.includes("%"))
      obj.addClass("warning");
  }

  //[Reviews] Create action button
  function createStatusAction(name) {
    var button = $(
      "<div class='status-el status-back status-action'><div style='padding-top:12px; text-align:center'><label style='font-size:11pt;pointer-events:none'>&nbsp;" +
        name +
        "</label></div></div>"
    );

    if (name === "Записать" || name === "Замечания") {
      button.addClass("warning");
      button
        .find("label")
        .prepend('<span class="glyphicon glyphicon-edit"></span>');
    } else if (name === "Напомнить") {
      button.addClass("error");
      button
        .find("label")
        .prepend('<span class="glyphicon glyphicon-envelope"></span>');
    } else if (name === "Статья+") {
      button.addClass("error");
      button
        .find("label")
        .prepend('<span class="glyphicon glyphicon-plus-sign"></span>');
    } else {
      button.addClass("primary");
      button
        .find("label")
        .prepend('<span class="glyphicon glyphicon-envelope"></span>');
    }
    return button;
  }

  //[Reviews] Sending all reviews
  $("#massaction").click(function () {
    var ID_Reviews = [];
    $("#statusListSci input[type=checkbox]:checked").each(function () {
      ID_Reviews.push($(this).next().val());
    });

    var ID_Version = null;
    $("#statusListTech input[type=checkbox]:checked").each(function () {
      ID_Version = $(this).next().val();
    });

    showLettersDialog({
      ID_Article: ID_Article,
      ID_Review: JSON.stringify(ID_Reviews),
      ID_Version: ID_Version,
      Type: "toA_Coms"
    });
  });

  var ID_Review = "";

  //[Reviews] To Expert article
  scitable.on("click", "input.toexp", function () {
    ID_Review = scitable.row($(this).closest("tr")).data().ID_Review;
    showLettersDialog({
      ID_Article: ID_Article,
      ID_Review: ID_Review,
      Type: "toR_A"
    });
  });

  //[Reviews] To Expert reminder
  scitable.on("click", "input.toexprem", function () {
    ID_Review = scitable.row($(this).closest("tr")).data().ID_Review;
    showLettersDialog({
      ID_Article: ID_Article,
      ID_Review: ID_Review,
      Type: "toR_RemH"
    });
  });

  //[Reviews] From Expert
  scitable.on("click", "input.fromexp", function () {
    ID_Review = scitable.row($(this).closest("tr")).data().ID_Review;
    var Name = contractName(scitable.row($(this).closest("tr")).data().EName);
    var RevNo = scitable.row($(this).closest("tr")).data().RevNo;
    $("#fromExpertDialog")
      .find(".modal-title")
      .html(Name + " - Рецензия #" + RevNo);
    $("#fromExpertDialog").modal({ backdrop: "static", keyboard: false });
  });

  //[Reviews] Change field props based on verdict
  $("#fromExpertDialog select[name=ID_Verdict]").change(function (e) {
    //Change file-neccessity based on verdict
    if ($(this).val() > 1 && $(this).val() < 4) $("#pdfRevFileRequired").show();
    else $("#pdfRevFileRequired").hide();

    //Hide review assessment, hide confirmation and uncheck it based on verdict
    if ($(this).val() >= 5) {
      $("#reviewQuality").hide();
      $("#confLetter").hide();
      $("#fromExpertDialog input[name=SendConfLetter]").attr("checked", false);
    } else {
      $("#reviewQuality").show();
      $("#confLetter").show();
      $("#fromExpertDialog input[name=SendConfLetter]").attr("checked", true);
    }
  });

  //[Reviews] Handle verdict-picker buttons
  $(".verdictpicker").click(function () {
    var index = 1;
    if ($(this).hasClass("verd2")) index = 2;
    if ($(this).hasClass("verd3")) index = 3;
    if ($(this).hasClass("verd4")) index = 4;
    if ($(this).hasClass("verd5")) index = 5;
    if ($(this).hasClass("verd6")) index = 6;

    var select = $(this).closest(".form-value").find("select");
    $(select).val(index).trigger("change");
  });

  //[Reviews] Handle quality-picker buttons
  $(".qualitypicker").click(function () {
    var index = 1;
    if ($(this).hasClass("verd2")) index = 2;
    if ($(this).hasClass("verd3")) index = 3;

    var select = $(this).closest(".form-value").find("select");
    $(select).val(index).trigger("change");
  });

  //[Reviews] Set Expert verdict
  $("#fromExpertForm").submit(function (e) {
    e.preventDefault();
    //Load form data
    var fd = new FormData($("#fromExpertForm")[0]);
    fd.append("ID_Article", ID_Article);
    fd.append("ID_Review", ID_Review);
    //Load PDF file
    if (
      pdfRevFM.filesCount === 0 &&
      $("#fromExpertDialog [name=ID_Verdict]").val() > 1 &&
      $("[name=ID_Verdict]").val() < 4
    ) {
      showMsg([2, "Загрузите PDF-файл рецензии"]);
      return;
    }
    fd.append("file", pdfRevFM.file);
    //Load extra file
    fd.append("filex", extraRevFM.file);

    fd.append("action", "reviews_set_expert_verdict_json");

    $.ajax({
      type: "POST",
      url: ADMIN_URL,
      data: fd,
      contentType: false,
      processData: false,
      success: function (response) {
        var data = JSON.parse(response).data;
        showMsg(data);
        $("#fromExpertDialog").modal("toggle");
        scitable.ajax.reload();
      }
    });
  });

  //[Reviews] To Author
  scitable.on("click", "input.toauth", function () {
    ID_Review = scitable.row($(this).closest("tr")).data().ID_Review;
    showLettersDialog({
      ID_Article: ID_Article,
      ID_Review: ID_Review,
      Type: "toA_SciCom"
    });
  });

  //[Reviews] From Author
  scitable.on("click", "input.fromauth", function () {
    ID_Review = scitable.row($(this).closest("tr")).data().ID_Review;
    var Name = contractName(scitable.row($(this).closest("tr")).data().EName);
    var RevNo = scitable.row($(this).closest("tr")).data().RevNo;
    $("#fromAuthorDialog")
      .find(".modal-title")
      .html(Name + " - Ответ на рецензию #" + RevNo);
    $("#fromAuthorDialog").modal({ backdrop: "static", keyboard: false });
  });
  $("#noRepPDF").on("change", function () {
    if ($(this).prop("checked")) {
      $("#pdfRepFileDiv").hide();
    } else {
      $("#pdfRepFileDiv").show();
    }
  });
  $("#fromAuthorForm").submit(function (e) {
    e.preventDefault();
    //Load form data
    var fd = new FormData($("#fromAuthorForm")[0]);
    fd.append("ID_Article", ID_Article);
    fd.append("ID_Review", ID_Review);
    //Load PDF reply file
    if (!$("#noRepPDF").prop("checked") && pdfRepFM.filesCount === 0) {
      showMsg([2, "Загрузите PDF-файл ответа"]);
      return;
    }

    fd.append("file", pdfRepFM.file);
    fd.append("action", "reviews_set_author_reply_json");

    $.ajax({
      type: "POST",
      url: ADMIN_URL,
      data: fd,
      contentType: false,
      processData: false,
      success: function (response) {
        showMsg(JSON.parse(response).data);
        $("#fromAuthorDialog").modal("toggle");
        scitable.ajax.reload();
      }
    });
  });

  //[Add reviewer] Set focus on search input
  $("#collapse3").on("shown.bs.collapse", function () {
    $("#revsearch").focus();
  });

  //[Add reviewer] Load reviews data
  var ID_Expert;
  var addrevtable = $("#addrevtable").DataTable({
    bAutoWidth: false,
    bInfo: false,
    bLengthChange: false,
    serverSide: false,
    processing: false,
    paging: false,
    language: {
      emptyTable:
        "<div style='text-align: center; font-size:11pt;'>Рецензентов не найдено</div>"
    },
    ajax: {
      url: ADMIN_URL + "?action=experts_search_advanced_json&id=" + ID_Article,
      type: "post",
      dataType: "json",
      contentType: "application/json; charset=utf-8"
    },

    columns: [
      { defaultContent: "", className: "centered" },
      { data: "ID_Expert" },
      {
        data: "Name",
        render: function (data, type, JsonResultRow, meta) {
          return (
            "<b>" +
            contractName(JsonResultRow.Name) +
            "</b> &lt;" +
            JsonResultRow.Mail +
            "&gt; [в работе: " +
            Math.round(JsonResultRow.ActiveCount) +
            ", опыт: " +
            Math.round(JsonResultRow.TotalCount) +
            "]" +
            (JsonResultRow.PrevExp === "1" ? " <b>рекомендуется</b>" : "") +
            (JsonResultRow.Interests != null
              ? "<br>" + JsonResultRow.Interests
              : "")
          );
        }
      }
    ],

    columnDefs: [
      {
        targets: [0, 1],
        visible: false
      },
      {
        targets: [0, 1, 2],
        orderable: false
      }
    ],

    drawCallback: function (settings) {
      var api = this.api();
      var rows = api.rows();
      if (rows[0].length == 0) return;
      //Add numeration
      api
        .column(0)
        .nodes()
        .each(function (cell, i) {
          cell.innerHTML = i + 1;
        });
      //Dissable add button on reload
      $("#addreviewer_ok").attr("disabled", true);
    }
  });
  InitMouseSelect(addrevtable);

  //[Add reviewer] Select row in table
  addrevtable.on("click", "td", function () {
    var rowNo = addrevtable.row($(this).closest("tr")).index();
    if (typeof rowNo != "undefined") {
      ID_Expert = addrevtable.cell(rowNo, 1).data();
      $("#addreviewer_ok").attr("disabled", false);
    }
  });

  //[Add reviewer] Search for experts
  $("#revsearch").on("keyup", function () {
    var kw = "";
    if (this.value.length >= 3)
      addrevtable.ajax
        .url(
          ADMIN_URL +
            "?action=experts_search_advanced_json&id=" +
            ID_Article +
            "&kw=" +
            this.value
        )
        .load();
  });

  //[Add reviewer] Add reviewer
  $("#addreviewer_ok").click(function () {
    var fd = new FormData();
    fd.append("ID_Article", ID_Article);
    fd.append("ID_Expert", ID_Expert);
    fd.append("LetterToExpert", "Y");
    fd.append("action", "reviews_assign_expert_json");

    $.ajax({
      type: "POST",
      url: ADMIN_URL,
      data: fd,
      contentType: false,
      processData: false,
      success: function (response) {
        var data = JSON.parse(response).data;
        if (data[0] == 2) showMsg(data);
        //Invitation letter
        else if (data[0] == 3) {
          showMsg([1, "Рецензент добавлен"]);

          showLettersDialog({
            ID_Article: ID_Article,
            ID_Review: data[2],
            Type: "toR_A"
          });
        } else {
          showMsg([1, "Рецензент добавлен"]);
          showMsg([1, "Письмо рецензенту отправлено"]);
        }

        scitable.ajax.reload();
        addrevtable.ajax.reload();
      }
    });
  });

  //[Add reviewer] Add new expert
  $("#addnewexpert").click(function () {
    $("#revsearch").val("");
    window.open(SITE_URL + "/experts/create", "_blank");
  });

  //[Tech] Load tech data
  var techtable = $("#techtable").DataTable({
    bAutoWidth: false,
    bInfo: false,
    bLengthChange: false,
    serverSide: false,
    processing: false,
    paging: false,
    language: {
      emptyTable:
        "<div style='text-align: center; font-size:11pt;'>Версий статьи нет</div>"
    },
    ajax: {
      url:
        ADMIN_URL +
        "?action=versions_get_article_versions_json&id=" +
        ID_Article,
      type: "post",
      dataType: "json",
      contentType: "application/json; charset=utf-8"
    },

    order: [[0, "desc"]],

    columns: [
      { data: "VerNo" },
      { data: "ID_Version" },
      {
        data: "RecvDate",
        render: function (data, type, JsonResultRow, meta) {
          return revertDate(data);
        }
      },
      {
        data: "TechComments",
        render: function (data, type, JsonResultRow, meta) {
          var control = "";
          var title = "Обзор";
          if (USER_ROLE === "administrator" || USER_ROLE === "jteditor")
            title = "Записать";

          control =
            "<br/><input type='button' value='" +
            title +
            "' class='btn btn-warning techcom' />";
          return (data == null ? "не вносились" : "записано") + control;
        }
      },
      {
        data: "ToAuthDate",
        render: function (data, type, JsonResultRow, meta) {
          var control = "";
          if (USER_ROLE === "administrator")
            control =
              "<br/><input type='button' value='Послать' class='btn btn-info toauth' />";
          return revertDate(data) + control;
        }
      },
      {
        defaultContent: "",
        render: function (data, type, JsonResultRow, meta) {
          var control = "";
          if (USER_ROLE === "administrator")
            control =
              "<br/><input type='button' value='Записать' class='btn btn-warning fromauth' />";
          return control;
        }
      },
      {
        defaultContent: "",
        render: function (data, type, JsonResultRow, meta) {
          var result = "";
          if (JsonResultRow.ArticlePdf)
            result +=
              "<a href='" +
              JsonResultRow.ArticlePdf +
              "' target='_blank'>" +
              pdfImage +
              "Статья</a><br/>";
          return result;
        }
      }
    ],

    columnDefs: [
      {
        targets: [1],
        visible: false
      },
      {
        targets: [0, 2, 3, 4, 5],
        class: "centered"
      },
      {
        targets: [0, 2, 3, 4, 5, 6],
        orderable: false
      }
    ],

    drawCallback: function (settings) {
      var api = this.api();
      var rows = api.rows();
      if (rows[0].length == 0) return;

      if (
        api.cell(0, 3).data() !== null &&
        api.cell(0, 3).data().includes("%CompletelyOK%")
      ) {
        $("#techappaction").hide();
        $("#techAppStatus").addClass("cool");
        $("#techAppStatus").removeClass("highlight");
        $("[name=TATitle]").html("принято");
      } else {
        $("#techappaction").show();
        $("#techAppStatus").addClass("highlight");
        $("#techAppStatus").removeClass("cool");
        $("[name=TATitle]").html("в работе");
      }

      //Show tech status
      $("#statusListTech").empty();
      var row = $("<div class='status-row'></div>");
      for (var i = 0; i < api.rows().count(); i++) {
        if (i === 0) {
          $("#statusListTech").prepend(row);

          var checkbox = $(
            "<input type='checkbox' style='visibility:hidden'/>"
          );
          //Enable or disable mass button
          checkbox.on("change", function () {
            $("#massaction").attr("disabled", true);
            $(
              "#statusListSci input[type=checkbox], #statusListTech input[type=checkbox]"
            ).each(function () {
              if ($(this).prop("checked"))
                $("#massaction").attr("disabled", false);
            });
          });
          if (USER_ROLE === "administrator") row.append(checkbox);
          row.append(
            "<input type='hidden' value='" + api.cell(i, 1).data() + "'/>"
          );
          row.append(createStatusName("Тех-редактор", api.cell(i, 3).data()));

          //Show actual actions
          if (api.cell(i, 3).data() == null) {
            var button = createStatusAction("Замечания");
            var link = api.cell(i, 3).nodes().to$().find("input");
            button.click({ btn: link }, function (event) {
              event.data.btn.trigger("click");
            });
            row
              .find(".status-el")
              .first()
              .after(
                createStatusStage(
                  "исправления",
                  revertDate(api.cell(i, 2).data())
                )
              );
            if (USER_ROLE === "administrator" || USER_ROLE === "jteditor")
              row.append(button);
          } else if (api.cell(i, 4).data() == null) {
            var button = createStatusAction("Послать");
            var link = api.cell(i, 4).nodes().to$().find("input");
            button.click({ btn: link }, function (event) {
              event.data.btn.trigger("click");
            });
            if (!api.cell(i, 3).data().includes("%CompletelyOK%"))
              checkbox.attr("checked", true).trigger("change");
            checkbox.css("visibility", "visible");
            if (USER_ROLE === "administrator") row.append(button);
          } else if (api.cell(i, 4).data() != null) {
            row.append(
              createStatusStage("у автора", revertDate(api.cell(i, 4).data()))
            );
          }
          {
            var button = createStatusAction("Статья+");
            var link = api.cell(i, 5).nodes().to$().find("input");
            button.click({ btn: link }, function (event) {
              event.data.btn.trigger("click");
            });
            if (USER_ROLE === "administrator") row.append(button);
          }
        }

        //Show verdict
        if (api.cell(i, 3).data() != null) {
          if (api.cell(i, 3).data().includes("%CompletelyOK%"))
            row
              .find(".status-el")
              .first()
              .after(
                createStatusStage("добро", revertDate(api.cell(i, 2).data()))
              );
          else
            row
              .find(".status-el")
              .first()
              .after(
                createStatusStage(
                  "замечания",
                  revertDate(api.cell(i, 2).data())
                )
              );
        }
      }
    }
  });
  if (USER_ROLE === "administrator")
    InitMouseClick(techtable, 1, "/versions/edit/?id=");

  var ID_Version = "";
  var prevtechcom = "";
  //[Tech] Load tech comments
  function loadComments(techcom) {
    //Clear form values
    $("#techComForm").find("input[type=text], textarea").val("");
    $("#techComForm").find("input[type=checkbox]").prop("checked", false);

    if (techcom != null) {
      var index = 2;
      if (techcom.includes("%CompletelyWrong%")) index = 0;
      else if (techcom.includes("%CompletelyOK%")) index = 1;
      else {
        var arr = techcom.split("\n");
        var others = "";
        for (var i = 0; i < arr.length; i++) {
          var cur = arr[i];
          if (cur.includes("%WrongLinking%"))
            $("input[name=WrongLinking]").prop("checked", true);
          else if (cur.includes("%ColorPictures%"))
            $("input[name=ColorPictures]").prop("checked", true);
          else if (cur.includes("%NoGRNTI%"))
            $("input[name=NoGRNTI]").prop("checked", true);
          else if (cur.includes("%AutoNumbering%"))
            $("input[name=AutoNumbering]").prop("checked", true);
          else if (cur.includes("%PictureTables%"))
            $("input[name=PictureTables]").prop("checked", true);
          else if (cur.includes("%MSEquation%"))
            $("input[name=MSEquation]").prop("checked", true);
          else if (cur.includes("%Fractions%"))
            $("input[name=Fractions]").prop("checked", true);
          else if (cur.includes("%WrongSubject%"))
            $("input[name=WrongSubject]").prop("checked", true);
          else if (cur.includes("%NeedVector%")) {
            $("input[name=NeedVector]").val(arr[i + 1]);
            i++;
          } else if (cur.includes("%SeparateFiles%")) {
            $("input[name=SeparateFiles]").val(arr[i + 1]);
            i++;
          } else if (cur.includes("%BlackPictures%")) {
            $("input[name=BlackPictures]").val(arr[i + 1]);
            i++;
          } else if (cur.includes("%Others%")) {
          } else {
            others += cur;
          }
        }
        $("textarea[name=Others]").val(others);
      }
      $("input[name=Overall]")
        .eq(index)
        .prop("checked", true)
        .trigger("change");
    }
  }

  //[Tech] Set tech comments
  techtable.on("click", "input.techcom", function () {
    ID_Version = techtable.row($(this).closest("tr")).data().ID_Version;

    var techcom = techtable.row($(this).closest("tr")).data().TechComments;
    loadComments(techcom);
    //Save previous comments - hide/show button
    if (techtable.row($(this).closest("tr").next()).length !== 0) {
      prevtechcom = techtable.row($(this).closest("tr").next()).data()
        .TechComments;
      $("#prevcom").show();
    } else $("#prevcom").hide();

    $("#techComDialog").modal({ backdrop: "static", keyboard: false });
  });

  //[Tech] Hide or show fields for comments (no need for Bad and OK)
  $("input[name=Overall]").change(function () {
    if (this.value >= 2) $("#techcomments").show();
    else $("#techcomments").hide();
  });

  //[Tech] Load previous comments
  $("#prevcom").click(function () {
    loadComments(prevtechcom);
    $("input[name=Overall]").eq(3).prop("checked", true);
  });

  //[Tech] Set tech comments
  $("#techComForm").submit(function (e) {
    e.preventDefault();
    //Load form data
    var fd = new FormData($("#techComForm")[0]);
    fd.append("ID_Version", ID_Version);
    fd.append("action", "versions_set_techcomments_json");

    $.ajax({
      type: "POST",
      url: ADMIN_URL,
      data: fd,
      contentType: false,
      processData: false,
      success: function (response) {
        showMsg(JSON.parse(response).data);
        $("#techComDialog").modal("toggle");
        techtable.ajax.reload();
      }
    });
  });

  //[Tech] Send to author
  techtable.on("click", "input.toauth", function () {
    ID_Version = techtable.row($(this).closest("tr")).data().ID_Version;
    showLettersDialog({
      ID_Article: ID_Article,
      ID_Version: ID_Version,
      Tech: "Y",
      Type: "toA_Coms"
    });
  });

  //[Tech] Save response from author
  techtable.on("click", "input.fromauth", function () {
    $("#newVersionDialog").modal({ backdrop: "static", keyboard: false });
  });
  $("#noUpdatePDF").on("change", function () {
    if ($(this).prop("checked")) {
      $("#pdfVerFileDiv").hide();
    } else {
      $("#pdfVerFileDiv").show();
    }
  });
  $("#newVersionForm").submit(function (e) {
    e.preventDefault();
    //Load form data
    var fd = new FormData($("#newVersionForm")[0]);
    fd.append("ID_Article", ID_Article);
    //Load PDF file
    if (!$("#noUpdatePDF").prop("checked") && pdfVerFM.filesCount === 0) {
      showMsg([2, "Загрузите PDF-файл статьи"]);
      return;
    }

    fd.append("file", pdfVerFM.file);
    fd.append("action", "versions_create_json");

    $.ajax({
      type: "POST",
      url: ADMIN_URL,
      data: fd,
      contentType: false,
      processData: false,
      success: function (response) {
        showMsg(JSON.parse(response).data);
        $("#newVersionDialog").modal("toggle");
        techtable.ajax.reload();
        loadInfo();
      }
    });
  });

  //[Common] Update everything after letter-dialog closed
  $("#letterDialog").on("hidden.bs.modal", function () {
    loadInfo();
    scitable.ajax.reload();
    techtable.ajax.reload();
  });

  //[Actions] Edit article
  $("#editaction").click(function () {
    window.location.href = SITE_URL + "/articles/edit/?id=" + ID_Article;
  });

  //[Actions] Remind to authors
  $("#remindaction").click(function () {
    showLettersDialog({
      ID_Article: ID_Article,
      Type: "toA_RemH"
    });
  });

  //[Actions] Send camera-ready version
  $("#camerareadyaction").click(function () {
    showLettersDialog({
      ID_Article: ID_Article,
      Type: "toA_CamR"
    });
  });

  //[Actions] Send letter
  $("#letteraction").click(function () {
    showLettersDialog({
      ID_Article: ID_Article,
      Type: "toA_Emp"
    });
  });

  //[Actions] Create article
  $("#createaction").click(function () {
    window.location.href = SITE_URL + "/articles/create";
  });

  //[Actions] Scientific approve
  $("#sciappaction").click(function () {
    showConfirmDialog("Принять научную часть?", function () {
      $.ajax({
        url: ADMIN_URL + "?action=articles_sciapp_json&id=" + ID_Article,
        success: function (response) {
          showMsg(JSON.parse(response).data);
          loadInfo();
        }
      });
    });
  });

  //[Actions] Reject article
  $("#rejectaction").click(function () {
    showLettersDialog({
      ID_Article: ID_Article,
      ID_Review: ID_Review,
      Type: "toA_Rej"
    });
  });

  //[Actions] Tech approve
  $("#techappaction").click(function () {
    showConfirmDialog("Принять техническую часть?", function () {
      $.ajax({
        url: ADMIN_URL + "?action=articles_techapp_json&id=" + ID_Article,
        success: function (response) {
          showMsg(JSON.parse(response).data);
          loadInfo();
        }
      });
    });
  });

  //[Actions] Reserve to the issue
  $("#reserveaction").click(function () {
    $("#reserveDialog").modal({ backdrop: "static", keyboard: false });
  });
  $("#reserveForm").submit(function (e) {
    e.preventDefault();
    //Load form data
    var fd = new FormData($("#reserveForm")[0]);
    fd.append("ID_Article", ID_Article);
    fd.append("action", "articles_edit_json");
    $.ajax({
      type: "POST",
      url: ADMIN_URL,
      data: fd,
      contentType: false,
      processData: false,
      success: function (response) {
        showMsg(JSON.parse(response).data);
        loadInfo();
        $("#reserveDialog").modal("toggle");
      }
    });
  });
});
