<?php

function chk_permission($id, $s_user = NULL, $s_permissions = NULL, $user, $permissions) {
   // global $user, $permissions;
    //echo"<pre>";print_r($user);print_r($permissions);echo"</pre>";//exit;
    if($s_user !== NULL) {
        $_user = $s_user;
        $_permissions = $s_permissions;
    }
    else {
        $_user = $user;
        $_permissions = $permissions;
    }

    $is_permission = FALSE;

    if(@$_permissions[$id]) {
        $is_permission = TRUE;
    }
    /*if($id == 10 && $_user["user_type_id"] != 1) {
        $is_permission = FALSE;
    }*/
    if($_user["user_type_id"] == 1) {
        $is_permission = TRUE;
    }

    return $is_permission;
}
function get_menu($menus, $url) {
    $_menu = array();
    foreach ($menus as $menu) {
        if ($menu["url"] == $url) {
            return $menu;
        } else if (!empty($menu["submenus"]) && empty($_menu)) {
            $_menu = get_menu($menu["submenus"], $url);
        }
    }
    return $_menu;
}

function get_menu_id($menus, $url) {
    $id = -1;
    if(!empty($menus)) {
        foreach ($menus as $menu) {
            if ($menu["url"] == $url  && $url != '') {
                return $menu["id"];
            } else if (!empty($menu["submenus"]) && $id == -1) {
                $id = get_menu_id($menu["submenus"], $url);
            }
        }
    }

    return $id;
}
?>

<div class="layout-header">
    <div class="navbar navbar-default">
        <div class="navbar-header">
            <a class="navbar-brand navbar-brand-center" href="/">
                <span style="font-size: 28px;font-family: 'upbean';letter-spacing:3px;font-weight:bold;"><?php echo $_SESSION['COOP_SHORT_NAME_EN']; ?></span>
            </a>
            <button class="navbar-toggler visible-xs-block collapsed" type="button" data-toggle="collapse" data-target="#sidenav">
                <span class="sr-only">Toggle navigation</span>
            <span class="bars">
              <span class="bar-line bar-line-1 out"></span>
              <span class="bar-line bar-line-2 out"></span>
              <span class="bar-line bar-line-3 out"></span>
            </span>
            <span class="bars bars-x">
              <span class="bar-line bar-line-4"></span>
              <span class="bar-line bar-line-5"></span>
            </span>
            </button>
            <button class="navbar-toggler visible-xs-block collapsed" type="button" data-toggle="collapse" data-target="#navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="arrow-up"></span>
            <span class="ellipsis ellipsis-vertical">
                <?php
                    $image_properties = array(
                        'src'   => 'assets/images/templete_img/0180441436.jpg',
                        'alt'   => 'Teddy Wilson',
                        'class' => 'ellipsis-object',
                        'width' => '32',
                        'height'=> '32',
                    );
                    img($image_properties);
                ?>
            </span>
            </button>
        </div>
        <div class="navbar-toggleable">
            <nav id="navbar" class="navbar-collapse collapse">
                <button class="sidenav-toggler hidden-xs" title="Collapse sidenav ( [ )" aria-expanded="true" type="button">
                    <span class="sr-only">Toggle navigation</span>
              <span class="bars">
                <span class="bar-line bar-line-1 out"></span>
                <span class="bar-line bar-line-2 out"></span>
                <span class="bar-line bar-line-3 out"></span>
                <span class="bar-line bar-line-4 in"></span>
                <span class="bar-line bar-line-5 in"></span>
                <span class="bar-line bar-line-6 in"></span>
              </span>
                </button>
                <button class="navbar-account-btn" data-toggle="dropdown" aria-haspopup="true" style="font-size: 28px;font-family: upbean;padding-top: 3px;padding-bottom: 3px;">
                    <?php echo $name_coop['title_name']; ?>
                </button>
                <ul class="nav navbar-nav navbar-right">
                    <li class="visible-xs-block">
                        <h4 class="navbar-text text-center">Hi, Teddy Wilson</h4>
                    </li>
                    <li class="hidden-xs hidden-sm">
                        <form class="navbar-search navbar-search-collapsed">
                            <div class="navbar-search-group">
                                <input class="navbar-search-input" type="text" placeholder="Search for people, companies, and more&hellip;">
                                <!-- <button class="navbar-search-toggler" title="Expand search form ( S )" aria-expanded="false" type="submit">
                                  <span class="icon icon-search icon-lg"></span>
                                </button> -->
                                <button class="navbar-search-adv-btn" type="button">Advanced</button>
                            </div>
                        </form>
                    </li>
                    <li class="dropdown" style="display:none;">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true">
                  <span class="icon-with-child hidden-xs">
                    <span class="icon icon-envelope-o icon-lg"></span>
                    <span class="badge badge-danger badge-above right">8</span>
                  </span>
                  <span class="visible-xs-block">
                    <span class="icon icon-envelope icon-lg icon-fw"></span>
                    <span class="badge badge-danger pull-right">8</span>
                    Messages
                  </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
                            <div class="dropdown-header">
                                <a class="dropdown-link" href="assets/compose.html">New Message</a>
                                <h5 class="dropdown-heading">Recent messages</h5>
                            </div>
                            <div class="dropdown-body">
                                <div class="list-group list-group-divided custom-scrollbar">
                                    <a class="list-group-item" href="#">
                                        <div class="notification">
                                            <div class="notification-media">
                                                <?php
                                                    $image_properties = array(
                                                        'src'   => 'assets/images/templete_img/0299419341.jpg',
                                                        'alt'   => 'Harry Jones',
                                                        'class' => 'rounded',
                                                        'width' => '40',
                                                        'height'=> '40',
                                                    );
                                                    img($image_properties);
                                                ?>
                                            </div>
                                            <div class="notification-content">
                                                <small class="notification-timestamp">16 min</small>
                                                <h5 class="notification-heading">Harry Jones</h5>
                                                <p class="notification-text">
                                                    <small class="truncate">Hi Teddy, Just wanted to let you know we got the project! We should be starting the planning next week. Harry</small>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="list-group-item" href="#">
                                        <div class="notification">
                                            <div class="notification-media">
                                                <?php
                                                $image_properties = array(
                                                    'src'   => 'assets/images/templete_img/0310728269.jpg',
                                                    'alt'   => 'Daniel Taylor',
                                                    'class' => 'rounded',
                                                    'width' => '40',
                                                    'height'=> '40',
                                                );
                                                img($image_properties);
                                                ?>
                                            </div>
                                            <div class="notification-content">
                                                <small class="notification-timestamp">2 hr</small>
                                                <h5 class="notification-heading">Daniel Taylor</h5>
                                                <p class="notification-text">
                                                    <small class="truncate">Teddy Boyyyy, label text isn't vertically aligned with value text in grid forms when using .form-control... DT</small>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="list-group-item" href="#">
                                        <div class="notification">
                                            <div class="notification-media">
                                                <?php
                                                $image_properties = array(
                                                    'src'   => 'assets/images/templete_img/0460697039.jpg',
                                                    'alt'   => 'Charlotte Harrison',
                                                    'class' => 'rounded',
                                                    'width' => '40',
                                                    'height'=> '40',
                                                );
                                                img($image_properties);
                                                ?>
                                            </div>
                                            <div class="notification-content">
                                                <small class="notification-timestamp">Sep 20</small>
                                                <h5 class="notification-heading">Charlotte Harrison</h5>
                                                <p class="notification-text">
                                                    <small class="truncate">Dear Teddy, Can we discuss the benefits of this approach during our Monday meeting? Best regards Charlotte Harrison</small>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="list-group-item" href="#">
                                        <div class="notification">
                                            <div class="notification-media">
                                                <?php
                                                $image_properties = array(
                                                    'src'   => 'assets/images/templete_img/0531871454.jpg',
                                                    'alt'   => 'Ethan Walker',
                                                    'class' => 'rounded',
                                                    'width' => '40',
                                                    'height'=> '40',
                                                );
                                                img($image_properties);
                                                ?>
                                            </div>
                                            <div class="notification-content">
                                                <small class="notification-timestamp">Sep 19</small>
                                                <h5 class="notification-heading">Ethan Walker</h5>
                                                <p class="notification-text">
                                                    <small class="truncate">If you need any further assistance, please feel free to contact us. We are always happy to assist you. Regards, Ethan</small>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="list-group-item" href="#">
                                        <div class="notification">
                                            <div class="notification-media">
                                                <?php
                                                $image_properties = array(
                                                    'src'   => 'assets/images/templete_img/0601274412.jpg',
                                                    'alt'   => 'Sophia Evans',
                                                    'class' => 'rounded',
                                                    'width' => '40',
                                                    'height'=> '40',
                                                );
                                                img($image_properties);
                                                ?>
                                            </div>
                                            <div class="notification-content">
                                                <small class="notification-timestamp">Sep 18</small>
                                                <h5 class="notification-heading">Sophia Evans</h5>
                                                <p class="notification-text">
                                                    <small class="truncate">Teddy, Please call me when you finish your work! I have many things to discuss. Don't forget call me !! Sophia</small>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="list-group-item" href="#">
                                        <div class="notification">
                                            <div class="notification-media">
                                                <?php
                                                $image_properties = array(
                                                    'src'   => 'assets/images/templete_img/0777931269.jpg',
                                                    'alt'   => 'Harry Walker',
                                                    'class' => 'rounded',
                                                    'width' => '40',
                                                    'height'=> '40',
                                                );
                                                img($image_properties);
                                                ?>
                                            </div>
                                            <div class="notification-content">
                                                <small class="notification-timestamp">Sep 17</small>
                                                <h5 class="notification-heading">Harry Walker</h5>
                                                <p class="notification-text">
                                                    <small class="truncate">Thank you for your message. I am currently out of the office, with no email access. I will be returning on 20 Jun.</small>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="list-group-item" href="#">
                                        <div class="notification">
                                            <div class="notification-media">
                                                <?php
                                                $image_properties = array(
                                                    'src'   => 'assets/images/templete_img/0872116906.jpg',
                                                    'alt'   => 'Emma Lewis',
                                                    'class' => 'rounded',
                                                    'width' => '40',
                                                    'height'=> '40',
                                                );
                                                img($image_properties);
                                                ?>
                                            </div>
                                            <div class="notification-content">
                                                <small class="notification-timestamp">Sep 15</small>
                                                <h5 class="notification-heading">Emma Lewis</h5>
                                                <p class="notification-text">
                                                    <small class="truncate">Teddy, Please find the attached report. I am truly sorry and very embarrassed about not finishing the report by the deadline.</small>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    <a class="list-group-item" href="#">
                                        <div class="notification">
                                            <div class="notification-media">
                                                <?php
                                                $image_properties = array(
                                                    'src'   => 'assets/images/templete_img/0980726243.jpg',
                                                    'alt'   => 'Emma Lewis',
                                                    'class' => 'rounded',
                                                    'width' => '40',
                                                    'height'=> '40',
                                                );
                                                img($image_properties);
                                                ?>
                                            </div>
                                            <div class="notification-content">
                                                <small class="notification-timestamp">Sep 15</small>
                                                <h5 class="notification-heading">Eliot Morgan</h5>
                                                <p class="notification-text">
                                                    <small class="truncate">Dear Teddy, Please accept this message as notification that I was unable to work yesterday, due to personal illness.m</small>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="dropdown-footer">
                                <a class="dropdown-btn" href="#">See All</a>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown" >
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true">
                  <span class="icon-with-child hidden-xs">
                    <span class="icon icon-bell-o icon-lg"></span>
                    <span class="badge badge-danger badge-above right notification_count"><?php echo count($notification)>0?count($notification):''; ?></span>
                  </span>
                  <span class="visible-xs-block">
                    <span class="icon icon-bell icon-lg icon-fw"></span>
                    <span class="badge badge-danger pull-right notification_count"><?php echo count($notification)>0?count($notification):''; ?></span>
                    Notifications
                  </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
                            <div class="dropdown-header">
                                <!--a class="dropdown-link" href="#">Mark all as read</a-->
                                <h5 class="dropdown-heading">Recent Notifications</h5>
                            </div>
							<div id="notification_space">
							<?php if(count($notification) > 0){ ?>
								<div class="dropdown-body">
									<div class="list-group list-group-divided custom-scrollbar">
										<?php foreach($notification as $key => $value){ ?>
										<a class="list-group-item" href="<?php echo PROJECTPATH."/notification/update_notification?id=".$value['id']; ?>">
											<div class="notification">
												<div class="notification-media">
													<!--span class="icon icon-exclamation-triangle bg-warning rounded sq-40"></span-->
												</div>
												<div class="notification-content">
													<small class="notification-timestamp"><?php echo $this->center_function->mydate2date($value['notification_datetime'],1); ?></small>
													<h5 class="notification-heading"><?php echo $value['notification_title']; ?></h5>
													<p class="notification-text">
														<small class="truncate"><?php echo $value['notification_text']; ?></small>
													</p>
												</div>
											</div>
										</a>
										<?php } ?>
									</div>
								</div>
							<?php } ?>
							</div>
                            <div class="dropdown-footer">
                                <a class="dropdown-btn" href="<?php echo PROJECTPATH."/notification"; ?>">See All</a>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown hidden-xs" id="dropdown_li" open_now="false">
                        <button class="navbar-account-btn" id="dropdown_btn" data-toggle="dropdown" aria-haspopup="true" >
							<?php 
								if(@$user['user_pic']!=''){
									$user_pic = PROJECTPATH."/assets/uploads/user_pic/".$user['user_pic'];
								}else{
									$user_pic = PROJECTPATH."/assets/images/member/1.jpg";
								}
							?>
                            <img class="circle" width="36" height="36" src="<?php echo $user_pic; ?>" alt=""> <?php echo $_SESSION['USER_NAME']; ?>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="<?php echo PROJECTPATH; ?>/main_menu/profile">ข้อมูลส่วนตัว</a></li>
                            <li><a href="<?php echo PROJECTPATH; ?>/main_menu/logout" class="logout">ออกจากระบบ</a></li>
                        </ul>
                    </li>
                    <script>
                        /*function click_dropdown(){
                            if($('#dropdown_li').attr('open_now')=='false'){
                                $('#dropdown_li').addClass('open');
                                $('#dropdown_li').attr('open_now','true')
                            }else{
                                $('#dropdown_li').removeClass('open');
                                $('#dropdown_li').attr('open_now','false')
                            }

                        }*/
                    </script>
                    <li class="visible-xs-block">
                        <a href="<?php echo PROJECTPATH; ?>/main_menu/profile">
                            <span class="icon icon-user icon-lg icon-fw"></span>
                            ข้อมูลส่วนตัว
                        </a>
                    </li>
                    <li class="visible-xs-block">
                        <a href="<?php echo PROJECTPATH; ?>/main_menu/logout" class="logout">
                            <span class="icon icon-power-off icon-lg icon-fw"></span>
                            ออกจากระบบ
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
<div class="layout-main">
    <div class="layout-sidebar">
        <div class="layout-sidebar-backdrop"></div>
        <div class="layout-sidebar-body">
            <div class="custom-scrollbar">
                <nav id="sidenav" class="sidenav-collapse collapse">
                    <?php
                    $start_menu_id = @$menu_paths[0]["id"];
                    ?>
                    <ul class="sidenav">
                        <li class="sidenav-heading">Navigation</li>
                        <li class="sidenav-item<?php if($current_path == "") { ?> active<?php } ?>">
                            <a href="<?php echo PROJECTPATH; ?>/main_menu">
                                <span class="sidenav-icon icon icon-home"></span>
                                <span class="sidenav-label">หน้าแรก</span>
                            </a>
                        </li>
                        <!-- <li class="sidenav-item">
                            <a href="#">
                                <span class="badge badge-success">26</span>
                                <span class="sidenav-icon icon icon-th"></span>
                                <span class="sidenav-label">ข้อความ</span>
                            </a>
                        </li> -->
                        <li class="sidenav-item<?php if($current_path == PROJECTPATH."/index.php/main_menu/profile") { ?> active<?php } ?>">
                            <a href="<?php echo PROJECTPATH; ?>/main_menu/profile">
                                <span class="sidenav-icon icon icon-columns"></span>
                                <span class="sidenav-label">ข้อมูลส่วนตัว</span>
                            </a>
                        </li>

                        <?php

                        if($menu_id == -1) {
                            $side_menus = array(array("id" => 0, "name" => "Program", "submenus" => $side_menus));
                        }

                        foreach($side_menus as $menu) {
                            if($menu["id"] == $start_menu_id || empty($start_menu_id)) { ?>
                                <li class="sidenav-heading"><?php echo $menu["name"]; ?></li>
                                <?php
                                if(!empty($menu["submenus"])) {
                                    foreach(@$menu["submenus"] as $submenu) {
                                        if(!isset($submenu["submenus"])){
                                            $submenu["submenus"] = array();
                                        }
                                        if(chk_permission($submenu["id"], NULL, NULL, $user, $permissions)) {
                                            ?>
											<?php 
												if($submenu["url"] == $current_path) { 
													$_SESSION['permission_id'] = @$submenu['id'];
												} 
											?>
                                            <li class="sidenav-item<?php if($start_menu_id != 0 && (get_menu_id(array($submenu), $current_path) != -1 || get_menu_id($submenu["submenus"], $current_path) != -1)) { ?><?php if(!empty($submenu["submenus"])) { ?> has-subnav<?php } ?> open<?php } ?><?php if($submenu["url"] == $current_path) { ?> active<?php } ?>">
                                                <a 
												<?php if(@$submenu["onclick"]!=''){ ?>
													href="#" onclick="<?php echo @$submenu["onclick"]; ?>" 
												<?php }else{ ?>
													href="<?php echo @$submenu["url"]; ?>" 
												<?php } ?>
												<?php if(@$submenu["hidden"]=='hidden'){ echo "style='display:none'"; }?> aria-haspopup="true" target="<?php echo $submenu["target"]; ?>">
                                                    <span class="sidenav-icon icon <?php echo $submenu["icon"]; ?>"></span>
                                                    <span class="sidenav-label"><?php echo $submenu["name"]; ?></span>
                                                </a>
                                                <?php if(!empty($submenu["submenus"]) && $start_menu_id != 0) { ?>
                                                    <ul class="sidenav-subnav collapse">
                                                        <?php foreach($submenu["submenus"] as $submenu2) { ?>											
                                                            <?php if(chk_permission($submenu2["id"], NULL, NULL, $user, $permissions)) { ?>
                                                                <li <?php if($submenu2["url"] == $current_path) { ?> class="active"<?php } ?>>
																	<a 
																	<?php if(@$submenu2["onclick"]!=''){ ?>
																		href="#" onclick="<?php echo @$submenu2["onclick"]; ?>" 
																	<?php }else{ ?>
																		href="<?php echo @$submenu2["url"]; ?>" 
																	<?php } ?>
																	 target="<?php echo $submenu2["target"]; ?>"
																	><?php echo $submenu2["name"]; ?></a>
																</li>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </ul>
                                                <?php } ?>
                                            </li>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>


