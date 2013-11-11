Number.prototype.formatMoney = function(c, d, t) {
	var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? ',' : d, t = t == undefined ? '.' : t, s = n < 0 ? '-' : '', i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + '', j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
};
function forceMoneyFormat(id) {
	var field = $(id);
	var value = field.value;

	// Remove everything after decimal point and strip non-numeric
	value = value.split(".")[0];
	value = value.replace(/[^0-9]/g, '');

	// Replace value in field
	field.value = '$'+parseInt(value).formatMoney(0, '.', ',');
}

function calculate(event) {
	event.stop();
	if (
		$('calc_input_home_value_before').value == '' ||
		$('calc_input_home_value_after').value == '' ||
		$('calc_input_income').value == ''
	) {
		alert('Please fill out all fields before continuing.');
		return false;
	}
	var jsRequest = new Ajax.Updater('calc_output_container', '/pages/output', {
		condition:'true',
		indicator:'calc_loading',
		method:'post',
		onComplete:function (transport) {
			//$('initial_input_form').setStyle({height: 0});
			$('calc_output_container').setStyle({opacity: 1});
		},
		onCreate:function (transport) {
			$('calc_output_container').setStyle({opacity: 0});
		},
		parameters:$($('initial_input_form')).serialize(),
		evalScripts: true
	});
}

function printContent(content) {
	var WindowObject = window.open("", "PrintWindow",
		"width=750,height=650,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes");
	WindowObject.document.writeln(content);
	WindowObject.document.close();
	WindowObject.focus();
	WindowObject.print();
	WindowObject.close();
}

function toggle_download_options(event) {
	event.stop();
	var wrapper = $('download_options');
	if (wrapper.getHeight() == 0) {
		var content_height = wrapper.down('div').getHeight();
		wrapper.setStyle({height: content_height+'px'});
	} else {
		wrapper.setStyle({height: 0});
	}
}

function toggle_formulas(event) {
	event.stop();
	var wrapper = $('formulas_used');
	if (wrapper.getHeight() == 0) {
		var content_height = wrapper.down('div').getHeight();
		wrapper.setStyle({height: content_height+'px'});
	} else {
		wrapper.setStyle({height: 0});
	}
}

function toggle_sources(event) {
	event.stop();
	var wrapper = $('sources');
	if (wrapper.getHeight() == 0) {
		var content_height = wrapper.down('div').getHeight();
		wrapper.setStyle({height: content_height+'px'});
	} else {
		wrapper.setStyle({height: 0});
	}
}

function toggle_footnotes(event) {
	event.stop();
	var wrapper = $('footnotes');
	if (wrapper.getHeight() == 0) {
		var content_height = wrapper.down('div').getHeight();
		wrapper.setStyle({height: content_height+'px'});
	} else {
		wrapper.setStyle({height: 0});
	}
}

function toggle_sales_taxes(event) {
	event.stop();
	var wrapper = $('sales_tax_breakdown_wrapper');
	if (wrapper.getHeight() == 0) {
		var content_height = $('sales_tax_breakdown').getHeight();
		wrapper.setStyle({height: content_height+'px'});
	} else {
		wrapper.setStyle({height: 0});
	}
}

function toggle_edit(event) {
	event.stop();
	var cells = $$('#output_wrapper .display_mode');

	// Switching from display to input mode
	if (cells.length > 0) {
		cells.each(function(cell) {
			cell.removeClassName('display_mode');
			cell.addClassName('input_mode');
		});

	// Switching from input to display mode
	} else {
		$$('#output_wrapper .input_mode').each(function(cell) {
			cell.removeClassName('input_mode');
			cell.addClassName('display_mode');
		});
	}
}

function recalculate(event) {
	event.stop();
	if ($('calc_input_home_value_before').value == '' || $('calc_input_home_value_after').value == '' || $('calc_input_income').value == '') {
		alert('Please fill out all fields before continuing.');
		return false;
	}
	var jsRequest = new Ajax.Updater('calc_output_container', '/pages/output', {
		condition:true,
		indicator:'recalc_loading',
		method:'post',
		onComplete: function (transport) {
			$('calc_output_container').setStyle({opacity: 1});
		},
		onCreate: function (transport) {
			$('calc_output_container').setStyle({opacity: 0});
		},
		onFailure: function (transport) {
			$('calc_output_container').update('Sorry, there was an error completing your request. Please refresh the page to try again. <a href="mailto:gtwatson@bsu.edu">Email the web developer</a> if you continue to have trouble.');
		},
		parameters:$($('recalculate_button').form).serialize(),
		evalScripts: true
	});
}