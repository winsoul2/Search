var base_url = $('#base_url').attr('class');
function reset_time_out(res) {
	var chk_time_out = '';
	clearTimeout(chk_time_out);

	chk_time_out = setTimeout(function(){ location.href = base_url+'main_menu/logout?res='+res; }, 60 * 60 * 1000);
}

$( document ).ready(function() {
	var res = window.location.pathname;

	if(res != '/finance/finance_month'){
		reset_time_out(res);

		document.onclick = function(e){ reset_time_out(res); };
		document.onmousemove = function(e){ reset_time_out(res); };
		document.onkeydown = function(e){ reset_time_out(res); };
		document.onscroll = function(e){ reset_time_out(res); };
	}

});
