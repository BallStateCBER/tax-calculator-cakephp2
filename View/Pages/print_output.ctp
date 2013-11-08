<h1>Illinois to Indiana Tax Savings Calculator</h1>
<?php if (isset($errors) && ! empty($errors)): ?>
	<ul class="error_message">
		<?php foreach ($errors as $error): ?>
			<li><?php echo $error; ?></li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<table class="calc_output">
		<thead>
			<tr>
				<td></td>
				<th>
					Moving from<br />
					<?php echo $county_name['before']; ?>
				</th>
				<th>
					Moving to<br />
					<?php echo $county_name['after']; ?>
				</th>
			</tr>
		</thead>
		<tfoot></tfoot>
		<tbody>
			<tr>
				<th>State taxes</th>
				<td><?php echo $taxes['state']['before']; ?></td>
				<td><?php echo $taxes['state']['after']; ?></td>
			</tr>
			<tr>
				<th>County taxes</th>
				<td><?php echo $taxes['county']['before']; ?></td>
				<td><?php echo $taxes['county']['after']; ?></td>
			</tr>
			<tr>
				<th>Property taxes</th>
				<td><?php echo $taxes['property']['before']; ?></td>
				<td><?php echo $taxes['property']['after']; ?></td>
			</tr>
			<tr>
				<th>
					<a href="#" id="toggle_sales_taxes" title="Show more details">
						Sales taxes...
					</a>
				</th>
				<td><?php echo $taxes['sales']['total']['before']['displayed']; ?></td>
				<td><?php echo $taxes['sales']['total']['after']['displayed']; ?></td>
			</tr>
		</tbody>
	</table>
	<div id="sales_tax_breakdown_wrapper">
		<table class="calc_output" id="sales_tax_breakdown">
			<thead></thead>
			<tfoot></tfoot>
			<tbody>
				<?php foreach ($sales_tax_types as $sales_tax_type): ?>
					<tr>
						<th>On <?php echo $sales_tax_type; ?></th>
						<td><?php echo $taxes['sales'][$sales_tax_type]['before']['displayed']; ?></td>
						<td><?php echo $taxes['sales'][$sales_tax_type]['after']['displayed']; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<table class="calc_output" id="calc_totals">
		<thead></thead>
		<tfoot class="savings">
			<tr>
				<th>Annual tax savings</th>
				<td colspan="2">
					<?php echo $savings['displayed']; ?>
				</td>
			</tr>
		</tfoot>
		<tbody class="total_taxes">
			<tr>
				<th>Total annual taxes</th>
				<td><?php echo $taxes['total']['before']['displayed']; ?></td>
				<td><?php echo $taxes['total']['after']['displayed']; ?></td>
			</tr>
		</tbody>
	</table>
	<?php if (! $suppress_auto_printing): ?>
		<script type="text/javascript">
			window.onload = window.print;
		</script>
	<?php endif; ?>
<?php endif; ?>