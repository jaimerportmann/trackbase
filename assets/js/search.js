var dbase = window.location;

function process(e) {

	jQuery.ajax({
		url : dbases + '/wp-admin/admin-ajax.php?action=post_nquery',
		type : 'post',
		data : {
			action : 'post_nquery',
			nquery : e.value,
		},
		success : function( response ) {
			document.getElementById('underInput').innerHTML = (response);
		},
		error : function( response ) {
			document.getElementById('underInput').innerHTML = (response);
		}
	});
	
    return true;

}