
function submitAjax(postURL, jsonData) {
    var result;
    $.ajax({
        type: "POST",
        url: postURL,
        data: jsonData,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        cache: false,
        beforeSend: function () {

        },
        success: function (msg) {
            result = msg.d;
        }
    });
    return result;
}
function checkEmptyControl(control) {
    if (control.val() == "") {
        control.focus();
        control.addClass("parsley-error");
        return false;
    } else {
        control.removeClass("parsley-error");
    }
    return true;
}
function checkIsNumber(control) {
    var check = isNaN(control.val());
    if (check) {
        control.focus();
        control.addClass("parsley-error");
        return false;
    } else {
        control.removeClass("parsley-error");
    }
}
function setValueCheckBox(checkall, controls) {
    var listvalue = controls;
    var val = "";
    var check_all = $("#checkall").is(':checked');
    $("tbody #checkItem").each(function (index, element) {
        if (check_all) {
            if (checkall) {
                if ($(this).attr('disabled') != "disabled") {
                    $(this).prop("checked", true);
                }
            }
        } else {
            if (checkall) {
                if ($(this).attr('disabled') != "disabled") {
                    $(this).prop("checked", false);
                }
            }
        }
        var isCheck = $(this).is(':checked');
        if (isCheck) {
            val += $(this).val() + "@";
        }
    });
    if (val == "") {
        $("#checkall").prop('checked', false);
    }
    listvalue.val(val);
}
function setValueCheckBoxGuiMail(checkall, controls) {
    var listvalue = controls;
    var val = "";
    var check_all = $("#checkallGuiMail").is(':checked');
    $("tbody #checkItemGuiMail").each(function (index, element) {
        if (check_all) {
            if (checkall) {
                if ($(this).attr('disabled') != "disabled") {
                    $(this).prop("checked", true);
                }
            }
        } else {
            if (checkall) {
                if ($(this).attr('disabled') != "disabled") {
                    $(this).prop("checked", false);
                }
            }
        }
        var isCheck = $(this).is(':checked');
        if (isCheck) {
            val += $(this).val() + ",";
        }
    });
    if (val == "") {
        $("#checkallGuiMail").prop('checked', false);
    }
    listvalue.val(val);
}

function setValueCheckBoxChon(checkall, controls) {
    var listvalue = controls;
    var val = "";
    var check_all = $("#checkallchon").is(':checked');
    $("tbody #checkItemChon").each(function (index, element) {
        if (check_all) {
            if (checkall) {
                if ($(this).attr('disabled') != "disabled") {
                    $(this).prop("checked", true);
                }
            }
        } else {
            if (checkall) {
                if ($(this).attr('disabled') != "disabled") {
                    $(this).prop("checked", false);
                }
            }
        }
        var isCheck = $(this).is(':checked');
        if (isCheck) {
            val += $(this).val() + "@";
        }
    });
    if (val == "") {
        $("#checkallchon").prop('checked', false);
    }
    listvalue.val(val);
}

function setValueCheckBoxRunnatServer(checkall, obj) {
    var listvalue = obj;
    var val = "";
    var check_all = $("#checkall").is(':checked');
    $("tbody #checkItem").each(function (index, element) {
        if (check_all) {
            if (checkall) {
                $(this).prop("checked", true);
            }
        } else {
            if (checkall) {
                $(this).prop("checked", false);
            }
        }
        var isCheck = $(this).is(':checked');
        if (isCheck) {
            val += $(this).val() + "@";
        }
    });
    if (val == "") {
        $("#checkall").prop('checked', false);
    }
    listvalue.val(val);
}
function setValueCheckBoxRunnatServerCheckSingle(value) {
    var listvalue = $("#ctl00_ContentPlaceHolder1_IDs");
    var check_all = $("#checkall").is(':checked');
    $("tbody #checkItem").each(function (index, element) {
        if ($(this).val() === value) {
            $(this).prop("checked", true);
        } else {
            $(this).prop("checked", false);
        }
    });
    listvalue.val(value);
}
function checkEmpty() {
    var flag = true;
    $("#bodycontrol input").each(function (index) {
        var id = $("#" + $(this).attr("id"));
        var required = $(this).attr("required");
        var value = $(this).val();
        var type = $(this).attr("type");
        if (required == "required") {
            if (value == "") {
                id.addClass("parsley-error");
                if (flag) {
                    flag = false;
                }
            } else {
                id.removeClass("parsley-error");
            }
        }
    });
    return flag;
}
function gotoEditURL(id) {
    var url = window.location.href + "&ctl=Edit&id=" + id;
    window.location.href = url;
}
function SelectAll(id) {
    //get reference of GridView control
    var grid = document.getElementById("<%= GridView1.ClientID %>");
    //variable to contain the cell of the grid
    var cell;

    if (grid.rows.length > 0) {
        //loop starts from 1. rows[0] points to the header.
        for (i = 1; i < grid.rows.length; i++) {
            //get the reference of first column
            cell = grid.rows[i].cells[0];

            //loop according to the number of childNodes in the cell
            for (j = 0; j < cell.childNodes.length; j++) {
                //if childNode type is CheckBox                 
                if (cell.childNodes[j].type == "checkbox") {
                    //assign the status of the Select All checkbox to the cell 
                    //checkbox within the grid
                    cell.childNodes[j].checked = document.getElementById(id).checked;
                }
            }
        }
    }
}
function comfirmDelete() {
    var ok = confirm("Bạn có muốn xóa các mục đã chọn");
    return ok;
}
function clickButton(e, buttonid) {
    var evt = e ? e : window.event;
    var bt = document.getElementById(buttonid);

    if (bt) {
        if (evt.keyCode == 13) {
            bt.click();
            return false;
        }
    }
    return true;
}
function numberFormat(number, decimals, dec_point, thousands_sep) {
    var n = number, prec = decimals;
    n = !isFinite(+n) ? 0 : +n;
    prec = !isFinite(+prec) ? 0 : Math.abs(prec);
    var sep = (typeof thousands_sep == "undefined") ? '.' : thousands_sep;
    var dec = (typeof dec_point == "undefined") ? ',' : dec_point;
    var s = (prec > 0) ? n.toFixed(prec) : Math.round(n).toFixed(prec); //fix for IE parseFloat(0.55).toFixed(0) = 0; 
    var abs = Math.abs(n).toFixed(prec);
    var _, i;
    if (abs >= 1000) {
        _ = abs.split(/\D/);
        i = _[0].length % 3 || 3;
        _[0] = s.slice(0, i + (n < 0)) +
        _[0].slice(i).replace(/(\d{3})/g, sep + '$1');
        s = _.join(dec);
    } else {
        s = s.replace(',', dec);
    }
    return s;
}
String.prototype.replaceAll = function (
strTarget, // The substring you want to replace
strSubString // The string you want to replace in.
) {
    var strText = this;
    var intIndexOfMatch = strText.indexOf(strTarget);
    while (intIndexOfMatch != -1) {
        // Relace out the current instance.
        strText = strText.replace(strTarget, strSubString)

        // Get the index of any next matching substring.
        intIndexOfMatch = strText.indexOf(strTarget);
    }

    // Return the updated string with ALL the target strings
    // replaced out with the new substring.
    return (strText);
}
function removeSigns(text) {
    var str = text;
    //str = str.toLocaleLowerCase();
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
    str = str.replace(/đ/g, "d");
    str = str.replace(/!|@|\$|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\'| |\"|\&|\#|\[|\]|~/g, "-");
    str = str.replace(/-+-/g, "-"); //thay thế 2- thành 1-
    str = str.replace(/^\-+|\-+$/g, "");//cắt bỏ ký tự - ở đầu và cuối chuỗi
    str = str.replace(/[&\/\\#,+()$~%.'":*?“”<>{}]/g, "");
    return str;    
}

function delRow(url, inputname) {
        var arrayID = '';
        $("input[name='"+inputname+"[]']:checked").each(function() {
            var value = $(this).val();
            arrayID += value + ',';
        });
        if (arrayID !== '') {
            var r = confirm("Bạn có chắc muốn xóa dòng này?");
            //alert(arrayID);
            if (r == true) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo  base_url() ?>" + url,
                    data: {id:arrayID},
                    success: function (data) {
                        if (data === "ok") {
                            // loaddingHide();
                            // textHide();
                            $('#FormMessage').show();
                            $('#messageError').text('Xóa thành công');
                            setTimeout(function () {
                                $('#FormMessage').fadeOut();
                                window.location.reload();
                            }, 3000);
                        }
                        else {
                            $('#FormMessagedanger').show();
                            $('#messageErrordanger').text('Lỗi: ' + data);

                        }
                    }
                });
            }
        }
    }
function alertError(msg) {
    var str = "<div class='alert alert-block alert-danger' id='FormMessagedanger'>"+
                "<button class='close' data-dismiss='alert' type='button'>"+
                    "<i class='ace-icon fa fa-times'></i>"+
                "</button>"+
                "<i class='ace-icon fa fa-check green'></i>"+
                "<span id='messageError'>" + msg + "</span>"+
                "</div>";
    return str;
}