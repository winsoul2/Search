<div class="row gutter-xs">
    <div class="col-xs-12 col-md-12">
        <div class="row m-t-1">
            <div class=" g24-col-sm-6">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;">  <?php echo $data['prename_short'].' '.$data['g_firstname'].' '. $data['g_lastname']; ?>  </p>
                </div>
            </div>
            <div class="g24-col-sm-5">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> เกี่ยวข้องเป็น  <span class="left_indent"> <?php echo $data['relation_name']; ?> </span> </p>
                </div>
            </div>
            <div class=" g24-col-sm-7">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> เลขบัตรประชาชน  <span class="left_indent"> <?php echo $data['g_id_card']?> </span> </p>
                </div>
            </div>
            <div class=" g24-col-sm-6">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> ได้รับส่วนแบ่ง <span class="left_indent"> <?php echo $data['g_share_rate']?> </span>  % </p>
                </div>
            </div>
        </div>
        <h3 class="text-left m-t-1 m-b-1" style="margin-left:20px;"> ที่อยู่ปัจจุบัน </h3>
        <div class="row m-t-1">
            <div class="g24-col-sm-6 form-group">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> บ้านเลขที่  <span style="margin-left : 1.5em;"> <?php echo empty($data['g_address_no']) ? " - " : $data['g_address_no'] ?> </span> </p>
                </div>
            </div>

            <div class="g24-col-sm-6 form-group">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> หมู่  <span style="margin-left : 1.5em;"> <?php echo empty($data['g_address_moo']) ?  " - " : $data['g_address_moo'] ?> </span> </p>
                </div>
            </div>

            <div class="g24-col-sm-6 form-group">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> หมู่บ้าน  <span style="margin-left : 1.5em;"> <?php echo empty($data['g_address_village']) ? " - " : $data['g_address_village']?> </span> </p>
                </div>
            </div>

            <div class="g24-col-sm-6 form-group">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> ถนน  <span style="margin-left : 1.5em;"> <?php echo empty($data['g_address_road'])  ? " - " : $data['g_address_road'] ?> </span> </p>
                </div>
            </div>
        </div>

        <div class="row m-t-1">
            <div class="g24-col-sm-6 form-group">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> ซอย  <span style="margin-left : 1.5em;"> <?php echo empty($data['g_address_soi']) ? " - " : $data['g_address_soi']?> </span> </p>
                </div>
            </div>

            <div class="g24-col-sm-6 form-group">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> ตำบล  <span style="margin-left : 1.5em;"> <?php echo empty($data['g_district_id']) ? " - " :  $data['district_name']; ?> </span> </p>
                </div>
            </div>

            <div class="g24-col-sm-6 form-group">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> อำเภอ  <span style="margin-left : 1.5em;"> <?php echo empty($data['g_amphur_id']) ? " - " :  $data['amphur_name']; ?> </span> </p>
                </div>
            </div>

            <div class="g24-col-sm-6 form-group">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> จังหวัด  <span style="margin-left : 1.5em;"> <?php echo empty($data['g_province_id']) ? " - " :  $data['province_name']; ?> </span> </p>
                </div>
            </div>
        </div>
        <div class="row m-t-1">
            <div class="g24-col-sm-6 form-group">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> รหัสไปรษณีย์  <span style="margin-left : 1.5em;"> <?php echo empty($data['g_zipcode']) ? " - " : $data['g_zipcode'] ?> </span> </p>
                </div>
            </div>


            <div class="g24-col-sm-6 form-group">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> เบอร์บ้าน  <span style="margin-left : 1.5em;"> <?php echo empty($data['g_tel']) ? " - " : $data['g_tel'] ?> </span> </p>
                </div>
            </div>

            <div class="g24-col-sm-6 form-group">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> เบอร์ที่ทำงาน  <span style="margin-left : 1.5em;"> <?php echo empty($data['g_office_tel']) ? " - " : $data['g_office_tel']?> </span> </p>
                </div>
            </div>

            <div class="g24-col-sm-6 form-group">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> มือถือ  <span style="margin-left : 1.5em;"> <?php echo empty($data['g_mobile']) ? " - " :  $data['g_mobile']?> </span> </p>
                </div>
            </div>
        </div>
        <div class="row m-t-1">
            <div class="g24-col-sm-6 form-group">
                <div class=" g24-col-sm-24">
                    <p class="text_indent" style="text-indent:20px;"> Email  <span style="margin-left : 1.5em;"> <?php echo empty($data['g_email']) ? " - " : $data['g_email'] ?> </span> </p>
                </div>
            </div>
        </div>
    </div>
</div>