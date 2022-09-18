<style>
    body, td {
        background-color:#ffffff;
        color:#000000;
    }
    @media (min-width: 768px) {
        .main-div {
            text-align: inherit;
        }
        .main-table {
            width:40%;
        }
        .main-div label {
            font-size:30px;
        }
        .text {
            font-size:18px;
        }
    }
    @media (max-width: 768px) {
        .main-table {
            width:100%;
        }
        .main-div label {
            font-size:20px;
        }
        .text {
            font-size:12px;
        }
    }
</style>
<div class="main-div">
    <table class="table table-bordered table-striped table-center main-table">
        <tbody class="text">
        <?php
            foreach($datas as $data) {
        ?>
            <tr>
                <td class="text-left">
                    [<?php echo !empty($data["available"]) ? '<span class="icon icon-check"></span>' : '&nbsp&nbsp&nbsp';  ?>]
                    <?php echo $data["name"]?>
                </td>
            </tr>
        <?php
            }
        ?>
        </tbody> 
    </table>
</div>