<?php

use yii\helpers\Html;
use yii\helpers\Url;
use application\assets\AppAsset;
use application\utilities\SessionUtils;
use application\utilities\MenuUtils;
use application\utilities\PermissionUtil;
use application\models\PersonalSchedule\PersonalScheduleUtil;
use application\utilities\DatetimeUtils;
use application\models\Customer\Customer;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="description" content="overview &amp; stats"/>
    <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <style>
        .custom-combobox {
            position: relative;
            display: inline-block;
        }
        .custom-combobox-toggle {
            position: absolute;
            top: 0;
            bottom: 0;
            margin-left: -1px;
            padding: 0;
        }
        .custom-combobox-input {
            margin: 0;
            padding: 5px 10px;
        }
        .ui-autocomplete {
            max-height: 100px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
        }
        /* IE 6 doesn't support max-height
         * we use height instead, but this forces the menu to always be this tall
         */
        * html .ui-autocomplete {
            height: 100px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $( function() {
            $.widget( "custom.combobox", {
                _create: function() {
                    this.wrapper = $( "<span>" )
                        .addClass( "custom-combobox" )
                        .insertAfter( this.element );

                    this.element.hide();
                    this._createAutocomplete();
                    this._createShowAllButton();
                },

                _createAutocomplete: function() {
                    var selected = this.element.children( ":selected" ),
                        value = selected.val() ? selected.text() : "";

                    this.input = $( "<input>" )
                        .appendTo( this.wrapper )
                        .val( value )
                        .attr( "title", "" )
                        .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
                        .autocomplete({
                            delay: 0,
                            minLength: 0,
                            source: $.proxy( this, "_source" )
                        })
                        .tooltip({
                            classes: {
                                "ui-tooltip": "ui-state-highlight"
                            }
                        });

                    this._on( this.input, {
                        autocompleteselect: function( event, ui ) {
                            ui.item.option.selected = true;
                            this._trigger( "select", event, {
                                item: ui.item.option
                            });
                        },

                        autocompletechange: "_removeIfInvalid"
                    });
                },

                _createShowAllButton: function() {
                    var input = this.input,
                        wasOpen = false;

                    $( "<a>" )
                        .attr( "tabIndex", -1 )
                        .tooltip()
                        .appendTo( this.wrapper )
                        .button({
                            icons: {
                                primary: "dropdown-toggle"
                            },
                            text: false
                        })
                        .removeClass( "ui-corner-all" )
                        .addClass( "ace-icon fa fa-caret-down icon-only" )
                        .on( "mousedown", function() {
                            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
                        })
                        .on( "click", function() {
                            input.trigger( "focus" );

                            // Close if already visible
                            if ( wasOpen ) {
                                return;
                            }

                            // Pass empty string as value to search for, displaying all results
                            input.autocomplete( "search", "" );
                        });
                },

                _source: function( request, response ) {
                    var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                    response( this.element.children( "option" ).map(function() {
                        var text = $( this ).text();
                        if ( this.value && ( !request.term || matcher.test(text) ) )
                            return {
                                label: text,
                                value: text,
                                option: this
                            };
                    }) );
                },

                _removeIfInvalid: function( event, ui ) {

                    // Selected an item, nothing to do
                    if ( ui.item ) {
                        return;
                    }

                    // Search for a match (case-insensitive)
                    var value = this.input.val(),
                        valueLowerCase = value.toLowerCase(),
                        valid = false;
                    this.element.children( "option" ).each(function() {
                        if ( $( this ).text().toLowerCase() === valueLowerCase ) {
                            this.selected = valid = true;
                            return false;
                        }
                    });

                    // Found a match, nothing to do
                    if ( valid ) {
                        return;
                    }

                    // Remove invalid value
                    this.input
                        .val( "" )
                        .attr( "title", value + " didn't match any item" )
                        .tooltip( "open" );
                    this.element.val( "" );
                    this._delay(function() {
                        this.input.tooltip( "close" ).attr( "title", "" );
                    }, 2500 );
                    this.input.autocomplete( "instance" ).term = "";
                },

                _destroy: function() {
                    this.wrapper.remove();
                    this.element.show();
                }
            });

            $( "#combobox" ).combobox();
            $( "#toggle" ).on( "click", function() {
                $( "#combobox" ).toggle();
            });
        } );
    </script>
</head>
<body class="no-skin">
<?php $this->beginBody() ?>
<div id="navbar" class="navbar navbar-default  ace-save-state">
    <div class="navbar-container ace-save-state" id="navbar-container">
        <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
            <span class="sr-only">Toggle sidebar</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <div class="navbar-header pull-left">
            <a href="<?= Url::to(['/']) ?>" class="navbar-brand">
                <small>
                    <i class="fa fa-leaf"></i>
                    AIA CRM
                </small>
            </a>
        </div>

        <div class="navbar-buttons navbar-header pull-right" role="navigation">
            <?php
            $date_from = date('Y-m-d 00:00:00');
            $date_to = date('Y-m-d 23:59:59');
            $sheduleToDaies = PersonalScheduleUtil::getScheduleByDate($date_from, $date_to);

            $tomorrow_from = date('Y-m-d 00:00:00',strtotime($date_from . "+1 days"));
            $tomorrow_to = date('Y-m-d 23:59:59', strtotime($date_from . "+1 days"));
            $sheduleTomorow = PersonalScheduleUtil::getScheduleByDate($tomorrow_from, $tomorrow_to);
            ?>
            <ul class="nav ace-nav">
                <li class="purple dropdown-modal">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
                        <i class="ace-icon fa fa-bell"></i>
                        <span class="badge badge-important"><?= count($sheduleToDaies) ?></span>
                    </a>
                    <ul class="dropdown-menu-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">

                        <li class="dropdown-header">
                            <i class="ace-icon fa fa-check"></i>
                            Kế hoạch hôm nay
                        </li>
                        <li class="dropdown-content ace-scroll">
                            <div class="scroll-track">
                                <div class="scroll-bar"></div>
                            </div>
                            <div class="scroll-content" style="">
                                <ul class="dropdown-menu dropdown-navbar">
                                    <?php
                                    if (count($sheduleToDaies) > 0) {
                                        foreach ($sheduleToDaies as $index => $sheduleToDay) {
                                            $customer = Customer::findOne(['id' => $sheduleToDay->customer_id]);
                                            if (!is_null($customer)) {
                                                ?>
                                                <li>
                                                    <a href="#" class="clearfix">
                                                        <span class="msg-title"><?= $customer->name ?>
                                                            - <?= $customer->phone ?></span>
                                                        <span class="msg-time">
                                                            <i class="ace-icon fa fa-clock-o"></i>
                                                            <span><?= DatetimeUtils::formatDate($sheduleToDay->date, "H:i") ?></span>
                                                        </span>
                                                    </a>
                                                </li>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>

                                </ul>
                            </div>
                        </li>
                        <li class="dropdown-footer">
                            <a href="<?= Url::to(['/personal-schedule']) ?>">
                                Xem hết lịch
                                <i class="ace-icon fa fa-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="grey dropdown-modal">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
                        <i class="ace-icon fa fa-tasks"></i>
                        <span class="badge badge-grey"><?= count($sheduleTomorow) ?></span>
                    </a>
                    <ul class="dropdown-menu-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">

                        <li class="dropdown-header">
                            <i class="ace-icon fa fa-check"></i>
                            Kế hoạch ngày mai
                        </li>
                        <li class="dropdown-content ace-scroll">
                            <div class="scroll-track">
                                <div class="scroll-bar"></div>
                            </div>
                            <div class="scroll-content" style="">
                                <ul class="dropdown-menu dropdown-navbar">
                                    <?php
                                    if (count($sheduleTomorow) > 0) {
                                        foreach ($sheduleTomorow as $index => $sheduleToDay) {
                                            $customer = Customer::findOne(['id' => $sheduleToDay->customer_id]);
                                            if (!is_null($customer)) {
                                                ?>
                                                <li>
                                                    <a href="#" class="clearfix">
                                                        <span class="msg-title"><?= $customer->name ?>
                                                            - <?= $customer->phone ?></span>
                                                        <span class="msg-time">
                                                            <i class="ace-icon fa fa-clock-o"></i>
                                                            <span><?= DatetimeUtils::formatDate($sheduleToDay->date, "H:i") ?></span>
                                                        </span>
                                                    </a>
                                                </li>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>

                                </ul>
                            </div>
                        </li>
                        <li class="dropdown-footer">
                            <a href="<?= Url::to(['/personal-schedule']) ?>">
                                Xem hết lịch
                                <i class="ace-icon fa fa-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="light-blue dropdown-modal">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <!--                        <img class="nav-user-photo" src="/images/person-icon.png" alt="Jason's Photo"/>-->
                        <span class="user-info"><small>Welcome,</small><?= SessionUtils::getUsername() ?></span>
                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <li>
                            <a href="<?= Url::to(['/change-password']) ?>">
                                <i class="ace-icon fa fa-cog"></i>
                                Đổi mật khẩu
                            </a>
                        </li>
                        <li>
                            <a href="<?= Url::to(['/docs/hdsd_xp.pdf']) ?>">
                                <i class="ace-icon fa fa-cog"></i>
                                Hướng dẫn sử dụng
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?= Url::to(['/site/logout']) ?>">
                                <i class="ace-icon fa fa-power-off"></i>
                                Thoát
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div><!-- /.navbar-container -->
</div>
<div class="main-container ace-save-state" id="main-container">
    <script type="text/javascript">
        try {
            ace.settings.loadState('main-container')
        } catch (e) {
        }
    </script>
    <div id="sidebar" class="sidebar  responsive   ace-save-state">
        <script type="text/javascript">
            try {
                ace.settings.loadState('sidebar')
            } catch (e) {
            }
        </script>
        <div class="sidebar-shortcuts" id="sidebar-shortcuts">
            <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
                <button class="btn btn-success">
                    <i class="ace-icon fa fa-signal"></i>
                </button>

                <button class="btn btn-info">
                    <i class="ace-icon fa fa-pencil"></i>
                </button>

                <button class="btn btn-warning">
                    <i class="ace-icon fa fa-users"></i>
                </button>

                <button class="btn btn-danger">
                    <i class="ace-icon fa fa-cogs"></i>
                </button>
            </div>

            <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
                <span class="btn btn-success"></span>

                <span class="btn btn-info"></span>

                <span class="btn btn-warning"></span>

                <span class="btn btn-danger"></span>
            </div>
        </div><!-- /.sidebar-shortcuts -->
        <?php
        $ctl = Yii::$app->controller->id;
        ?>
        <ul class="nav nav-list">

            <li class="<?= $ctl == 'call-result' ? 'active' : '' ?>">
                <a href="<?= Url::to(['/call-result']) ?>"><i class="menu-icon fa fa-phone"></i>
                    <span class="menu-text">Kết quả cuộc gọi</span>
                </a>
            </li>
            <li class="<?= $ctl == 'meeting-result' ? 'active' : '' ?>">
                <a href="<?= Url::to(['/meeting-result']) ?>"><i class="menu-icon fa fa-handshake-o"></i>
                    <span class="menu-text">Kết quả cuộc gặp</span>
                </a>
            </li>
            <li class="<?= $ctl == 'fhc-report' ? 'active' : '' ?>">
                <a href="<?= Url::to(['/fhc-report']) ?>"><i class="menu-icon fa fa-file-text-o"></i>
                    <span class="menu-text">Báo cáo FHC</span>
                </a>
            </li>
            <li class="<?= $ctl == 'potential-customer' ? 'active' : '' ?>">
                <a href="<?= Url::to(['/potential-customer']) ?>"><i class="menu-icon fa fa-address-card-o"></i>
                    <span class="menu-text">Kết quả KHTN</span>
                </a>
            </li>

            <li class="<?= $ctl == 'customer' ? 'active' : '' ?>">
                <a href="<?= Url::to(['/customer']) ?>"><i class="menu-icon fa fa-user-secret"></i>
                    <span class="menu-text">Khách hàng</span>
                </a>
            </li>
            <li class="<?= $ctl == 'daily-review' ? 'active' : '' ?>">
                <a href="<?= Url::to(['/daily-review']) ?>"><i class="menu-icon fa fa-street-view"></i>
                    <span class="menu-text">Daily review</span>
                </a>
            </li>
            <li class="<?= $ctl == 'personal-schedule' ? 'active' : '' ?>">
                <a href="<?= Url::to(['/personal-schedule']) ?>"><i class="menu-icon fa fa-calendar"></i>
                    <span class="menu-text">Kế hoạch cá nhân</span>
                </a>
            </li>
            <?php
            if (!PermissionUtil::isXPRole()) {
                ?>
                <li class="<?= $ctl == 'jfw-schedule' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/jfw-schedule']) ?>"><i class="menu-icon fa fa-calendar"></i>
                        <span class="menu-text">Kế hoạch XP</span>
                    </a>
                </li>
                <?php
            }
            ?>
            <?php
            if (!PermissionUtil::isXPRole()) {
                ?>
                <li class="<?= $ctl == 'department-shedule' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/department-shedule']) ?>"><i class="menu-icon fa fa-calendar"></i>
                        <span class="menu-text">Kế hoạch phòng</span>
                    </a>
                </li>
                <?php
            }
            ?>
            <?php
            if(PermissionUtil::isHodRole() || PermissionUtil::isAdminRole() || PermissionUtil::isXPMRole()){?>
                <li class="<?= $ctl == 'call-statistics-and-potential-customer' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/call-statistics-and-potential-customer']) ?>"><i class="menu-icon fa fa-bar-chart"></i>
                        <span class="menu-text">Thống kê</span>
                    </a>
                </li>
            <?php
            }
            if (PermissionUtil::isHodRole()) { ?>
                <li class="<?= $ctl == 'sis-analysis' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/sis-analysis']) ?>"><i class="menu-icon fa fa-bar-chart"></i>
                        <span class="menu-text">Báo cáo SIS</span>
                    </a>
                </li>
                <?php
            }
            if (PermissionUtil::isAdminRole()) {
                ?>
                <li class="<?= MenuUtils::setSelectedParentMenu([
                    'level',
                    'chanel',
                    'purpose',
                    'marital-status',
                    'department',
                    'job'
                ]) ?>">
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon fa fa-list"></i>
                        <span class="menu-text"> Danh mục </span>
                        <b class="arrow fa fa-angle-down"></b>
                    </a>
                    <b class="arrow"></b>
                    <ul class="submenu">
                        <li class="<?= MenuUtils::setSelectedMenu(['chanel']) ?>">
                            <a href="<?= Url::to(['/chanel']) ?>">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Kênh khách hàng
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="<?= MenuUtils::setSelectedMenu(['purpose']) ?>">
                            <a href="<?= Url::to(['/purpose']) ?>">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Mục đích
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="<?= MenuUtils::setSelectedMenu(['marital-status']) ?>">
                            <a href="<?= Url::to(['/marital-status']) ?>">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Tình trạng hôn nhân
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="<?= MenuUtils::setSelectedMenu(['department']) ?>">
                            <a href="<?= Url::to(['/department']) ?>">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Phòng ban
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li class="<?= MenuUtils::setSelectedMenu(['job']) ?>">
                            <a href="<?= Url::to(['/job']) ?>">
                                <i class="menu-icon fa fa-caret-right"></i>
                                Nghề nghiệp
                            </a>
                            <b class="arrow"></b>
                        </li>
                    </ul>
                </li>
                <li class="<?= $ctl == 'user-management' ? 'active' : '' ?>">
                    <a href="<?= Url::to(['/user-management']) ?>"><i class="menu-icon fa fa-user"></i>
                        <span class="menu-text">Nhân viên</span>
                    </a>
                </li>
            <?php } ?>
        </ul>
        <!-- /.nav-list -->
        <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
            <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state"
               data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
        </div>
    </div>
    <div class="main-content">
        <div class="main-content-inner">
            <?= $content ?>
        </div>
    </div>
    <div class="footer">
        <div class="footer-inner">
            <div class="footer-content">
						<span class="bigger-120">
							<span class="blue bolder">AIA CRM</span>
							Application &copy; 2018
						</span>

            </div>
        </div>
    </div>
</div>

<div id="overlay">
    <img src="<?php echo Yii::$app->request->baseUrl ?>/images/loading.gif" id="img-load"/>
</div>
<script type="text/javascript">

    $(document).ready(function () {
        $('#customer-salary').inputmask("numeric", {
            radixPoint: ".",
            groupSeparator: ",",
            digits: 2,
            autoGroup: true,
            rightAlign: false,
            oncleared: function () {
                self.Value('');
            }
        });
        $('#fhcreport-salary').inputmask("numeric", {
            radixPoint: ".",
            groupSeparator: ",",
            digits: 2,
            autoGroup: true,
            rightAlign: false,
            oncleared: function () {
                self.Value('');
            }
        });
        $('.number-int').number(true, 0, ',', '.');

        if ($('#meetingresult-fhc').val() == 0) {
            $('#fhcreport-khtn').prop('disabled', true);
            $('#fhcreport-salary').prop('disabled', true);
            $('#fhcreport-job').prop('disabled', true);
            $('#fhcreport-demand').attr("disabled", true).trigger("chosen:updated");
        }
        ;

        $('#fhcreport-khtn').focusout(function () {
            $('#meetingresult-khtn').val(this.value);
        });

    });

    $("#meetingresult-fhc").change(function () {
        if (this.checked) {
            this.value = 1;
            $('#fhcreport-khtn').prop('disabled', false);
            $('#fhcreport-salary').prop('disabled', false);
            $('#fhcreport-job').prop('disabled', false);
            $('#fhcreport-demand').attr("disabled", false).trigger("chosen:updated");
        } else {
            this.value = 0;
            $('#fhcreport-khtn').prop('disabled', true);
            $('#fhcreport-salary').prop('disabled', true);
            $('#fhcreport-job').prop('disabled', true);
            $('#fhcreport-khtn').val("");
            $('#fhcreport-salary').val("");
            $('#fhcreport-job').val("");
            $('#fhcreport-demand').val("");
            $('#fhcreport-demand').attr("disabled", true).trigger("chosen:updated");
        }
    });

    $("#meetingresult-customer_id").change(function () {
        if (this.checked) {
            $('#fhcreport-khtn').prop('disabled', false);
            $('#fhcreport-salary').prop('disabled', false);
            $('#fhcreport-job').prop('disabled', false);
            $('#fhcreport-demand').attr("disabled", false).trigger("chosen:updated");
        } else {
            $('#fhcreport-khtn').prop('disabled', true);
            $('#fhcreport-salary').prop('disabled', true);
            $('#fhcreport-job').prop('disabled', true);
            $('#fhcreport-khtn').val("");
            $('#fhcreport-salary').val("");
            $('#fhcreport-job').val("");
            $('#fhcreport-demand').val("");
            $('#fhcreport-demand').attr("disabled", true).trigger("chosen:updated");
        }
    });


    function getSelectedCheckboxGridView() {
        var checkedVals = $("#list-data input[type='checkbox'][name='selection[]'").map(function () {
            var checked = $(this).is(":checked");
            if (checked) {
                return this.value;
            }
        }).get();
        return checkedVals;
    }

    function deleteData(controller, action) {
        var value = getSelectedCheckboxGridView();
        if (value.length > 0) {
            var ok = confirm("Bạn có muốn xóa các dòng đã chọn");
            if (ok) {
                $.ajax({
                    url: '<?php echo Yii::$app->request->baseUrl ?>' + '/' + controller + '/' + action,
                    data: {values: value},
                    type: "POST",
                    beforeSend: function (xhr) {
                        showLoading();
                    },
                    success: function (data) {
                        if (parseInt(data.trim()) === 1) {
                            alert("Đã xóa");
                            window.location.reload();
                        }
                        if (parseInt(data.trim()) === 0) {
                            alert("Có lỗi xảy ra");
                        }
                        if (parseInt(data.trim()) === -1) {
                            alert("Bạn chưa chọn dòng nào để xóa");
                        }
                        hideLoading();
                    },
                    error: function () {
                        alert("Không thể xóa");
                        hideLoading();
                    }
                });
            }
        } else {
            alert("Bạn chưa chọn dòng nào để xóa");
        }
        return false;
    }

    function deleteDataMobile(controller, action, id) {
        var value = [id];
        if (value.length > 0) {
            var ok = confirm("Bạn có muốn xóa?");
            if (ok) {
                $.ajax({
                    url: '<?php echo Yii::$app->request->baseUrl ?>' + '/' + controller + '/' + action,
                    data: {values: value},
                    type: "POST",
                    beforeSend: function (xhr) {
                        showLoading();
                    },
                    success: function (data) {
                        if (parseInt(data.trim()) === 1) {
                            alert("Đã xóa");
                            window.location.reload();
                        }
                        if (parseInt(data.trim()) === 0) {
                            alert("Có lỗi xảy ra");
                        }
                        if (parseInt(data.trim()) === -1) {
                            alert("Bạn chưa chọn dòng nào để xóa");
                        }
                        hideLoading();
                    },
                    error: function () {
                        alert("Không thể xóa");
                        hideLoading();
                    }
                });
            }
        } else {
            alert("Bạn chưa chọn dòng nào để xóa");
        }
        return false;
    }

    function paging(controller) {
        var pageValue = $("#" + controller + "-paging").val();
        window.location.href = '<?php echo Yii::$app->request->baseUrl ?>/' + controller + '?page=' + pageValue + '&per-page=<?=Yii::$app->params['page_size']?>';
    }


</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
