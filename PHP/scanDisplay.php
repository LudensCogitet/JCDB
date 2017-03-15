<?php
	if(!isset($_REQUEST['scanSrc'])){
		echo "You're clearly not supposed to be here.";
	}
?>
<img style='border: 2px solid black;' src='<?php echo $_REQUEST['scanSrc']?>'>