<style>
@media (min-width: 768px) {
    .modal-dialog {
        width: 700px;
    }
}

</style>
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">ข้อมูลสมาชิก</h4>
        </div>
        <div class="modal-body">
       		<div class="input-with-icon">
					  <!-- <input class="form-control input-thick pill m-b-2" type="text" placeholder="กรอกเลขทะเบียนหรือชื่อ-สกุล" name="search_text" id="search_mem">
            <span class="icon icon-search input-icon"></span> -->
              <div class="row">
              <div class="col">

                <label class="col-sm-2 control-label">รูปแบบค้นหา</label>
                <div class="col-sm-4">
                  <div class="form-group">
                    <select id="search_list" name="search_list" class="form-control m-b-1">
                      <option value="">เลือกรูปแบบค้นหา</option>
                      <option value="member_id">รหัสสมาชิก</option>
						<option value="member_id">รหัสพนักงาน</option>
                      <option value="id_card">หมายเลขบัตรประชาชน</option>
                      <option value="firstname_th">ชื่อสมาชิก</option>
                      <option value="lastname_th">นามสกุล</option>
                    </select>
                  </div>
                </div>
                
                <label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
                <div class="col-sm-4">
                  <div class="form-group">
                    <div class="input-group">
                      <input id="search_text" name="search_text" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>">
                      <span class="input-group-btn">
                        <button type="button" id="member_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                      </span>	
                    </div>
                  </div>
                </div>	
              </div>
            </div>
					</div>

			<div class="bs-example" data-example-id="striped-table">
					  <table class="table table-striped">
              <tbody id="result_member">
              </tbody>
					  </table>
					</div>

        </div>
        <div class="modal-footer">
          <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
        </div>
      </div>
    </div>
  </div>

<script>
    var base_url = $('#base_url').attr('class');
    $('#member_search').click(function(){
        if($('#search_list').val() == '') {
            swal('กรุณาเลือกรูปแบบค้นหา','','warning');
        } else if ($('#search_text').val() == ''){
            swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
        } else {
            $.ajax({  
              url: base_url+"manage_member_share/search_member_add",
              method:"post",  
              data: {
                search_text : $('#search_text').val(), 
                search_list : $('#search_list').val()
              },  
              dataType:"text",  
              success:function(data) {
                $('#result_member').html(data);  
              }  ,
              error: function(xhr){
                  console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
              }
          });  
      }
    });
</script>
