$.ajax({
	type: "GET",
	url:"include/questions/sql.php",
	data: {file: 'questions/q2d'},
	success:function(result) {
		$('#q2d > tbody').html(build_html(result));
	}
});