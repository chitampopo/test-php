<?php
/**
 * Created by PhpStorm.
 * User: phuocnguyen
 * Date: 05/09/2018
 * Time: 9:28 PM
 */

use application\models\Chanel\ChanelUtil;
use application\models\User\UserUtil;
use application\models\Department\DepartmentUtil;
use application\utilities\DatetimeUtils;
$this->title = "Danh sách cuộc gọi";
$chanels = ChanelUtil::getChanels();
?>
<div class="breadcrumbs" id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="#">Thống kê</a>
        </li>
    </ul>
</div>
<br>
<div class="page-content">
    <?= $this->render('search', ['model' => $model,'departments' => $departments]) ?>
    <style type="text/css">
        tr.info {
            font-weight: bold;
        }

        table {
            text-align: center;

        }

        table tr td {
            vertical-align: middle;
        }

    </style>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th width="10" rowspan="2">#</th>
            <th rowspan="2">XP/SXP/EXP</th>
            <th colspan="2">Số lượng</th>
            <th colspan="<?= count($chanels) ?>">Nguồn</th>
        </tr>
        <tr>
            <th align="center" colspan="2">(goi/hẹn/KHTN)</th>
            <?php
            foreach ($chanels as $index => $chanel) { ?>
                <th><?= $chanel->name ?></th>
                <?php
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php
        $sumCuocGoi = 0;
        $sumCuocGap = 0;
        $sumKHTN = 0;
        $sumSIS = 0;
        $sumFHC = 0;
        $sumHD = 0;

        $array_sum = array();

        $array_chanels = array();
        foreach ($chanels as $index => $chanel) {
            $array_chanels[$chanel->id] = 0;
        }
        $array_sum["goi"] = $array_chanels;
        $array_sum["gap"] = $array_chanels;
        $array_sum["khtn"] = $array_chanels;
        $array_sum["fhc"] = $array_chanels;
        $array_sum["sis"] = $array_chanels;
        $array_sum["hd"] = $array_chanels;
        if(!empty($department)) {
            $results = UserUtil::getUserByDepartment($department);
        }else{
            $results = DepartmentUtil::getDepartments();
        }
        foreach ($results as $index => $item) {
            $_fromDate = DatetimeUtils::convertStringToDate($from_date);
            $_toDate = DatetimeUtils::convertStringToDate($to_date);
            if(empty($department)){
                $strQuery = $model->buildQueryForDepartment($_fromDate, $_toDate, $item->id);
            }else{
                $strQuery = $model->buildQuery($_fromDate, $_toDate, $item->id);
            }
            $data = \Yii::$app->db->createCommand($strQuery)->queryAll();
            $class = "success";
            if ($index % 2 == 0) {
                $class = "";
            }
            ?>
            <tr class="<?= $class ?>">
                <td rowspan="7"><?= $index + 1 ?></td>
                <td rowspan="7"><?= $item->name ?></td>

            </tr>
            <tr class="<?= $class ?>">
                <td>Gọi</td>
                <td>
                    <?php
                    $tong = 0;
                    foreach ($chanels as $index => $chanel) {
                        $tong += $data[0]["chanel_" . $chanel->id];

                    }
                    $sumCuocGoi += $tong;
                    echo $tong;
                    ?>
                </td>
                <?php
                foreach ($chanels as $index => $chanel) { ?>
                    <td><?php
                        echo $data[0]["chanel_" . $chanel->id];
                        $array_sum["goi"][$chanel->id] += $data[0]["chanel_" . $chanel->id];
                        ?></td>
                    <?php
                }
                ?>

            </tr>
            <tr class="<?= $class ?>">
                <td>Gặp</td>
                <?php
                $tong = 0;
                foreach ($chanels as $index => $chanel) {
                    $tong += $data[1]["chanel_" . $chanel->id];
                }
                $sumCuocGap += $tong;
                ?>
                <td><?= $tong ?></td>
                <?php
                foreach ($chanels as $index => $chanel) { ?>
                    <td><?php

                        echo $data[1]["chanel_" . $chanel->id];
                        $array_sum["gap"][$chanel->id] += $data[1]["chanel_" . $chanel->id];
                        ?></td>
                    <?php
                }
                ?>
            </tr>
            <tr class="<?= $class ?>">
                <td>KHTN</td>
                <?php
                $tong = 0;
                foreach ($chanels as $index => $chanel) {
                    $tong += $data[2]["chanel_" . $chanel->id];
                }
                $sumKHTN += $tong;
                ?>
                <td><?= $tong ?></td>
                <?php
                foreach ($chanels as $index => $chanel) { ?>
                    <td><?php
                        echo $data[2]["chanel_" . $chanel->id];
                        $array_sum["khtn"][$chanel->id] += $data[2]["chanel_" . $chanel->id];
                        ?></td>
                    <?php
                }
                ?>
            </tr>
            <tr class="<?= $class ?>">
                <td>FHC</td>
                <?php
                $tong = 0;
                foreach ($chanels as $index => $chanel) {
                    $tong += $data[3]["chanel_" . $chanel->id];
                }
                $sumFHC += $tong;
                ?>
                <td><?= $tong ?></td>
                <?php
                foreach ($chanels as $index => $chanel) { ?>
                    <td><?php
                        echo $data[3]["chanel_" . $chanel->id];
                        $array_sum["fhc"][$chanel->id] += $data[3]["chanel_" . $chanel->id];
                        ?></td>
                    <?php
                }
                ?>
            </tr>
            <tr class="<?= $class ?>">
                <td>SIS</td>
                <?php
                $tong = 0;
                foreach ($chanels as $index => $chanel) {
                    $tong += $data[4]["chanel_" . $chanel->id];
                }
                $sumSIS += $tong;
                ?>
                <td><?= $tong ?></td>
                <?php
                foreach ($chanels as $index => $chanel) { ?>
                    <td><?php
                        echo $data[4]["chanel_" . $chanel->id];
                        $array_sum["sis"][$chanel->id] += $data[4]["chanel_" . $chanel->id];
                        ?></td>
                    <?php
                }
                ?>
            </tr>
            <tr class="<?= $class ?>">
                <td>HĐ</td>
                <?php
                $tong = 0;
                foreach ($chanels as $index => $chanel) {
                    $tong += $data[5]["chanel_" . $chanel->id];
                }
                $sumHD += $tong;
                ?>
                <td><?= $tong ?></td>
                <?php
                foreach ($chanels as $index => $chanel) { ?>
                    <td><?php
                        echo $data[5]["chanel_" . $chanel->id];
                        $array_sum["hd"][$chanel->id] += $data[5]["chanel_" . $chanel->id];
                        ?></td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }


        ?>
        <tr class="info">
            <td rowspan="7"></td>
            <td rowspan="7">Tổng cộng</td>
        </tr>
        <tr class="info">
            <td>Gọi</td>
            <td><?= $sumCuocGoi ?></td>
            <?php
            foreach ($chanels as $index => $chanel) { ?>
                <td>
                    <?php
                    echo $array_sum["goi"][$chanel->id];
                    ?>
                </td>
                <?php
            }
            ?>
        </tr>
        <tr class="info">
            <td>Gặp</td>
            <td><?= $sumCuocGap ?></td>
            <?php
            foreach ($chanels as $index => $chanel) { ?>
                <td>
                    <?php
                    echo $array_sum["gap"][$chanel->id];
                    ?>
                </td>
                <?php
            }
            ?>
        </tr>
        <tr class="info">

            <td>KHTN</td>
            <td><?= $sumKHTN ?></td>
            <?php
            foreach ($chanels as $index => $chanel) { ?>
                <td>
                    <?php
                    echo $array_sum["khtn"][$chanel->id];
                    ?>
                </td>
                <?php
            }
            ?>
        </tr>
        <tr class="info">
            <td>FHC</td>
            <td><?= $sumFHC ?></td>
            <?php
            foreach ($chanels as $index => $chanel) { ?>
                <td>
                    <?php
                    echo $array_sum["fhc"][$chanel->id];
                    ?>
                </td>
                <?php
            }
            ?>
        </tr>
        <tr class="info">
            <td>SIS</td>
            <td><?= $sumSIS ?></td>
            <?php
            foreach ($chanels as $index => $chanel) { ?>
                <td>
                    <?php
                    echo $array_sum["sis"][$chanel->id];
                    ?>
                </td>
                <?php
            }
            ?>
        </tr>
        <tr class="info">
            <td>HĐ</td>
            <td><?= $sumHD ?></td>
            <?php
            foreach ($chanels as $index => $chanel) { ?>
                <td>
                    <?php
                    echo $array_sum["hd"][$chanel->id];
                    ?>
                </td>
                <?php
            }
            ?>
        </tr>
        </tbody>
    </table>
</div>