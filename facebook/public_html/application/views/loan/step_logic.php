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
<h1 style="margin-bottom: 0">วิธีคิด ดบ. ฉ.ATM</h1>
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

            <div class="col-md-offset-4 col-md-4">
                <label for="">เลขสมาชิก</label>
                <input type="text" class="form-control" maxligth="6" max=6 placeholder="ระบุเลขสมาชิก" id="member_id">
                
                
                <div class="row" style="margin-top: 15px;margin-bottom: 15px;">
                    <div class="col-md-offset-4 col-md-4 text-center" >
                        <button class="btn btn-primary" id="submit_button" type="button">ดาวน์โหลด</button>
                    </div>
                </div>
            </div>
            

        </div>

    </div>
</div>
    </div>
</div>

<script>
    $( "#submit_button" ).click(function() {
        var member_id = $("#member_id").val();
        window.open(window.location.origin+"/loan/calc_atm_csv?month=02&year=2562&member_id="+member_id+"&excel=1");
        return false;
    });
</script>



