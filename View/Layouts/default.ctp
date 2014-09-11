<?php
	$this->extend('DataCenter.default');
	$this->Html->script('script', array('inline' => false));
	echo $this->element('flash_messages', array(), array('plugin' => 'DataCenter'));
?>

<?php $this->start('subsite_title'); ?>
	<h1 id="subsite_title" class="max_width">
		<a href="/">
			<img src="/img/ILINTaxSavings.jpg" alt="Illinois-to-Indiana Tax Savings Calculator" />
		</a>
	</h1>
<?php $this->end(); ?>

<div id="content">
	<?php echo $this->fetch('content'); ?>
</div>