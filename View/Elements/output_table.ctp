<?php echo $this->Form->create(false, array('id' => 'recalculate_form')); ?>
<div id="output_wrapper">
	<h2>
		Estimated Annual Tax Savings
	</h2>
	<table class="calc_output">
		<thead>
			<tr>
				<td>
					<?php echo $this->element('post_output_links'); ?>
				</td>
				<th class="display_mode">
					Moving from
					<div class="display">
						<?php echo $county_name['before']; ?>
					</div>
					<div class="input">
						<?php echo $this->Form->input('from_county', array(
							'label' => false,
							'div' => false,
							'options' => $counties['IL'],
							'default' => $county_id['before']
						)); ?>
						<br />
						County, IL
					</div>
				</th>
				<th class="display_mode">
					Moving to
					<div class="display">
						<?php echo $county_name['after']; ?>
					</div>
					<div class="input">
						<?php echo $this->Form->input('to_county', array(
							'label' => false,
							'div' => false,
							'options' => $counties['IN'],
							'default' => $county_id['after']
						)); ?>
						<br />
						County, IN
					</div>
				</th>
			</tr>
		</thead>
		<tfoot></tfoot>
		<tbody class="input">
			<tr>
				<th>Household Income</th>
				<td class="display_mode">
					<div class="display">
						<?php echo $income; ?>
					</div>
					<div class="input">
						<?php echo $this->Form->input('income', array(
							'label' => false,
							'div' => false,
							'value' => $income,
							'id' => 'calc_input_income'
						)); ?>
					</div>
				</td>
				<td></td>
			</tr>
			<tr>
				<th>Dependents</th>
				<td class="display_mode">
					<div class="display">
						<?php echo $dependents; ?>
					</div>
					<div class="input">
						<?php
							$dependent_options = array('None', 1, 2, 3, '4 or more');
							echo $this->Form->input('dependents', array(
								'label' => false,
								'div' => false,
								'options' => $dependent_options,
								'value' => array_search($dependents, $dependent_options)
							));
						?>
					</div>
				</td>
				<td></td>
			</tr>
			<tr>
				<th>Home Value</th>
				<td class="display_mode">
					<div class="display">
						<?php echo $home_value['before']; ?>
					</div>
					<div class="input">
						<?php echo $this->Form->input('home_value_before', array(
							'label' => false,
							'div' => false,
							'value' => $home_value['before'],
							'id' => 'calc_input_home_value_before'
						)); ?>
					</div>
				</td>
				<td class="display_mode">
					<div class="display">
						<?php echo $home_value['after']; ?>
					</div>
					<div class="input">
						<?php echo $this->Form->input('home_value_after', array(
							'label' => false,
							'div' => false,
							'value' => $home_value['after'],
							'id' => 'calc_input_home_value_after'
						)); ?>
					</div>
				</td>
			</tr>
		</tbody>
		<tbody class="output">
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
</div>
<?php echo $this->Form->end(); ?>