<div id="formulas_used">
	<div>
		<div>
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
				<tbody class="formulas">
					<tr>
						<th>Total Tax Exemptions</th>
						<td><?php echo $formulas['exemptions']['before']; ?></td>
						<td><?php echo $formulas['exemptions']['after']; ?></td>
					</tr>
					<tr>
						<th>Adjusted Gross Income (AGI)</th>
						<td><?php echo $formulas['agi']['before']; ?></td>
						<td><?php echo $formulas['agi']['after']; ?></td>
					</tr>
					<tr>
						<th>State taxes</th>
						<td><?php echo $formulas['taxes']['state']['before']; ?></td>
						<td><?php echo $formulas['taxes']['state']['after']; ?></td>
					</tr>
					<tr>
						<th>County taxes</th>
						<td><?php echo $formulas['taxes']['county']['before']; ?></td>
						<td><?php echo $formulas['taxes']['county']['after']; ?></td>
					</tr>
					<tr>
						<th>Remainder Home Value (RHV)</th>
						<td><?php echo $formulas['rhv']['before']; ?></td>
						<td><?php echo $formulas['rhv']['after']; ?></td>
					</tr>
					<tr>
						<th>Supplemental Homestead Deduction (SHD)</th>
						<td><?php echo $formulas['shd']['before']; ?></td>
						<td><?php echo $formulas['shd']['after']; ?></td>
					</tr>
					<tr>
						<th>Net Adjusted Home Value (Net AHV)</th>
						<td><?php echo $formulas['net_ahv']['before']; ?></td>
						<td><?php echo $formulas['net_ahv']['after']; ?></td>
					</tr>
					<tr>
						<th>Property taxes</th>
						<td><?php echo $formulas['taxes']['property']['before']; ?></td>
						<td><?php echo $formulas['taxes']['property']['after']; ?></td>
					</tr>
					<tr>
						<th>Average Annual Expenditures (AAE)</th>
						<td><?php echo $formulas['aae']; ?></td>
						<td><?php echo $formulas['aae']; ?></td>
					</tr>
					<?php foreach ($sales_tax_types as $sales_tax_type): ?>
						<tr>
							<th colspan="3" class="sales_tax_type">
								<?php echo ucfirst($sales_tax_type); ?>
							</th>
						</tr>
						<tr>
							<th>Estimated Expenditures</th>
							<td><?php echo $formulas['expenditures'][$sales_tax_type]; ?></td>
							<td><?php echo $formulas['expenditures'][$sales_tax_type]; ?></td>
						</tr>
						<tr>
							<th>Sales tax paid</th>
							<td><?php echo $formulas['taxes']['sales'][$sales_tax_type]['before']; ?></td>
							<td><?php echo $formulas['taxes']['sales'][$sales_tax_type]['after']; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p class="calc_footnote">
				All values estimated.
			</p>
		</div>
	</div>
</div>