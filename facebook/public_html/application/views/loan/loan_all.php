<div class="layout-content">
    <div class="layout-content-body">
<style>
    .form-group { margin-bottom: 0; }
    .border1 { border: solid 1px #ccc; padding: 0 15px; }
    .mem_pic { float: right; width: 150px; }
    .mem_pic img { width: 100%; border: solid 1px #ccc; }
    .mem_pic button { display: block; width: 100%; }

    .hide_error{color : inherit;border-color : inherit;}

    .has-error{color : #d50000;border-color : #d50000;}

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .alert-danger {
        background-color: #F2DEDE;
        border-color: #e0b1b8;
        color: #B94A48;
    }
    .modal-backdrop.in{
        opacity: 0;
    }
    .modal-backdrop {
        position: relative;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1040;
        background-color: #000;
    }
    .modal.fade {
        z-index: 10000000 !important;
    }
</style>
<h1 style="margin-bottom: 0">สรุปรายการกู้เงินคงเหลือ</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <?php $this->load->view('breadcrumb'); ?>
    </div>

    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        
    </div>

</div>
<div class="row gutter-xs">
    <div class="col-xs-12 col-md-12">
        <div class="panel panel-body">


            <div class="row">
                <div class="col-sm-6">
                    <div class="input-with-icon">
                        <input class="form-control input-thick pill m-b-2" type="text" placeholder="ค้นหา" name="search_text" id="search_text" onkeyup="get_search_member()">
                        <span class="icon icon-search input-icon"></span>
                    </div>
                </div>
            </div>

            <div class="bs-example" data-example-id="striped-table">
                <div id="tb_wrap">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>เลขที่สัญญา</th>
                            <th>รหัสสมาชิก</th>
                            <th>ชื่อสกุล</th>
                            <th>วงเงินกู้</th>
							<th>คงเหลือ</th>
                            <!--th>จัดการ</th-->
                        </tr>
                        </thead>
                        <tbody id="table_data">
                        <?php foreach($row as $key => $value){ ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo @$value['contract_number']; ?></td>
                                <td><?php echo @$value['member_id']; ?></td>
                                <td><?php echo @$value['prename_short'].@$value['firstname_th']." ".@$value['lastname_th']; ?></td>
                                <td><?php echo number_format(@$value['loan_amount'],2); ?></td>
								<td><?php echo number_format(@$value['loan_amount_balance'],2); ?></td>
                                <!--td>
                                    <a href="">ค้างชำระ</a>                                     
                                </td-->
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="page_wrap">
            <?php echo $paging ?>
        </div>
    </div>
</div>
    </div>
</div>
<script>
    var base_url = $('#base_url').attr('class');
    function get_search_member(){
        $.ajax({
            type: "POST",
            url: base_url+'loan/get_search_loan',
            data: {
                search_text : $("#search_text").val(),
				form_target : 'index'
            },
            success: function(msg) {
                $("#table_data").html(msg);
            }
        });
    }
</script>