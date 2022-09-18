<div class="layout-content">
    <div class="layout-content-body">
<?php
if($menu_id == -1) {
    $side_menus = array(array("id" => 0, "name" => "Program", "submenus" => $side_menus));
}
if(@$menu_id == -1 ) {
    $index_menus = $side_menus[0];
    ?>
    <h1 class="title_top">Dashboards</h1>
    <p style="font-family: upbean; font-size: 20px; margin-bottom:5px;">ระบบบริหารงานสหกรณ์</p>

    <div class="row gutter-xs">

        <div class="col-xs-6 col-md-3">
            <div class="card">
                <div class="card-values">
                    <div class="p-x">
                        <small>สมาชิก</small>
                        <h3 class="card-title fw-l"><?php echo number_format($num_rows); ?></h3>
                    </div>
                </div>
                <div class="card-chart"><iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;"></iframe>
                    <canvas data-chart="line" data-animation="false" data-labels="[&quot;Jun 21&quot;, &quot;Jun 20&quot;, &quot;Jun 19&quot;, &quot;Jun 18&quot;, &quot;Jun 17&quot;, &quot;Jun 16&quot;, &quot;Jun 15&quot;]" data-values="[{&quot;backgroundColor&quot;: &quot;rgba(2, 136, 209, 0.03)&quot;, &quot;borderColor&quot;: &quot;#0288d1&quot;, &quot;data&quot;: [25250, 23370, 25568, 28961, 26762, 30072, 25135]}]" data-scales="{&quot;yAxes&quot;: [{ &quot;ticks&quot;: {&quot;max&quot;: 32327}}]}" data-hide="[&quot;legend&quot;, &quot;points&quot;, &quot;scalesX&quot;, &quot;scalesY&quot;, &quot;tooltips&quot;]" height="30" style="display: block; width: 265px; height: 30px;" width="265"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-md-3">
            <div class="card">
                <div class="card-values">
                    <div class="p-x">
                        <small>สมาชิกใหม่เดือนนี้</small>
                        <h3 class="card-title fw-l"><?php echo $num_m;?></h3>
                    </div>
                </div>
                <div class="card-chart"><iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;"></iframe>
                    <canvas data-chart="line" data-animation="false" data-labels="[&quot;Jun 21&quot;, &quot;Jun 20&quot;, &quot;Jun 19&quot;, &quot;Jun 18&quot;, &quot;Jun 17&quot;, &quot;Jun 16&quot;, &quot;Jun 15&quot;]" data-values="[{&quot;backgroundColor&quot;: &quot;rgba(2, 136, 209, 0.03)&quot;, &quot;borderColor&quot;: &quot;#0288d1&quot;, &quot;data&quot;: [8796, 11317, 8678, 9452, 8453, 11853, 9945]}]" data-scales="{&quot;yAxes&quot;: [{ &quot;ticks&quot;: {&quot;max&quot;: 12742}}]}" data-hide="[&quot;legend&quot;, &quot;points&quot;, &quot;scalesX&quot;, &quot;scalesY&quot;, &quot;tooltips&quot;]" height="30" style="display: block; width: 265px; height: 30px;" width="265"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-md-3">
            <div class="card">
                <div class="card-values">
                    <div class="p-x">
                        <small>ซื้อหุ้นเพิ่มเดือนนี้</small>
                        <h3 class="card-title fw-l"><?php echo number_format($share); ?></h3>
                    </div>
                </div>
                <div class="card-chart"><iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;"></iframe>
                    <canvas data-chart="line" data-animation="false" data-labels="[&quot;Jun 21&quot;, &quot;Jun 20&quot;, &quot;Jun 19&quot;, &quot;Jun 18&quot;, &quot;Jun 17&quot;, &quot;Jun 16&quot;, &quot;Jun 15&quot;]" data-values="[{&quot;backgroundColor&quot;: &quot;rgba(2, 136, 209, 0.03)&quot;, &quot;borderColor&quot;: &quot;#0288d1&quot;, &quot;data&quot;: [116196, 145160, 124419, 147004, 134740, 120846, 137225]}]" data-scales="{&quot;yAxes&quot;: [{ &quot;ticks&quot;: {&quot;max&quot;: 158029}}]}" data-hide="[&quot;legend&quot;, &quot;points&quot;, &quot;scalesX&quot;, &quot;scalesY&quot;, &quot;tooltips&quot;]" height="30" style="display: block; width: 265px; height: 30px;" width="265"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-md-3">
            <div class="card">
                <div class="card-values">
                    <div class="p-x">
                        <small>ยอดเรียกเก็บเดือนนี้</small>
                        <h3 class="card-title fw-l"><?php echo number_format($finance_amount,2); ?></h3>
                    </div>
                </div>
                <div class="card-chart"><iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;"></iframe>
                    <canvas data-chart="line" data-animation="false" data-labels="[&quot;Jun 21&quot;, &quot;Jun 20&quot;, &quot;Jun 19&quot;, &quot;Jun 18&quot;, &quot;Jun 17&quot;, &quot;Jun 16&quot;, &quot;Jun 15&quot;]" data-values="[{&quot;backgroundColor&quot;: &quot;rgba(2, 136, 209, 0.03)&quot;, &quot;borderColor&quot;: &quot;#0288d1&quot;, &quot;data&quot;: [116196, 145160, 124419, 147004, 134740, 120846, 137225]}]" data-scales="{&quot;yAxes&quot;: [{ &quot;ticks&quot;: {&quot;max&quot;: 158029}}]}" data-hide="[&quot;legend&quot;, &quot;points&quot;, &quot;scalesX&quot;, &quot;scalesY&quot;, &quot;tooltips&quot;]" height="30" style="display: block; width: 265px; height: 30px;" width="265"></canvas>
                </div>
            </div>
        </div>

    </div>
    <?php
}
else {
    $index_menus = get_menu(@$menus, @$current_path);
    ?>
    <h1 class="title_top"><?php echo $index_menus["name"]; ?></h1>
    <div class="breadcrump">
        <a class="font-menu-main link-line-none" href="/">หน้าแรก</a>
        <?php foreach($menu_paths as $menu_path) { ?>
            <a class="font-menu-main link-line-none" onclick="<?php echo @$menu_path["onclick"]; ?>" href="<?php echo $menu_path["url"]; ?>"> / <?php echo $menu_path["name"]; ?></a>
        <?php } ?>
    </div>
    <?php
}
//echo"<pre>";print_r($menu_id);echo"</pre>";
?>

<div class="row gutter-xs">
    <div class="col-xs-12 col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row m-t-5">
                    <?php
                    $i = 0;
                    foreach($index_menus["submenus"] as $item) {
                        if(chk_permission($item["id"], NULL, NULL, $user, $permissions)) { ?>
                            <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 <?php echo ($i % 5 == 0) ? "col-md-offset-1" : "" ;?>" style="<?php if($item['hidden']=="hidden") { echo 'display:none'; } else { $i++; } ?>">
                                <a class="link-line-none" 
								<?php if(@$item["onclick"]!=''){ ?>
									href="#" onclick="<?php echo @$item["onclick"]; ?>"
								<?php }else{ ?>
									href="<?php echo $item['url'];?>"
								<?php } ?>
								target="<?php echo $item["target"]; ?>"
                                >
                                    <img width="75" src="assets/images/icon_web/<?php echo empty($item['img']) ? $menu['img'] : $item['img'];?>" class="img-responsive m-auto" style="border-radius: 8px; position: relative;">
                                <?php
                                    //cal badge
                                    $unread_doc_count = 0;
                                    if($item["id"] == "253") {
                                        //electronic_document main
                                        $unread_doc = $this->db->select("t1.id, t2.id as t2id")
                                                                ->from("coop_ele_document_user as t2")
                                                                ->join("coop_ele_document as t1", "t1.id = t2.document_id", "inner")
                                                                ->where("t2.user_id = '".$_SESSION['USER_ID']."' AND ((t2.type in ('receiver','cc') AND t2.read_at is null AND t1.status = 5)
                                                                                                                        OR (t2.type in ('reviewer') AND t2.read_at is null AND t1.status = 2)
                                                                                                                        OR (t2.type in ('approve_draft') AND t2.read_at is null AND t1.status = 7)
                                                                                                                        OR (t2.type in ('approver') AND t2.read_at is null AND t1.status = 4))")
                                                                ->group_by("t2.document_id")
                                                                ->get()->result_array();
                                        $unread_doc_count = count($unread_doc);

                                        $comments = $this->db->select("*")
                                                                ->from("coop_ele_document as t1")
                                                                ->join("coop_ele_document_comment as t2", "t1.id = t2.document_id", "inner")
                                                                ->where("t1.user_id = '".$_SESSION['USER_ID']."' AND t1.status = 2 AND (t1.last_access is null OR t1.last_access < t2.created_at)")
                                                                ->get()->result_array();
                                        $unread_doc_count += count($comments);
                                    } else if ($item["id"] == "249") {
                                        // electronic_document => my document
                                        $process_timestamp = date('Y-m-d H:i:s');
                                        $unread_doc = $this->db->select("t1.id, t2.id as t2id")
                                                                ->from("coop_ele_document_user as t2")
                                                                ->join("coop_ele_document as t1", "t1.id = t2.document_id", "inner")
                                                                ->where("t2.user_id = '".$_SESSION['USER_ID']."' AND ((t2.type in ('receiver') AND t2.read_at is null AND t1.status = 5))")
                                                                ->group_by("t2.document_id")
                                                                ->get()->result_array();
                                        $unread_doc_count = count($unread_doc);
                                    } else if ($item["id"] == "241") {
                                        // electronic_document => cc
                                        $process_timestamp = date('Y-m-d H:i:s');
                                        $unread_doc = $this->db->select("t1.id, t2.id as t2id")
                                                                ->from("coop_ele_document_user as t2")
                                                                ->join("coop_ele_document as t1", "t1.id = t2.document_id", "inner")
                                                                ->where("t2.user_id = '".$_SESSION['USER_ID']."' AND ((t2.type in ('cc') AND t2.read_at is null AND t1.status = 5))")
                                                                ->group_by("t2.document_id")
                                                                ->get()->result_array();
                                        $unread_doc_count = count($unread_doc);
                                    } else if($item["id"] == "256") {
                                        // electronic_document => draft
                                        $unread_doc = $this->db->select("t1.id, t2.id as t2id")
                                                                ->from("coop_ele_document_user as t2")
                                                                ->join("coop_ele_document as t1", "t1.id = t2.document_id", "inner")
                                                                ->where("t2.user_id = '".$_SESSION['USER_ID']."' AND ((t2.type in ('reviewer') AND t2.read_at is null AND t1.status = 2)
                                                                                                                        OR (t2.type in ('approve_draft') AND t2.read_at is null AND t1.status = 7))")
                                                                ->group_by("t2.document_id")
                                                                ->get()->result_array();
                                        $unread_doc_count = count($unread_doc);

                                        $comments = $this->db->select("*")
                                                                ->from("coop_ele_document as t1")
                                                                ->join("coop_ele_document_comment as t2", "t1.id = t2.document_id", "inner")
                                                                ->where("t1.user_id = '".$_SESSION['USER_ID']."' AND t1.status = 2 AND (t1.last_access is null OR t1.last_access < t2.created_at)")
                                                                ->get()->result_array();
                                        $unread_doc_count += count($comments);
                                    } else if ($item["id"] == "245") {
                                        // electronic_document => approve
                                        $process_timestamp = date('Y-m-d H:i:s');
                                        $unread_doc = $this->db->select("t1.id, t2.id as t2id")
                                                                ->from("coop_ele_document_user as t2")
                                                                ->join("coop_ele_document as t1", "t1.id = t2.document_id", "inner")
                                                                ->where("t2.user_id = '".$_SESSION['USER_ID']."' AND (t2.type in ('approver') AND t2.read_at is null AND t1.status = 4)")
                                                                ->group_by("t2.document_id")
                                                                ->get()->result_array();
                                        $unread_doc_count = count($unread_doc);
                                    }
                                    if(!empty($unread_doc_count)) {
                                ?>
                                    <span class="badge badge-danger pull-right " style="position: absolute;top: -5%;left: 50%;margin-left: 25px;padding: 2px 8px;font-size:14px;"><?php echo $unread_doc_count?></span>
                                <?php
                                    }
                                ?>
                                    <p class="text-center font-menu-main p-t-sm" style="height: 90px;"><?php echo $item['name'];?></p>
                                </a>
                            </div>
                            <?php
                        }
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>
