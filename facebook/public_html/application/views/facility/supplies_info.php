<style>
    body {
        background-color:#ffffff;
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
            font-size:20px;
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
            font-size:16px;
        }
    }
</style>
<div class="main-div">
    <label class="bg-primary text-left" style=" width:100%; padding-left: 10px;"> ระบบงานพัสดุ </label>
    <table class="table table-bordered table-striped table-center main-table">
        <tbody class="text">
            <tr>
                <td style="background-color:#ffffff;">
                    <img id="store_pic" src="<?php echo base_url(PROJECTPATH."/assets/uploads/facility/".$data->store_pic); ?>" alt="" height="300" width="300"/>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    รหัส : <?php echo $data->store_code;?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    ชื่อคุรภัณฑ์ : <?php echo $data->store_name;?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    ปีงบประมาณ : <?php echo $data->budget_year;?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    วันที่รับ : <?php echo !empty($data->receive_date) ? $this->center_function->ConvertToThaiDate($data->receive_date,'1','0') : "";?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    ราคา : <?php echo number_format($data->store_price,2);?> บาท
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    ผู้ขาย : <?php echo $data->seller_name;?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    ที่อยู่ : <?php echo $data->address;?>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    เบอร์โทร : <?php echo $data->phone_number;?>
                </td>
            </tr>
        </tbody>
    </table>
</div>