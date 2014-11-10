var d = new Date();
var reset = true;
var start_time;
var key_strokes = [];

var secret_shows = 1;

function key_signal(id, action, timestamp) {
	this.id = id;
	this.action = action;
	this.timestamp = timestamp;
}

function server_call(url, parameters, successCallback) {
	$.ajax({
		url: url,
		type: 'POST',
		data: jQuery.param(parameters),
		contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
		dataType: 'json',
		success: successCallback,
		error: function(xhr, textStatus, errorThrown) {
			alert('Sorry... Server call failed: '+errorThrown);
		}
	});
}

function clickResult(msg) {
	return function() {
		alert(msg);
	}
}

function toggle_secret() {
	if(secret_shows == 0) {
		$('#secret').show();
		secret_shows = 1;
	}
	else{
		$('#secret').hide();
		secret_shows = 0;
	}
}

function reset_capture() {
	key_strokes = [];
	reset = true;
	$('#password').val('');
}

function show_current_keystrokes() {
	var tdt = '';
	var img_line = '';
	for(var k in key_strokes) {
		var file = key_strokes[k].action == 'd' ? "key_down" : "key_up";
		tdt += '<td align="center" valign="bottom" nowrap>'+translate_key(key_strokes[k].id)+'<br/>'+'<img src="image/'+file+'.png" />'+'<br/>'+key_strokes[k].timestamp+'ms'+'</td>';
		
		img_line += '<td id="img_'+k+'" align="center" valign="bottom" nowrap>&nbsp</td>'
	}
	
	var txt = "<table border='0' cellpadding='10'><tr>" + tdt + "</tr><tr>"+img_line+"</tr></table>";
	$('#current').html(txt);
}

function load_user_info() {
	var url = "server/api-user_info.php";
	
	var data = {
		'username' : $('#username').val()
	};
	
	function onSuccess(responce) {
		if(!responce.success) { alert("Error: " + responce.value); }
		else {
			$('#pass_hint').html("(hint: " + responce.value['password'] + ")");
			
			var first = "";
			var second = "";
			
			for(var i = 0; i < responce.value.keystrokes.length; i++)
			{
				var delta = new Number(responce.value.statistics[i].delta).toPrecision(5);
				var avg = new Number(responce.value.statistics[i].average).toPrecision(5);

				if( i % 2 == 0) 
				{ // Down events
					first = first + "<td align='center'>"+translate_key(responce.value.keystrokes[i].key)+"<br />"+"<img src='image/key_down.png' /><br /><b>μ:</b> "+avg+"ms<br /><b>σ:</b> "+delta+"ms</td>"
				}
				else
				{ // Up events
					second = second + "<td align='center'>"+translate_key(responce.value.keystrokes[i].key)+"<br />"+"<img src='image/key_up.png' /><br /><b>μ:</b> "+avg+"ms<br /><b>σ:</b> "+delta+"ms</td>"
				}
			}
			var txt = "<table border='0' cellpadding='10'><tr>" + first + "</tr><tr>" + second + "</tr></table>";
	
			$('#stats').html(txt);
			
			txt = 'Number of logins: ' + responce.no_of_logins + '<br />Time since last login: '+responce.dt+'ms<br/><br />Activated rules:<br />';
			
			for(var key in responce.fuzzy)
			{
				txt += '&nbsp;&nbsp;&nbsp;&nbsp[' + key + ']:<br />';
				for(var kk in responce.fuzzy[key]) {
					txt += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp[' + kk + ']: '+responce.fuzzy[key][kk]+'<br />';
				}
				txt += '<br/>';
			}
			
			
			txt += 'Acceptable error level: ' + (1.0 - responce.success_rate);
			
			$('#fuzzy').html(txt);
		}
	}
	
	server_call(url, data, onSuccess);
		
	return false;
}

$(document).ready(function() {
	$('#username').change(function() {
		reset_capture();
		show_current_keystrokes();
		load_user_info();
		$('#password').focus();
	});
	
	$('#secret_switch').click(function() {
		toggle_secret();
		
		return false;
	});

	$('#password').keydown(function(event) {
		d = new Date();

		if(reset) {
			reset = false;
			key_strokes = [];
			start_time = d.getTime();
		}

		if(!is_special_key(event.keyCode)) {
			var ts = (d.getTime() - start_time);
			key_strokes.push(new key_signal(event.keyCode, 'd', ts));
			show_current_keystrokes();
		}
	});
	
	$('#password').keyup(function(event) {
		d = new Date();
		
		if(!is_special_key(event.keyCode)) {
			var ts = (d.getTime() - start_time);
			key_strokes.push(new key_signal(event.keyCode, 'u', ts));
			show_current_keystrokes();
		}
	});

	$('#loginForm').submit(function(event) {
		var url = "server/srv_user-login.php";
	
		var data = {
			'username' : $('#username').val(),
			'password' : $('#password').val(),
			'keystrokes' : key_strokes
		};
	
		function onSuccess(responce) {
			if(! responce.status) {
				alert("Login failed: " + responce.message);
				//reset_capture();
			}
			else {
				alert("Successful login!");
				
				
			}
			
			reset_capture();
				//show_current_keystrokes();
			load_user_info();
			
			var skata = '';
			for(var k in responce.keystats) {
				var color = responce.keystats[k].result ? 'green' : 'red';
				var image = responce.keystats[k].result ? 'true.png' : 'false.png';
					
				$('#img_'+k).css({"background-image" : "url('image/"+image+"')", "background-repeat": "no-repeat", "background-position" : "center", "cursor" : "pointer"});
				
				$('#img_'+k).click(
					clickResult(responce.keystats[k].msg)
				);
			}
			
			var txt = '<br/>Current error level: ' + responce.error_level;
			$('#end').html(txt);
			
		}
	
		server_call(url, data, onSuccess);
		
		return false;
	});
	
	//$('#secret').hide();
	load_user_info();
	$('#password').focus();
});
