<div class="breadcrump">
	<a class="font-menu-main link-line-none" href="/">หน้าแรก</a>
	<?php

	foreach($menu_paths as $key => $menu_path) {
		if($key + 1 < count($menu_paths)) {?>
			<a class="font-menu-main link-line-none" href="<?php echo $menu_path["url"]; ?>"> / <?php echo $menu_path["name"]; ?></a>
			<?php
		}
	}
	?>
</div>