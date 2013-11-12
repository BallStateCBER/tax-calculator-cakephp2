<?php header('Content-type: text/html; charset=UTF-8'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="print">
	<head>
		<title>
			<?php echo Configure::read('data_center_subsite_title'); ?>
		</title>
		<link href='http://fonts.googleapis.com/css?family=Asap:400,400italic,700' rel='stylesheet' type='text/css'>
		<?php
			echo $this->Html->css('DataCenter.datacenter');
			echo $this->Html->css('style');
		?>
	</head>
	<body>
		<?php echo $content_for_layout ?>
		<?php echo $this->Js->writeBuffer(); ?>
	</body>
</html>