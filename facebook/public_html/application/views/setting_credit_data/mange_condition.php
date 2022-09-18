<style type="text/css">
    .clone-content{
        display: block;
        position: absolute;
        right: 40px;
    }
</style>
<div class="layout-content">
    <div class="layout-content-body">
		<h1 style="margin-bottom: 0">ตั้งค่าเงื่อนไข <?=@$_GET['key']?></h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<div class="form-group text-center">&nbsp;</div>
                    <div class="clone-content">
                        <button type="button" class="btn btn-primary" style="width: 46px" data-toggle="modal" data-target="#modal-clone">
                            <i class="fa fa-clone"></i>
                        </button>
                    </div>
					<form id='form_save' action="<?php echo base_url(PROJECTPATH.'/setting_credit_data/save_manage_condition?id='.@$_GET['id']).'&key='.@$_GET['key']; ?>" method="post" data-toggle="validator" novalidate="novalidate" >	
					  
          <div class="container-fluid">
            <ul class="nav nav-tabs" id="menu_condition">
              <li class="active tabpill"><a data-toggle="tab" data-tabpill="1" href="#home1" id="tab1">เงื่อนไข 1</a></li>
              <?php
                $c = 1;
                foreach ($main_condition as $key => $value) {
                  if($c==1){
                    $c++;
                    continue;
                  }
                  echo '<li class="tabpill"><a data-toggle="tab" data-tabpill="'.($c).'" href="#home'.($c).'" id="tab'.($c).'">เงื่อนไข '.($c++).'</a></li>';
                }
              ?>
              <li class="condition_create tabpill"><a data-toggle="tab" href="#"><i class="fa fa-plus-square-o" style="font-size:24px;"></i> เพิ่ม</a></li>
            </ul>

            <div class="tab-content">
            <?php
              if(sizeof($main_condition)>=1) {
              foreach ($main_condition as $key_main => $value_main) {
                $count_garantor = 1;
                // echo "<pre>";var_dump( $value['condition_garantor']);echo "</pre>";
                ?>
                  <div id="home<?=($key_main+1)?>" class="tab-pane fade in active">
                  <br>
                  <div class="row">
                    <label class="col-sm-1 control-label text-right" >ชื่อเงื่อนไข</label>
                    <div class="col-sm-9 pairOpnone">
                      <input name="condition_name[home<?=($key_main+1)?>][]" id="max_period_month" class="form-control m-b-1 check_number" type="text" value="<?=@$value_main['detail_text']?>">
                    </div>
                    <div class="col-md-1">
                          <button class="btn btn-danger" type="button" onclick="confirm_delete_condition('<?php echo $_GET['id']; ?>', '<?php echo $_GET['key']; ?>', '<?php echo $value_main['col_id']?>')">
                              <i class="fa fa-trash-o"></i>
                          </button>
                    </div>
                  </div>

                  <div id="home<?=($key_main+1)?>_condition">
                    <?php
                      if($value_main['condition']==""){
                        $value_main['condition'] = array("");
                      }
                      $is_last = false;
                      $c = 1;
                      foreach ($value_main['condition'] as $key => $val_condition) {
                        if(sizeof($value_main['condition'])-1 == $key )
                          $is_last = true;
                        ?>
                          
                          <div class="row" data-form="<?=($key_main+1)?>">
                          <br>
                            <label class="col-sm-1 control-label text-right" >เงื่อนไข</label>
                            <div class="col-sm-4 pairOp">
 
                              <div class="row">
                                <div class="col-sm-4">
                                  <input type="text" name="pair1_a[home<?=($key_main+1)?>][]" value="<?=@$val_condition['pair1']['a']?>" list="condition_list" class="form-control">
                                </div>
                                <div class="col-sm-4">
                                  <select name="op1[home<?=($key_main+1)?>][]" class="form-control">
                                    <option value="">ไม่มี</option>
                                    <option value="+" <?=(@$val_condition['pair1']['op']=='+' ? "selected" : "")?> >+</option>
                                    <option value="-" <?=(@$val_condition['pair1']['op']=='-' ? "selected" : "")?> >-</option>
                                    <option value="*" <?=(@$val_condition['pair1']['op']=='*' ? "selected" : "")?> >*</option>
                                    <option value="/" <?=(@$val_condition['pair1']['op']=='/' ? "selected" : "")?> >/</option>
                                    <option value="^" <?=(@$val_condition['pair1']['op']=='^' ? "selected" : "")?> >^</option>
                                  </select>
                                </div>
                                <div class="col-sm-4">
                                  <input type="text" name="pair1_b[home<?=($key_main+1)?>][]" value="<?=@$val_condition['pair1']['b']?>" list="condition_list" class="form-control">
                                </div>
                              </div>

                            </div>
                            <div class="col-sm-1 pairOpnone">
                              <select name="operation[home<?=($key_main+1)?>][]" id="" class="form-control">
                                <option class="form-control m-b-1" value="">เลือก operation</option>
                                <option value=">" <?=(@$val_condition['operation']==">" ? "selected" : "")?>>></option>
                                <option value=">=" <?=(@$val_condition['operation']==">=" ? "selected" : "")?>>>=</option>
                                <option value="<" <?=(@$val_condition['operation']=="<" ? "selected" : "")?>><</option>
                                <option value="<=" <?=(@$val_condition['operation']=="<=" ? "selected" : "")?>><=</option>
                                <option value="==" <?=(@$val_condition['operation']=="=" ? "selected" : "")?>>=</option>
                                <option value="!=" <?=(@$val_condition['operation']=="!=" ? "selected" : "")?>>!=</option>
                              </select>
                            </div>
                            <div class="col-sm-4 pairOp">
                              <!-- <input name="value[home<?=($key_main+1)?>][]" id="" class="form-control m-b-1" type="text" value="<?=(@$val_condition['value']!="" ? $val_condition['value'] : "")?>" placeholder="ระบุค่า"> -->
                              <div class="row">
                                <div class="col-sm-4">
                                  <?php
                                    // echo "<pre>";
                                    // var_dump($val_condition);
                                  ?>
                                  <input type="text" name="pair2_a[home<?=($key_main+1)?>][]" value="<?=@$val_condition['pair2']['a']?>" list="condition_list" class="form-control">
                                </div>
                                <div class="col-sm-4">
                                  <select name="op2[home<?=($key_main+1)?>][]" class="form-control">
                                    <option value="">ไม่มี</option>
                                    <option value="+" <?=(@$val_condition['pair2']['op']=='+' ? "selected" : "")?> >+</option>
                                    <option value="-" <?=(@$val_condition['pair2']['op']=='-' ? "selected" : "")?> >-</option>
                                    <option value="*" <?=(@$val_condition['pair2']['op']=='*' ? "selected" : "")?> >*</option>
                                    <option value="/" <?=(@$val_condition['pair2']['op']=='/' ? "selected" : "")?> >/</option>
                                    <option value="^" <?=(@$val_condition['pair2']['op']=='^' ? "selected" : "")?> >^</option>
                                  </select>
                                </div>
                                <div class="col-sm-4">
                                  <input type="text" name="pair2_b[home<?=($key_main+1)?>][]" value="<?=@$val_condition['pair2']['b']?>" list="condition_list" class="form-control">
                                </div>
                              </div>
                            
                            </div>
                            <div class="col-sm-1">
                              <div class="col-sm-6 text-right">
                                <button style="font-size:16px;width: fit-content;<?=($is_last==false ? "display: none;" : "")?>" class="btn btn-primary btn-block plus" data-btn="<?=($c)?>" data-tag="home<?=($key_main+1)?>" type="button"><i class="fa fa-plus"></i></button>
                              </div>
                              <div class="col-sm-6 text-left">
                                <button style="font-size:16px;width: fit-content;" class="btn btn-danger btn-block minus" data-btn="<?=($c++)?>" data-tag="home<?=($key_main+1)?>" type="button"><i class="fa fa-minus"></i></button>
                              </div>
                            </div>
                          </div>
                        <?php
                      }
                    ?>
                  </div>
                  <br>
                  <div class="row">
                      <label class="col-sm-1 control-label text-right" >ผลลัพท์</label>
                      <div class="col-sm-9">
                            <!-- <input type="text" name="result_value[home<?=($key_main+1)?>]" value="<?=$value_main['result_value']?>" class="form-control m-b-1"> -->

                            <div class="row">
                                <div class="col-sm-4 pairOpnone">
                                  <input type="text" name="result_value_a[home<?=($key_main+1)?>]" value="<?=@$value_main['result_value']['a']?>" list="condition_list" class="form-control">
                                </div>
                                <div class="col-sm-4 pairOpnone">
                                  <select name="result_value_op[home<?=($key_main+1)?>]" class="form-control">
                                    <option value="">ไม่มี</option>
                                    <option value="+" <?=(@$value_main['result_value']['op']=='+' ? "selected" : "")?> >+</option>
                                    <option value="-" <?=(@$value_main['result_value']['op']=='-' ? "selected" : "")?> >-</option>
                                    <option value="*" <?=(@$value_main['result_value']['op']=='*' ? "selected" : "")?> >*</option>
                                    <option value="/" <?=(@$value_main['result_value']['op']=='/' ? "selected" : "")?> >/</option>
                                    <option value="^" <?=(@$value_main['result_value']['op']=='^' ? "selected" : "")?> >^</option>
                                  </select>
                                </div>
                                <div class="col-sm-4 pairOpnone">
                                  <input type="text" name="result_value_b[home<?=($key_main+1)?>]" value="<?=@$value_main['result_value']['b']?>" list="condition_list" class="form-control">
                                </div>
                            </div>

                      </div>
                  </div>

                </div>
                <?php
              }//end foreach
              }else{//end if
            ?>
            
              <div id="home1" class="tab-pane fade in active">
                <br>
                <div class="row">
                  <label class="col-sm-1 control-label text-right" >ชื่อเงื่อนไข</label>
                  <div class="col-sm-9">
                    <input name="condition_name[home1][]" id="max_period_month" class="form-control m-b-1 check_number" type="text" value="">
                  </div>
                </div>

                <br>
                <div id="home1_condition">
                  <div class="row" data-form="1">
                    <label class="col-sm-1 control-label text-right" >เงื่อนไข</label>
                    <div class="col-sm-4">
                      <select name="condition[home1][]" id="" class="form-control">
                        <option class="form-control m-b-1" value="">เลือกตัวแปร</option>
                        <?php
                          foreach ($meta_condition as $key => $value) {
                            echo "<option value='".$value['id']."'>".$value['detail_text']."</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <div class="col-sm-1">
                      <select name="operation[home1][]" id="" class="form-control">
                        <option class="form-control m-b-1" value="">เลือก operation</option>
                        <option value=">">></option>
                        <option value=">=">>=</option>
                        <option value="<"><</option>
                        <option value="<="><=</option>
                        <option value="==">=</option>
                        <option value="!=">!=</option>
                      </select>
                    </div>
                    <div class="col-sm-4">
                      <input name="value[home1][]" id="" class="form-control m-b-1" type="text" value="" placeholder="ระบุค่า">
                    </div>
                    <div class="col-sm-1">
                      <div class="col-sm-6 text-right">
                        <button style="font-size:16px;width: fit-content;" class="btn btn-primary btn-block plus" data-btn="1" data-tag="home1" type="button"><i class="fa fa-plus"></i></button>
                      </div>
                      <div class="col-sm-6 text-left">
                        <button style="font-size:16px;width: fit-content;display: none;" class="btn btn-danger btn-block minus" data-btn="1" data-tag="home1" type="button"><i class="fa fa-minus"></i></button>
                      </div>
                    </div>
                  </div>
                </div>

                <br>
                <div class="row">
                    <label class="col-sm-1 control-label text-right" >ผลลัพท์</label>
                    <div class="col-sm-4">
                          <input type="text" name="result_value" class="form-control m-b-1">
                    </div>
                </div>

              </div>
            <?php
              }
            ?>
            </div><!-- end tab-content -->
            
          </div>

						
						<div class="form-group text-center">&nbsp;</div>

						<div class="form-group text-center">
							<button type="button" class="btn btn-primary min-width-100" onclick="submit_form()">ตกลง</button>
							<a href="?"><button class="btn btn-danger min-width-100" type="button">ยกเลิก</button></a>
						</div>

            <!-- Load React. -->
            <!-- Note: when deploying, replace "development.js" with "production.min.js". -->
            <script src="https://unpkg.com/react@16/umd/react.development.js" crossorigin></script>
            <script src="https://unpkg.com/react-dom@16/umd/react-dom.development.js" crossorigin></script>
            <!-- Load our React component. -->
            <script src="<?=base_url();?>assets/js/react/setting_credit_data/manage_garantor.js" type="text/babel"></script>

					</form>
				</div>
			</div>
		</div>
	</div>
    <!-- Modal -->
    <div id="modal-clone" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <form action="<?php echo base_url('/setting_credit_data/duplicate'); ?>" enctype="text/plain" method="get">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Clone Condition</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="run">
                    <input type="hidden" name="name" value="<?php echo $_GET['key']; ?>">
                    <input type="hidden" name="to_id" value="<?php echo $_GET['id']; ?>">
                    <div class="row">
                        <div class="form-group">
                            <label class="control-label col-sm-3">เงื่อนไข:</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="form_id">
                                    <?php  foreach ($form_list as $key => $item){ ?>
                                        <option value="<?php echo $item['id']; ?>"> id : <?php echo $item['id']?>, name: <?php echo $item['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><span><i class="fa fa-clone"></i> Clone</span></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
                </form>
            </div>

        </div>
    </div>
</div>

<datalist id="condition_list">
<?php
  foreach ($meta_condition as $key => $value) {
    echo '<option value="'.$value['detail_text'].'">';
  }
?>
</datalist>

<style>
  .pairOp, .pairOpnone{
    background-color: #efefef;
    padding: 5px;
    border-radius: 5px;
  }

  .pairOpnone{
    background-color: #fff !important;
  }
</style>

<script>
  var tab = <?=sizeof($main_condition) == 0 ? 1 : sizeof($main_condition)?>;
  var template_main = jQuery("#home1").get(0).outerHTML;
  // var template_condition = jQuery("#home1_condition").html();
  var template_condition = `<div id="home1_condition">
                  <div class="row" data-form="1">
                    <br>
                    <label class="col-sm-1 control-label text-right" >เงื่อนไข</label>
                    <div class="col-sm-4 pairOp">
                      <div class="row">
                          <div class="col-sm-4">
                            <input type="text" name="pair1_a[home1][]" value="" list="condition_list" class="form-control">
                          </div>
                            <div class="col-sm-4">
                              <select name="op1[home1][]" class="form-control">
                                <option value="">ไม่มี</option>
                                <option value="+">+</option>
                                <option value="-">-</option>
                                <option value="*">*</option>
                                <option value="/">/</option>
                                <option value="^">^</option>
                              </select>
                            </div>
                          <div class="col-sm-4">
                            <input type="text" name="pair1_b[home1][]" value="" list="condition_list" class="form-control">
                          </div>
                      </div>
                    </div>
                    <div class="col-sm-1 pairOpnone">
                      <select name="operation[home1][]" id="" class="form-control">
                        <option class="form-control m-b-1" value="">เลือก operation</option>
                        <option value=">">></option>
                        <option value=">=">>=</option>
                        <option value="<"><</option>
                        <option value="<="><=</option>
                        <option value="==">=</option>
                        <option value="!=">!=</option>
                      </select>
                    </div>
                    <div class="col-sm-4 pairOp">
                      <div class="row">
                          <div class="col-sm-4">
                            <input type="text" name="pair2_a[home1][]" value="" list="condition_list" class="form-control">
                          </div>
                            <div class="col-sm-4">
                              <select name="op2[home1][]" class="form-control">
                                <option value="">ไม่มี</option>
                                <option value="+">+</option>
                                <option value="-">-</option>
                                <option value="*">*</option>
                                <option value="/">/</option>
                                <option value="^">^</option>
                              </select>
                            </div>
                          <div class="col-sm-4">
                            <input type="text" name="pair2_b[home1][]" value="" list="condition_list" class="form-control">
                          </div>
                      </div>
                    </div>
                    <div class="col-sm-1">
                      <div class="col-sm-6 text-right">
                        <button style="font-size:16px;width: fit-content;" class="btn btn-primary btn-block plus" data-btn="1" data-tag="home1" type="button"><i class="fa fa-plus"></i></button>
                      </div>
                      <div class="col-sm-6 text-left">
                        <button style="font-size:16px;width: fit-content;display: none;" class="btn btn-danger btn-block minus" data-btn="1" data-tag="home1" type="button"><i class="fa fa-minus"></i></button>
                      </div>
                    </div>
                  </div>
                </div>`;
  // var template_garantor = jQuery("#home1_garantor").html();
  var template_garantor = `<div id="home1_garantor">
                  <div class="row" data-form="1">
                    <label class="col-sm-2 control-label text-right" >เงื่อนไขคนค้ำบุลคลที่ 1</label>
                    <div class="col-sm-3">
                      <select name="condition_garantor[home1][]" id="" class="form-control">
                        <option class="form-control m-b-1" value="">เลือกตัวแปร</option>
                        <?php
                          foreach ($meta_condition as $key => $value) {
                            echo "<option value='".$value['id']."'>".$value['detail_text']."</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <div class="col-sm-2">
                      <select name="operation_garantor[home1][]" id="" class="form-control">
                        <option class="form-control m-b-1" value="">เลือก operation</option>
                        <option value=">">></option>
                        <option value=">=">>=</option>
                        <option value="<"><</option>
                        <option value="<="><=</option>
                        <option value="==">=</option>
                        <option value="!=">!=</option>
                      </select>
                    </div>
                    <div class="col-sm-3">
                      <input name="value_garantor[home1][]" id="max_period_month" class="form-control m-b-1" type="text" value="" placeholder="ระบุค่า">
                    </div>
                    <div class="col-sm-2">
                      <div class="col-sm-6 text-right">
                        <button style="font-size:16px;width: fit-content;" class="btn btn-primary btn-block plus-garantor" data-btn="1" data-tag="home1" type="button"><i class="fa fa-plus"></i></button>
                      </div>
                      <div class="col-sm-6 text-left">
                        <button style="font-size:16px;width: fit-content;display: none;" class="btn btn-danger btn-block minus-garantor" data-btn="1" data-tag="home1" type="button"><i class="fa fa-minus"></i></button>
                      </div>
                    </div>
                  </div>
                </div>`;
  var main_condition = <?=$main_condition?>;
  
  setTimeout(() => {
    $("#tab"+2).trigger('click');
    $("#tab"+1).trigger('click');
  }, 100);
  // $(function() {
  //   var c = 1;
  //   main_condition.forEach(element => {
  //     // console.log(element);
      
  //     console.log("input[name='condition_name[home"+c+"][]']");
  //     if(c > 1){
  //       fn_async();
  //     }
  //     var i = 0;
  //     $("input[name='condition_name[home"+c+"][]']").val(element.detail_text);
  //     if(element.condition.length >= 1){
  //       element.condition.forEach(condition => {
  //         $("input[name='value[home"+c+"][]']").val(condition.value);
  //       });
        
  //     }
  //     c++;
  //   });


  // });

  function condition_create(){
    return new Promise(resolve => {
      tab++;
      var $activeTab = $('.tab-content .tab-pane.active');
      var activeId = $activeTab.attr('id');
      $( ".condition_create" ).before( '<li class="tabpill" ><a id="tab'+tab+'" data-toggle="tab" data-tabpill="'+tab+'" href="#home'+tab+'">เงื่อนไข '+tab+'</a></li>' );
      
      var template = template_main;
      var res = template.replace(/home1/gi, "home"+tab);
      $( ".tab-content" ).append(res);

      setTimeout(() => {
        $("#tab"+tab).trigger('click');
        // alert("tab");
        resolve("success");
      }, 50, tab);

			
		});
  }

  async function fn_async(){
    await condition_create()
  }

  
  console.log(main_condition);
  $( ".condition_create" ).click(function() {
    fn_async();
  });


  $( "body" ).delegate('.plus', 'click', function() {
    var btn_id = $(this).data("btn") + 1;
    var activeTab = $('.tab-content .tab-pane.active');
    var activeId = activeTab.attr('id');
    var template = template_condition;
    template = template.replace(/data-btn=\"1\"/gi, 'data-btn="'+(btn_id)+'"');
    template = template.replace(/data-form=\"1\"/gi, 'data-form="'+(btn_id)+'"');
    template = template.replace(/data-tag=\"home1\"/gi, 'data-tag="'+(activeId)+'"');
    template = template.replace(/home1/gi, (activeId));
    jQuery("#"+activeId+"_condition").append(template);
    $(this).hide();
    $('.minus[data-btn="'+(btn_id-1)+'"]').show();
    $('.minus[data-btn="'+(btn_id)+'"]').show();
  });

  $( "body" ).delegate('.minus', 'click', function() {
    
    var btn_id = $(this).data("btn");
    var activeTab = $('.tab-content .tab-pane.active');
    var activeId = activeTab.attr('id');
    if(btn_id<=1){
      return;
    }
    $('.plus[data-btn="'+(btn_id-1)+'"][data-tag="'+activeId+'"]').show();
    $('#'+activeId+'_condition').children().last().remove();
  });

  $( "body" ).delegate('.plus-garantor', 'click', function() {
    var btn_id = $(this).data("btn") + 1;
    var activeTab = $('.tab-content .tab-pane.active');
    var activeId = activeTab.attr('id');
    var template = template_garantor;
    template = template.replace(/data-btn=\"1\"/gi, 'data-btn="'+(btn_id)+'"');
    template = template.replace(/data-form=\"1\"/gi, 'data-form="'+(btn_id)+'"');
    template = template.replace(/data-tag=\"home1\"/gi, 'data-tag="'+(activeId)+'"');
    template = template.replace(/เงื่อนไขคนค้ำบุลคลที่ 1/gi, "เงื่อนไขคนค้ำบุลคลที่ "+ btn_id);
    template = template.replace(/home1/gi, (activeId));
    jQuery("#"+activeId+"_garantor").append(template);
    $(this).hide();
    $('.minus-garantor[data-btn="'+(btn_id-1)+'"]').show();
    $('.minus-garantor[data-btn="'+(btn_id)+'"]').show();
  });

  $( "body" ).delegate('.minus-garantor', 'click', function() {
    
    var btn_id = $(this).data("btn");
    var activeTab = $('.tab-content .tab-pane.active');
    var activeId = activeTab.attr('id');
    if(btn_id<=1){
      return;
    }
    $('.plus-garantor[data-btn="'+(btn_id-1)+'"][data-tag="'+activeId+'"]').show();
    $('#'+activeId+'_garantor').children().last().remove();
  });
  
  function submit_form(){

    $('#form_save').submit();

    // $('#form_save').submit();
  }


  function confirm_delete_condition(term_id, name, col_id) {

      swal({
              title: "ต้องการดำเนินการต่อหรือไม่?",
              text: "ยื่นยันการลบเงื่อนไขนี้ จะไม่สามารถกู้คืนเงื่อนไขนี้ได้?",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: '#DD6B55',
              confirmButtonText: 'ตกลง',
              cancelButtonText: "ยกเลิก",
              closeOnConfirm: false,
              closeOnCancel: false
          },
          function (isConfirm) {
              if (isConfirm) {
                  $.get('/setting_credit_data/del_condition', {id: term_id, name: name, col_id: col_id, action: 'run'}, function () {
                      swal("สำเร็จ", "ลบเงื่ิิอนไขสำเร็จแล้ว", "success");
                  });

              } else {
                  swal("ยกเลิก", "ยกเลิกการลบเงื่อนไขนี้", "error");
                  // e.preventDefault();
              }
          });
  }

</script>
