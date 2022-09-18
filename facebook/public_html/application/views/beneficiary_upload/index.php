<?php

//$this->db->select(array('firstname_th','lastname_th'));
//$this->db->from('coop_mem_apply');
//$this->db->where("benefits_attach = '".$fileName."'");
//$row = $this->db->get()->result_array();

?>

    <div class="layout-content">
        <div class="layout-content-body ">

<!--            <div class = "layout-main">-->

                <h1> อัปโหลดพินัยกรรม</h1>
            <div class = "panel panel-body" style="height:100%  ; text-align:center" >
                <form action= "<?php echo base_url(PROJECTPATH.'/Beneficiary/beneficiary_upload_file'); ?>" method="post" enctype="multipart/form-data" onsubmit="return validateFileAmount()" style ="padding-top:10px ; margin-left:500px ">

                    <!--ปรัปให้ปุ่มไปอยู่ด้านขวา -->
                    <div style="display: flex; justify-content: flex-start">
                    <input type="file" name="files[]" multiple >
                        <!--เปลี่ยนสีให้ background = สีส้ม ,  ตัวอักษรสีขาว -->
                    <input type="submit" value="อัปโหลด" name="uploadFile" class="btn " style="border-radius:5px ; background-color: #EA7032  ;color: #fff " >
                    </div>

                </form>

                <div class="bs-example" data-example-id="striped-table" style ="padding-top:10px ; margin-left:500px " >
                    <table>


                        <thead>
                    <tr class="bg-primary" >
                        <!-- หัวข้อตาราง -->
                        <th style="text-align:center">ลำดับ </th>
                        <th style="text-align:center">ชื่อไฟล์</th>
                        <th style="text-align:center">รหัสสมาชิก</th>
                        <th style="text-align:center ; width:200px " >ชื่อ - นามสกุล</th>


                    </tr>
                        </thead>

                    <?php
                    $fileList=$_SESSION["fileList"];
                    if (!empty($fileList)){


                        foreach ($fileList as $index => $fileName) {


                            $this->db->select(array('firstname_th', 'lastname_th'));
                            $this->db->from('coop_mem_apply');
                            $this->db->where("benefits_attach = '" . $fileName . "'");

                            $row = $this->db->get()->result_array();


                        ?>

                        <tr>

                            <td style="text-align:center ; table-layout: auto " ><?php echo $index+1?></td>
                            <td style="text-align:center"><?php echo $fileName?></td>
                            <td style="text-align:center"><?php echo substr($fileName,0,5)?></td>
                            <td ><?php echo  $row[0]['firstname_th'] ." ".$row[0]['lastname_th']  ?></td>



                        </tr>
                    <?php } ?>
                </table>
                    <?php } ?>
                </div>


            </div>
        </div>
    </div>


<!--/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->


<style>
    td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }


</style>



<script>
    // ตรวจสอบไฟล์
    function validateFileAmount() {
        let files = document.querySelector('input[type="file"]').files;

        if (files.length > 20) {

            alert('เกิน 20 ไฟล์ ไม่สามารถอัพโหลดได้');
            return false;
        }
    }
</script>




<!--<div class = "layout-main">-->

<?php
    if (!empty($errorMessage)) {
        echo $errorMessage;
    }
?>

<!--<h3> ตารางอัพโหลดไฟล์ </h3>-->
<!--<form action= "--><?php //echo base_url(PROJECTPATH.'/Beneficiary_upload/index2'); ?><!--" method="post" enctype="multipart/form-data" onsubmit="return validateFileAmount()">-->
<!---->
<!--    <input type="file" name="files[]" multiple>-->
<!--    <input type="submit" value="Upload" name="uploadFile">-->
<!---->
<!--</form>-->
<!---->
<!--<table>-->
<!--    <tr>-->
<!--        <th>ลำดับ</th>-->
<!--        <th>ชื่อไฟล์</th>-->
<!--        <th>รหัสสมาชิก</th>-->
<!--        <th>ชื่อ</th>-->
<!--        <th>นามสกุล</th>-->
<!---->
<!--    </tr>-->
<?php //foreach ($files as $index => $fileName) {
//
//
//    $this->db->select(array('firstname_th','lastname_th'));
//    $this->db->from('coop_mem_apply');
//    $this->db->where("benefits_attach = '".$fileName."'");
//
//    $row = $this->db->get()->result_array();
//
//
//    ?>
<!---->
<!--    <tr>-->
<!--        <td>--><?php //echo $index+1?><!--</td>-->
<!--        <td>--><?php //echo $fileName?><!--</td>-->
<!--        <td>--><?php //echo substr($fileName,0,5)?><!--</td>-->
<!--        <td>--><?php //echo  $row[0]['firstname_th'] ?><!--</td>-->
<!--        <td>--><?php //echo  $row[0]['lastname_th'] ?><!--</td>-->
<!---->
<!---->
<!--    </tr>-->
<?php //} ?>
<!--</table>-->
<!---->
<!--</div>-->