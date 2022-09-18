<?php if(count($notification)>0){ ?>
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