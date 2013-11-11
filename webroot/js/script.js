Number.prototype.formatMoney = function(c, d, t) {
	var n = this, 
		c = isNaN(c = Math.abs(c)) ? 2 : c, 
		d = d == undefined ? "." : d, 
		t = t == undefined ? "," : t, 
		s = n < 0 ? "-" : "", 
		i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
		j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

function forceMoneyFormat(id) {
	var field = $('#'+id);
	var value = field.val();

	// Remove everything after decimal point and strip non-numeric
	value = value.split(".")[0];
	value = value.replace(/[^0-9]/g, '');

	// Replace value in field
	field.val('$'+parseInt(value).formatMoney(0));
}

function calculate(event) {
	event.preventDefault();
	if (
		$('calc_input_home_value_before').val() == '' ||
		$('calc_input_home_value_after').val() == '' ||
		$('calc_input_income').val() == ''
	) {
		alert('Please fill out all fields before continuing.');
		return false;
	}
	$.ajax({
		url: '/pages/output',
		type: 'POST',
		data: $('#initial_input_form').serialize(),
		beforeSend: function () {
			$('#calc_loading').show();
			$('#calc_output_container').css({opacity: 0});
		},
		success: function (data) {
			$('#calc_output_container').html(data);
			$('#calc_output_container').css({opacity: 1});
		},
		error: function () {
			alert('Sorry, there was an error processing your request. Please try again.');
		},
		complete: function () {
			$('#calc_loading').hide();
		}
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
	event.preventDefault();
	var wrapper = $('#download_options');
	if (wrapper.height() == 0) {
		var content_height = wrapper.children('div').first().height();
		wrapper.css({height: content_height+'px'});
	} else {
		wrapper.css({height: 0});
	}
}

function toggle_formulas(event) {
	event.preventDefault();
	var wrapper = $('#formulas_used');
	if (wrapper.height() == 0) {
		var content_height = wrapper.children('div').first().height();
		wrapper.css({height: content_height+'px'});
	} else {
		wrapper.css({height: 0});
	}
}

function toggle_sources(event) {
	event.preventDefault();
	var wrapper = $('#sources');
	if (wrapper.height() == 0) {
		var content_height = wrapper.children('div').first().height();
		wrapper.css({height: content_height+'px'});
	} else {
		wrapper.css({height: 0});
	}
}

function toggle_footnotes(event) {
	event.preventDefault();
	var wrapper = $('#footnotes');
	if (wrapper.height() == 0) {
		var content_height = wrapper.children('div').first().height();
		wrapper.css({height: content_height+'px'});
	} else {
		wrapper.css({height: 0});
	}
}

function toggle_sales_taxes(event) {
	event.preventDefault();
	var wrapper = $('#sales_tax_breakdown_wrapper');
	if (wrapper.height() == 0) {
		var content_height = $('#sales_tax_breakdown').height();
		wrapper.css({height: content_height+'px'});
	} else {
		wrapper.css({height: 0});
	}
}

function toggle_edit(event) {
	event.preventDefault();
	var cells = $('#output_wrapper .display_mode');

	// Switching from display to input mode
	if (cells.length > 0) {
		cells.each(function() {
			$(this).removeClass('display_mode');
			$(this).addClass('input_mode');
		});

	// Switching from input to display mode
	} else {
		$('#output_wrapper .input_mode').each(function() {
			$(this).removeClass('input_mode');
			$(this).addClass('display_mode');
		});
	}
}

function recalculate(event) {
	event.preventDefault();
	if (
		$('#calc_input_home_value_before').val() == '' ||  
		$('#calc_input_home_value_after').val() == '' ||
		$('#calc_input_income').val() == ''
	) {
		alert('Please fill out all fields before continuing.');
		return false;
	}
	$.ajax({
		url: '/pages/output',
		type: 'POST',
		data: $('#recalculate_button').closest('form').serialize(),
		beforeSend: function () {
			$('#recalc_loading').show();
			$('#calc_output_container').css({opacity: 0});
		},
		success: function (data) {
			$('#calc_output_container').html(data);
			$('#calc_output_container').css({opacity: 1});
		},
		error: function () {
			$('#calc_output_container').html('Sorry, there was an error completing your request. Please refresh the page to try again. <a href="mailto:gtwatson@bsu.edu">Email the web developer</a> if you continue to have trouble.');
		},
		complete: function () {
			$('#recalc_loading').hide();
		}
	});
}