<?php header('Content-type: text/html; charset=UTF-8'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			<?php echo Configure::read('data_center_subsite_title'); ?>
		</title>
		<link rel="stylesheet" type="text/css" href="/css/style.css" />
	</head>
	<body class="print">
		<?php echo $content_for_layout ?>
		<?php echo $this->Js->writeBuffer(); ?>
	</body>
</html>