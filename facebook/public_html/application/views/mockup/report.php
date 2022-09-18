<div class="layout-content">
    <div class="layout-content-body">
        <h1 class="title_top">ท.ด.1 / ท.ด.15</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <a class="btn btn-primary btn-lg bt-add">
                    <span class="icon icon-plus-circle"></span>
                    สร้างใบ ท.ด.15
                </a>
                <a class="btn btn-primary btn-lg bt-add"  style="margin-right:20px;">
                    <span class="icon icon-plus-circle"></span>
                    สร้างใบ ท.ด.1
                </a>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="min-height: 100vh">
                    <div class="col-xs-12 col-md-12" style="margin-bottom: 15px">

                        <div class="col-xs-12 col-md-2">เลือกการแสดงข้อมูล</div>
                        <label class="radio-inline col-xs-4 col-md-1">
                            <input type="radio" name="optradio" checked> ทั้งหมด
                        </label>
                        <label class="radio-inline col-xs-4 col-md-1">
                            <input type="radio" name="optradio"> ท.ด.1
                        </label>
                        <label class="radio-inline col-xs-4 col-md-1">
                            <input type="radio" name="optradio"> ท.ด.15
                        </label>
                    </div>
                    <div class="bs-example" data-example-id="striped-table" style="padding: 0 2%">
                        <table class="table table-bordered table-striped table-center">
                            <thead>
                            <tr class="bg-primary">
                                <th width="5%">ลำดับ</th>
                                <th width="15%">วันที่สร้าง</th>
                                <th width="15%">ประเภท</th>
                                <th width="35%">ผู้ทำรายการ</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center">ไม่พบข้อมูล</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
