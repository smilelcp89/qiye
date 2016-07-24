<?php if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");} ?><?php $contact = phpok('contact');?>
<div class="pfw">
	<div class="title"><h3><?php echo $contact['title'];?></h3></div>
	<div class="info contact_info">
		<h4><?php echo $contact['company'];?></h4>
		<ul>
			<li><i>联系人：</i><?php echo $contact['fullname'];?></li>
			<li><i>电　话：</i><?php echo $contact['tel'];?></li>
			<li><i>邮　箱：</i><?php echo $contact['email'];?></li>
		</ul>
	</div>
</div>