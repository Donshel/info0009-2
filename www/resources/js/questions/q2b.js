$('#matricule').change(function() {
	if (isset($('#matricule').val())) {
		$.ajax({
			type: "GET",
			url:"include/questions/q2b.php",
			data: {matricule: $('#matricule').val()},
			success:function(result) {
				$('#q2b > tbody').html(build_html(result));
			}
		});
	}
});