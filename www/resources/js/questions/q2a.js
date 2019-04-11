$('#search + button').click(function() {
	if (isset($('#search').val())) {
		var input = parse($('#search').val());
		if (isset(tables[input['table']])) {
			$('#hero').css('padding-top', '15vh');
			$.ajax({
				type: "GET",
				url:"include/questions/q2a.php",
				data: input,
				success:function(result) {
					var html = '<tr class="special">';
					$.each(result[0], function(key, value) {
						html += '<th scope="column">' + value[0] + '</th>';
					});
					html += '</tr>';
					$('.search + div').html('');
					$('#q2a > thead').html(html);
					$('#q2a > tbody').html(build_html(result[1]));
				}
			});
		} else {
			$('.search + div').html("Nom de la table erroné.");
		}
	}
});

function parse(str) {
	var constraints = str.split(',');
	var input = {
		'table': constraints.shift()
	};

	var e, i;
	while (constraints.length > 0) {
		constraint = constraints.shift();
		if (constraint.includes('=')) {
			i = constraint.indexOf('=');
			input[$.trim(constraint.slice(0, i))] = $.trim(constraint.slice(i + 1));
		}
	}

	return input;
}