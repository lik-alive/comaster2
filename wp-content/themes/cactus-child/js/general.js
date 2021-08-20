//Initialize GET seach
{
  var searchParams = new URLSearchParams(window.location.search);
  //Get listNo from GET parameters
  var listNo = searchParams.get("list");
  if (listNo === null) listNo = 1;
}

//Throw datatable ajax errors
$.fn.dataTable.ext.errMode = "throw";

//Periodically check logged_in status and open login page on session closed
function checkLoggedIn() {
  $.post(ADMIN_URL, { action: "is_user_logged_in" }, function (response) {
    if (!response) {
      window.location.href =
        SITE_URL + "/wp-login.php?redirect_to=" + window.location.href;
    }
  });

  setTimeout(function () {
    checkLoggedIn();
  }, 60 * 1000);
}

checkLoggedIn();

//Mouse click on table rows
function InitMouseClick(dataTable, idColNo, tolink) {
  dataTable.on("click", "td", function () {
    var rowNo = dataTable.row($(this).closest("tr")).index();
    if (
      typeof rowNo != "undefined" &&
      $(this).find("img").length == 0 &&
      $(this).find("input:button").length == 0 &&
      $(this).find("button").length == 0 &&
      $(this).find("select").length == 0
    ) {
      var id = dataTable.cell(rowNo, idColNo).data();
      window.location.href = SITE_URL + tolink + id;
    }
  });

  dataTable.on("mousedown", "tr", function (e) {
    var rowNo = dataTable.row(this).index();
    if (typeof rowNo != "undefined") {
      var id = dataTable.cell(rowNo, idColNo).data();
      if (e.which == 2) {
        window.open(SITE_URL + tolink + id);
      }
    }
  });

  dataTable.on("mouseenter", "tbody tr", function () {
    if (!$(this).hasClass("chapter") && !$(this).hasClass("subchapter"))
      $(this).addClass("hovered");
  });
}

//Mouse select on table rows
function InitMouseSelect(dataTable) {
  dataTable.on("click", "td", function () {
    var rowNo = dataTable.row($(this).closest("tr")).index();
    if (typeof rowNo != "undefined") {
      dataTable.rows().every(function () {
        $(this.node()).removeClass("selected");
      });
      $(this).closest("tr").addClass("selected");
    }
  });

  dataTable.on("mouseenter", "tbody tr", function () {
    if (!$(this).hasClass("chapter") && !$(this).hasClass("subchapter"))
      $(this).addClass("hovered");
  });
}

//Add GET parameter to the current url (DO NOT USE 'page' name for parameter)
function InitPagingStates(dataTable) {
  dataTable.on("page.dt", function (e, a) {
    var urlGET = "?";

    searchParams.forEach(function (value, key) {
      if (key !== "list") urlGET += key + "=" + value + "&";
    });

    var pageNo = dataTable.page.info().page + 1;
    urlGET += "list" + "=" + pageNo;

    window.history.pushState(null, null, urlGET);
  });
}

//Show message on top of the screen (array(status, msg))
function showMsg(data) {
  if (
    $("#status-bar").hasClass("show") ||
    $("#status-bar").hasClass("hidding")
  ) {
    setTimeout(function () {
      showMsg(data);
    }, 500);
    return;
  }

  //Multiple message
  if (typeof data[0] === "object") {
    data.forEach(function (val) {
      showMsg(val);
    });
  }
  //Single messages
  else {
    var style = "cool";
    if (data[0] === 2) style = "alarm";

    $("#status-bar").text(data[1]);
    $("#status-bar").addClass(style);
    $("#status-bar").addClass("show");
    setTimeout(function () {
      $("#status-bar").addClass("hidding");
      $("#status-bar").removeClass(style);
      $("#status-bar").removeClass("show");
    }, 3000);
    setTimeout(function () {
      $("#status-bar").removeClass("hidding");
    }, 3500);
  }
}

//Contract fullname
function contractName(str) {
  var result = "";
  str.split(" ").forEach(function (el) {
    if (result === "") result = el + " ";
    else result = result + el[0] + ".";
  });
  return result.trim();
}

//Check string localization
function isEnglish(str) {
  return /^[^а-яА-я]*$/.test(str);
}

//Revert date from yyyy-mm-dd to dd-mm-yyyy and back
function revertDate(date) {
  if (date == null) return "";

  if (date[4] === "-")
    return (
      date.substr(8, 2) + "-" + date.substr(5, 2) + "-" + date.substr(0, 4)
    );
  else
    return (
      date.substr(6, 4) + "-" + date.substr(3, 2) + "-" + date.substr(0, 2)
    );
}

//Change keyboard layout
function changeKeyboardLayout(str) {
  var str_rus = [
    "й",
    "ц",
    "у",
    "к",
    "е",
    "н",
    "г",
    "ш",
    "щ",
    "з",
    "х",
    "ъ",
    "ф",
    "ы",
    "в",
    "а",
    "п",
    "р",
    "о",
    "л",
    "д",
    "ж",
    "э",
    "я",
    "ч",
    "с",
    "м",
    "и",
    "т",
    "ь",
    "б",
    "ю",
    "ё"
  ];
  var str_eng = [
    "q",
    "w",
    "e",
    "r",
    "t",
    "y",
    "u",
    "i",
    "o",
    "p",
    "[",
    "]",
    "a",
    "s",
    "d",
    "f",
    "g",
    "h",
    "j",
    "k",
    "l",
    ";",
    "'",
    "z",
    "x",
    "c",
    "v",
    "b",
    "n",
    "m",
    ",",
    ".",
    "`"
  ];

  if (isEnglish(str))
    revert = str.replace(/[a-z\[\];',.`]/g, function (match) {
      return str_rus[str_eng.indexOf(match)];
    });
  else
    revert = str.replace(/[а-я]/g, function (match) {
      return str_eng[str_rus.indexOf(match)];
    });
  return revert;
}

//Escape regexp string
function escapeRegExp(string) {
  return string.replace(/[.*+?^${}()|[\]\\]/g, "\\$&"); // $& means the whole matched string
}

//Collapsible panel
$(".collapser").click(function () {
  $(this).toggleClass("active");
});

/**
 * Create date (fix format for mac systems)
 * @param {} str
 */
function parseDate(str) {
  str = str.replace(/-/g, "/");
  //Fix for swaping year and day if year less than 1970
  if (str.substr(0, 4) < 1970) str = "1970" + str.substr(5);
  return new Date(str);
}

//Set dd-mm-yyyy date format
$("input[type=date]")
  .change(function () {
    var date = parseDate(this.value);
    //Prevent from future dates
    if (new Date() < date) this.value = "";

    var day = date.getDate();
    if (day < 10) day = "0" + day;
    var month = date.getMonth() + 1;
    if (month < 10) month = "0" + month;
    var year = date.getFullYear();

    this.setAttribute(
      "data-date",
      this.value === "" ? "не выбрано" : day + "-" + month + "-" + year
    );
  })
  .change();

//Handle date-picker buttons
$(".daypicker").click(function () {
  var offset = 0;
  if ($(this).hasClass("day1")) offset = 1;
  else if ($(this).hasClass("day2")) offset = 2;
  else if ($(this).hasClass("day3")) offset = 3;

  var date = new Date();
  date.setDate(date.getDate() - offset);

  var input = $(this).closest(".form-value").find("input[type=date]")[0];
  $(input).val(date.toISOString().slice(0, 10)).change();
});

//Textarea disable line breaks
$(".nolinebreaks").on("keyup", function () {
  str = $(this).val();
  $(this).val(str.replace("\n", " ").replace("  ", " "));
});
