<h3><?php echo $header; ?></h3>

<form action="<?php echo $action?>" method="post">

	<?php echo $table ?>
	
	<input type="hidden" name="XID" value="<?php echo $xid?>" />
	<button type="submit" class="submit"><?php echo $button ?></button>
	
</form>