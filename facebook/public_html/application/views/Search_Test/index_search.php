<?php

?>


<div class="layout-content">
    <div class="layout-content-body ">

<h1> ค้นหา </h1>
<div class = "panel panel-body" style="height:100%  ; text-align:center" >

    <form id="form_search" name="form_search" method="get" action="">
        <input type="text" name="keyword" id="keyword" />
        <input type="submit" name="button" id="button" value="ค้นหา" />
    </form>

    <div class="bs-example" data-example-id="striped-table" style ="padding-top:10px ; margin-left:500px " >
        <table>


            <thead>
            <tr class="bg-primary" >
                <!-- หัวข้อตาราง -->
                <th style="text-align:center">ความสนใจ </th>
                <th style="text-align:center">Audience Size</th>
                <th style="text-align:center">หัวข้อ</th>
                <th style="text-align:center">Path</th>


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