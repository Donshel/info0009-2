$.ajax({
	type: "GET",
	url:"include/questions/sql.php",
	data: {file: 'questions/q2e'},
	success:function(result) {
		$('#q2e > tbody').html(build_html(result));
	}
});