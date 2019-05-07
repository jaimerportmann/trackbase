//localStorage.clear("time_on_site");
var ms = 0, i = 1, time_on_page = 0, time_on_site = 0;
var dbase = window.location;

if (window.localStorage.getItem("time_on_site") == null) {
	var time_on_site = 0;
    window.localStorage.setItem("time_on_site", time_on_site);
} else {
    time_on_site = window.localStorage.getItem("time_on_site");
}

function msToTime (ms) {
        var seconds = (ms/1000);
        var minutes = parseInt(seconds/60, 10);
        seconds = seconds%60;
        var hours = parseInt(minutes/60, 10);
        minutes = minutes%60;
        
        return hours + ':' + minutes + ':' + seconds.toFixed(2);
}

jQuery(function($){
 
    setDate();
 
    function setDate(){
 
        isZero(ms);

        var time = msToTime(ms);

		time_on_page = ms;
		time_on_site = Number(time_on_site) + 10;
		window.localStorage.setItem("time_on_site", time_on_site);
	
        setTimeout(setDate,10);
    }
  
    function isZero(value){
        if(value == 0){
            ms = 1000;
        }
        else{
            ms += 10;
        }
    };
});

console.log(window.screen.width);
console.log(window.innerWidth);

  var node1 = 'view';
	
	jQuery.ajax({
		url : dbase + '/wp-admin/admin-ajax.php?action=post_trackbase',
		type : 'post',
		data : {
			action : 'post_trackbase',
			type : node1,
			click_url : node1,
			click_class : node1,
			click_id : node1,
			click_title : node1,
			click_alt : node1,
			clicks : clicks,
			click_x : node1,
			click_y : node1,
			scroll_evt : scroll_evt,
			time_on_page : time_on_page,
			time_on_site : time_on_site,
			url : document.URL
		},
		success : function( response ) {
			//alert('success: ' + response );
		},
		error : function( response ) {
			alert('error: ' + response );
		}
	});
	



var clicks = 0;

var scroll_evt = 0;
window.onscroll = function (e) {  
	scroll_evt = 1;
}

window.onclick = function(e) {

  var node = e.target;
  while (node != undefined && node.localName != 'a') {
    node = node.parentNode;
  }
  if (node != undefined) {
	
	jQuery.ajax({
		url : dbase + '/wp-admin/admin-ajax.php?action=post_trackbase',
		type : 'post',
		data : {
			action : 'post_trackbase',
			type : 'click',
			click_url : node.href,
			click_class : node.className,
			click_id : node.id,
			click_title : node.title,
			click_alt : node.alt,
			clicks : clicks,
			click_x : e.pageX,
			click_y : e.pageY,
			scroll_evt : scroll_evt,
			time_on_page : time_on_page,
			time_on_site : time_on_site,
			url : document.URL
		},
		success : function( response ) {
			//alert('success: ' + response );
		},
		error : function( response ) {
			//alert('error: ' + response );
		}
	});
	
    return true;
  } else {
	clicks++;
    return true;
  }
}
