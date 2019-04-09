$('#article_type').change(function() {
	if ($('#article_type').val() == 'articles_journaux') {
		$('#articles_conferences').hide();
		$('#articles_conferences [name]').removeAttr('required');
		$('#articles_journaux').show();
		$('#articles_journaux [name]').attr('required', 'true');
	} else {
		$('#articles_conferences').show();
		$('#articles_conferences [name]').attr('required', 'true');
		$('#articles_journaux').hide();
		$('#articles_journaux [name]').removeAttr('required');
	}
});

$('#pg_debut').change(function() {
	if (Number($('#pg_debut').val()) > Number($('#pg_fin').val())) {
		$('#pg_fin').val($('#pg_debut').val());
	}
});
$('#pg_fin').change(function() {
	if (Number($('#pg_debut').val()) > Number($('#pg_fin').val())) {
		$('#pg_debut').val($('#pg_fin').val());
	}
});

$('#q2c').submit(function(e) {
	e.preventDefault();

	$.ajax({
		type: "GET",
		url:"include/questions/q2c.php",
		data: $('#q2c').serialize(),
		success:function(result) {
			if (result == "success") {
				$('#q2c + small').html("Article ajouté avec succès à la base de donnée.");
			} else {
				$('#q2c + small').html(result);
			}
		}
	});
});