
<style type="text/css">
    input[type=checkbox]{
        transform: scale(1.2);
    }
</style>
<div class="layout-content">
    <div class="layout-content-body">
        <h1 class="title_top">จัดการโอนเงิน</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-8 control-label" for="form-control-2">เลือกสมาชิก</label>
                        <div class="g24-col-sm-4">
                            <select id="form-control-1" class="form-control member_id" >
                                <?php foreach ($page as $key => $val){ ?>
                                    <option value="<?php echo $val['page']; ?>" <?php echo $_GET['page'] == $val['page'] ? 'selected' : '';?> ><?php echo $val['text']?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="g24-col-sm-8">
                            <button class="btn btn-primary" id="controller-form-1">แสดงผล</button>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-8 control-label" for="form-control-2">ค้นหารหัสสมาชิก</label>
                        <div class="g24-col-sm-4">
                            <input id="form-control-2" class="form-control member_id" type="text" value="<?echo $member_id; ?>">
                        </div>
                        <div class="g24-col-sm-8">
                            <button class="btn btn-primary" id="controller-form-2">แสดงผล</button>
                        </div>
                    </div>
                    <div class="bs-example" data-example-id="striped-table">
                        <input type="hidden" name="master_id" value="<?php echo $master_id?>">
                        <table class="table table-bordered table-striped table-center">
                            <thead>
                            <tr class="bg-primary">
                                <th width="10%">รหัสสมาชิก</th>
                                <th width="20%">ชื่อสมาชิก</th>
                                <th width="20%">ยอดปันผลเฉลี่ยคืน</th>
                                <th width="20%">เงินของขวัญ</th>
                                <th width="10%">โอนปันผลเฉี่ยคืน</th>
                                <th width="10%">โอนเงินของขวัญ</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 1;
                            foreach ($list as $key => $row) {

                                $account = $row['account_status'] == '0' ? true : false;

                                $return = $row['return'] - $row['deduct'] > 0 ? true : false;

                                $chk_ign_ret = $row['ignore_return'] == '1' ? false : true;
                                $chk_ign_gif = $row['ignore_gift'] == '1' ? false : true;

                                ?>
                                <tr>
                                    <td><?php echo $row['member_id']; ?></td>
                                    <td style="text-align: left;"><span style="padding-left: 25px"><?php echo $row['fullname']; ?></span></td>
                                    <td style="text-align: right"><?php echo number_format($row['return'] - $row['deduct'],2); ?></td>
                                    <td style="text-align: right"><?php echo number_format($row['gift_varchar'], 2); ?></td>
                                    <td>
                                        <input type="checkbox" data-account="" id="return_chk[<?php echo $row['member_id'];?>]" <?php echo $account && $return && $chk_ign_ret ? 'checked="checked"' : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" id="gift_chk[<?php echo $row['member_id'];?>]" <?php echo $account && $chk_ign_gif ? 'checked="checked"' : ''; ?>>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    var master = $('input[name=master_id]').val();
    $(document).on('click', '#controller-form-2', function(e){
        location.href       = '?id='+master+'&member_id='+$('#form-control-2').val();
    }).on('keypress', '#form-control-2', function(e){
        if(e.keyCode === 13 ){
            location.href       = '?id='+master+'&member_id='+$('#form-control-2').val();
        }
    });

    $(document).on('click', '#controller-form-1', function(e){
        location.href       = '?id='+master+'&page='+$('#form-control-1').val();
    });

    $(document).on('click', 'input[id^=return_chk], input[id^=gift_chk]', function(){
        var member          = "";
        if($(this).attr('id').substring(0,10) === 'return_chk') {
            member = $(this).attr('id').substring(11).substring(-1, 6);
        }else{
            member = $(this).attr('id').substring(9).substring(-1, 6);
        }

        var data            = {};
        data.master_id      = master;
        data.member_id      = member;
        data.ignore_return  = $('input[id^="return_chk['+member+']"]').is(":checked") ? 0 : 1;
        data.ignore_gift    = $('input[id^="gift_chk['+member+']"]').is(":checked") ? 0 : 1;

        jQuery.post('/average_dividend/ignore_transfer', data, function(res){
            if(res.status === true)
                console.log(res);
            console.log(res);
        });
    });
</script>