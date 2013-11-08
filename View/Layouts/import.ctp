<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			<?php if (isset($title_for_layout) && $title_for_layout): ?>
				Import -
			<?php endif; ?>
			Illinois / Indiana Tax Comparison
		</title>
		<meta name="title" content="" />
		<meta name="description" content="" />
		<meta name="author" content="Center for Business and Economic Research, Ball State University" />
		<meta name="language" content="en" />
		<link rel="stylesheet" type="text/css" href="/css/main.css" />
		<link rel="icon" type="image/png" href="/img/chart.png" />
	</head>
	<body>
		<?php echo $content_for_layout; ?>
		<?php echo $this->element('sql_dump'); ?>
		<?php echo $this->Js->writeBuffer(); ?>
	</body>
</html>