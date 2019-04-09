function isset(e) {
	return typeof(e) != 'undefined' && e !== null;
}

function build_html(result) {
	if (!isset(result) || result.length == 0) {
		return '<tr><td style="text-align:center;">Aucun résultat.</td></tr>';
	}
	var html;
	$.each(result, function(key, value) {
		html += '<tr>';
		$.each(value, function(subkey, subvalue) {
			html += '<td>' + subvalue + '</td>';
		});
		html += '</tr>';
	});
	return html;
}

var tables;

$.ajax({
	url:"include/scheme.php",
	success:function(result){
		tables = result;
		var html = '';
		$.each(tables, function(key, value) {
			html += '<tr><th class="special" scope="row">' + key + '</th>';
			$.each(value, function(subkey, subvalue) {
				html += '<td>' + subvalue + '</td>';
			});
			html += '</tr>';
		});
		$('#tables > tbody').html(html);
	}
});

var auteurs;

$.ajax({
	type: "GET",
	url:"include/questions/sql.php",
	data: {file: 'build/authors'},
	success:function(result) {
		auteurs = result;
		var html = '';
		$.each(auteurs, function(key, value) {
			html += '<option value="' + value[0] + '">' + value[0] + ' - ' + value[1] + ' ' + value[2] + '</option>';
		});
		$('[build=auteurs]').append(html);
	}
});

var conferences;

$.ajax({
	type: "GET",
	url:"include/questions/sql.php",
	data: {file: 'build/conferences'},
	success:function(result) {
		conferences = result;
		var html = '';
		$.each(conferences, function(key, value) {
			html += '<option value="' + value + '">' + value[0] + ' (' + value[1] + ')' + '</option>'
		});
		$('[build=conferences]').append(html);
	}
});

var journaux;

$.ajax({
	type: "GET",
	url:"include/questions/sql.php",
	data: {file: 'build/journaux'},
	success:function(result) {
		journaux = result;
		var html = '';
		$.each(journaux, function(key, value) {
			html += '<option value="' + value + '">' + value[0] + ', n°' + value[1] + '</option>'
		});
		$('[build=journaux]').append(html);
	}
});