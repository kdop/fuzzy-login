var d = new Date();
var reset = true;
var reset2 = true;
var start_time;
var start_time2;
var key_strokes = [];
var key_strokes2 = [];

function key_signal(id, action, timestamp) {
	this.id = id;
	this.action = action;
	this.timestamp = timestamp;
}

function is_special_key(key_id) {
	switch(key_id) {
		case 8: return true;	// Backspace
		case 9: return true;	// Enter
		case 13: return true;	// Tab
		case 17: return true;	// Ctrl
		case 18: return true;	// Alt
		case 19: return true;	// Pause
		case 27: return true;	// Esc
		case 33: return true;	// Pg up
		case 34: return true;	// Pg down
		case 35: return true;	// End
		case 36: return true;	// Home
		case 37: return true;	// Left
		case 38: return true;	// Up
		case 39: return true;	// Right
		case 40: return true;	// Down
		case 45: return true;	// Insert
		case 46: return true;	// Delete
		case 91: return true;	// Windows
		case 93: return true;	// Dialog
		case 112: return true;	// F1
		case 113: return true;	// F2
		case 114: return true;	// F3
		case 115: return true;	// F4
		case 116: return true;	// F5
		case 117: return true;	// F6
		case 118: return true;	// F7
		case 119: return true;	// F8
		case 120: return true;	// F9
		case 121: return true;	// F10
		case 122: return true;	// F11
		case 123: return true;	// F12
		case 145: return true;	// Scroll
		default: return false;
	}
}

function form_submit() {
	//Server request.
	var request = $.ajax({
		url: "server/srv_user-add.php",
		type: "POST",
		data: {
			name : $('#username').val(),
			password : $('#password').val(),
			password2 : $('#password2').val(),
			keystrokes : key_strokes,
			keystrokes2 : key_strokes2
		},
		dataType: "json"
	});

	request.done(function(response) {
		alert((response.status ? "Success: " : "Error: ") + response.message);
		if(response.status) {
			window.location.href = 'users.php';
		}
		else {
			reset = reset2 = true;
			key_strokes = key_strokes2 = [];
			window.location.href = '';
		}
	});
	
	request.fail(function(jqXHR, textStatus) {
		alert("Server request failed: " + textStatus);
		window.location.href = '';
	});
}

$(document).ready(function() {
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
		}
	});
	
	$('#password').keyup(function(event) {
		d = new Date();
		
		if(!is_special_key(event.keyCode)) {
			var ts = (d.getTime() - start_time);
			key_strokes.push(new key_signal(event.keyCode, 'u', ts));
		}
	});

	$('#password2').keydown(function(event) {
		d = new Date();

		if(reset2) {
			reset2 = false;
			key_strokes2 = [];
			start_time2 = d.getTime();
		}
		
		if(!is_special_key(event.keyCode)) {
			var ts = (d.getTime() - start_time2);
			key_strokes2.push(new key_signal(event.keyCode, 'd', ts));
		}
	});
	
	$('#password2').keyup(function(event) {
		d = new Date();

		if(!is_special_key(event.keyCode)) {
			var ts = (d.getTime() - start_time2);
			key_strokes2.push(new key_signal(event.keyCode, 'u', ts));
		}
	});

	$('#addForm').submit(function(event) {
		form_submit();
		
		return false;
	});
	
	$('#username').focus();
});
