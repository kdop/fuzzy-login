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
		case 44: return true;	// Print
		case 45: return true;	// Insert
		case 46: return true;	// Delete
		case 91: return true;	// Windows
		case 93: return true;	// Select
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
		case 144: return true;	// Num lock
		case 145: return true;	// Scroll
		default: return false;
	}
}

function translate_key(id) {
	switch(parseInt(id)) {
		case 16: return 'Shift';
		case 20: return 'Caps Lock';
		case 32: return 'Space';
		case 48: return '0'; //0   48
		case 49: return '1'; //1   49
		case 50: return '2'; //2   50
		case 51: return '3'; //3   51
		case 52: return '4'; //4   52
		case 53: return '5'; //5   53
		case 54: return '6'; //6   54
		case 55: return '7'; //7   55
		case 56: return '8'; //8   56
		case 57: return '9'; //9   57
		
		case 59: return ';'; //;   59
		
		case 61: return '+'; //a   65
		
		case 65: return 'a'; //a   65
		case 66: return 'b'; //b   66
		case 67: return 'c'; //c   67
		case 68: return 'd'; //d   68
		case 69: return 'e'; //e   69
		case 70: return 'f'; //f   70
		case 71: return 'g'; //g   71
		case 72: return 'h'; //h   72
		case 73: return 'i'; //i   73
		case 74: return 'j'; //j   74
		case 75: return 'k'; //k   75
		case 76: return 'l'; //l   76
		case 77: return 'm'; //m   77
		case 78: return 'n'; //n   78
		case 79: return 'o'; //o   79
		case 80: return 'p'; //p   80
		case 81: return 'q'; //q   81
		case 82: return 'r'; //r   82
		case 83: return 's'; //s   83
		case 84: return 't'; //t   84
		case 85: return 'u'; //u   85
		case 86: return 'v'; //v   86
		case 87: return 'w'; //w   87
		case 88: return 'x'; //x   88
		case 89: return 'y'; //y   89
		case 90: return 'z'; //z   90
		
		case 96: return 'Num 0'; //numpad 0    96
		case 97: return 'Num 1'; //numpad 1    97
		case 98: return 'Num 2'; //numpad 2    98
		case 99: return 'Num 3'; //numpad 3    99
		case 100: return 'Num 4'; //numpad 4    100
		case 101: return 'Num 5'; //numpad 5    101
		case 102: return 'Num 6'; //numpad 6    102
		case 103: return 'Num 7'; //numpad 7    103
		case 104: return 'Num 8'; //numpad 8    104
		case 105: return 'Num 9'; //numpad 9    105
		
		case 106: return '*'; //multiply    106
		case 107: return '+'; //add     107
		case 109: return '-'; //subtract    109
		case 110: return '.'; //decimal point   110
		case 111: return '/'; //devide   111
		
		case 173: return '-'; //semi-colon  186
		
		case 186: return ';'; //semi-colon  186
		case 187: return '='; //equal sign  187
		case 188: return ','; //comma   188
		case 189: return '-'; //dash    189
		case 190: return '.'; //period  190
		
		case 191: return '/'; //forward slash   191
		case 192: return '`'; //grave accent    192
		
		case 219: return '['; //open bracket    219
		case 220: return '\\'; //back slash  220
		case 221: return ']'; //close braket    221
		case 222: return '\''; //single quote    222
		default: return id;
	}
}