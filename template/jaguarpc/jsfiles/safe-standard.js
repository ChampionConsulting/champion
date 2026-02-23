var ps3dPssid = "BMPBYOEinw99";
// safe-standard@gecko.js

var ps3dPsiso;
try {
	ps3dPsiso = (opener != null) && (typeof(opener.name) != "unknown") && (opener.ps3dPswid != null);
} catch(e) {
	ps3dPsiso = false;
}
if (ps3dPsiso) {
	window.ps3dPswid = opener.ps3dPswid + 1;
	ps3dPssid = ps3dPssid + "_" + window.ps3dPswid;
} else {
	window.ps3dPswid = 1;
}
function ps3dPsn() {
	return (new Date()).getTime();
}
var ps3dPss = ps3dPsn();
function ps3dPsst(f, t) {
	if ((ps3dPsn() - ps3dPss) < 7200000) {
		return setTimeout(f, t * 1000);
	} else {
		return null;
	}
}
var ps3dPsol = true;
function ps3dPsow() {
	if (ps3dPsol || (2 == 1)) {
		var pswo = "menubar=0,location=0,scrollbars=auto,resizable=1,status=0,width=650,height=680";
		var pswn = "pscw_" + ps3dPsn();
		var url = "https://messenger.providesupport.com/messenger/1bxosejpcvrrc1sce9weowyg8f.html?ps_l=" + escape(document.location) + "";
		window.open(url, pswn, pswo);
	} else if (2 == 2) {
		document.location = "https://d9clients.com/submitticket.php";
	}
}
var ps3dPsil;
var ps3dPsit;
function ps3dPspi() {
	var il;
	if (3 == 2) {
		il = window.pageXOffset + 50;
	} else if (3 == 3) {
		il = (window.innerWidth * 50 / 100) + window.pageXOffset;
	} else {
		il = 50;
	}
	il -= (325 / 2);
	var it;
	if (3 == 2) {
		it = window.pageYOffset + 50;
	} else if (3 == 3) {
		it = (window.innerHeight * 50 / 100) + window.pageYOffset;
	} else {
		it = 50;
	}
	it -= (208 / 2);
	if ((il != ps3dPsil) || (it != ps3dPsit)) {
		ps3dPsil = il;
		ps3dPsit = it;
		var d = document.getElementById('ci3dPs');
		if (d != null) {
			d.style.left  = Math.round(ps3dPsil) + "px";
			d.style.top  = Math.round(ps3dPsit) + "px";
		}
	}
	setTimeout("ps3dPspi()", 100);
}
var ps3dPslc = 0;
function ps3dPssi(t) {
	window.onscroll = ps3dPspi;
	window.onresize = ps3dPspi;
	ps3dPspi();
	ps3dPslc = 0;
	var url = "http://messenger.providesupport.com/" + ((t == 2) ? "auto" : "chat") + "-invitation/1bxosejpcvrrc1sce9weowyg8f.html?ps_t=" + ps3dPsn() + "";
	var d = document.getElementById('ci3dPs');
	if (d != null) {
		d.innerHTML = '<iframe allowtransparency="true" style="background:transparent;width:325;height:208" src="' + url + 
			'" onload="ps3dPsld()" frameborder="no" width="325" height="208" scrolling="no"></iframe>';
	}
}
function ps3dPsld() {
	if (ps3dPslc == 1) {
		var d = document.getElementById('ci3dPs');
		if (d != null) {
			d.innerHTML = "";
		}
	}
	ps3dPslc++;
}
if (false) {
	ps3dPssi(1);
}
var ps3dPsd = document.getElementById('sc3dPs');
if (ps3dPsd != null) {
	if (ps3dPsol || (2 == 1) || (2 == 2)) {
		var ctt = "";
		if (ctt != "") {
			tt = 'alt="' + ctt + '" title="' + ctt + '"';
		} else {
			tt = '';
		}
		if (false) {
			var p1 = '<table style="display:inline;border:0px;border-collapse:collapse;border-spacing:0;"><tr><td style="padding:0px;text-align:center;border:0px;vertical-align:middle"><a href="#" onclick="ps3dPsow(); return false;"><img name="ps3dPsimage" src="http://image.providesupport.com/image/1bxosejpcvrrc1sce9weowyg8f/online-1854293040.png" width="163" height="29" style="border:0;display:block;margin:auto"';
			var p2 = '<td style="padding:0px;text-align:center;border:0px;vertical-align:middle"><a href="http://www.providesupport.com/pb/1bxosejpcvrrc1sce9weowyg8f" target="_blank"><img src="http://image.providesupport.com/';
			var p3 = 'style="border:0;display:block;margin:auto"></a></td></tr></table>';
			if ((163 >= 140) || (163 >= 29)) {
				ps3dPsd.innerHTML = p1+tt+'></a></td></tr><tr>'+p2+'lcbpsh.gif" width="140" height="17"'+p3;
			} else {
				ps3dPsd.innerHTML = p1+tt+'></a></td>'+p2+'lcbpsv.gif" width="17" height="140"'+p3;
			}
		} else {
			ps3dPsd.innerHTML = '<a href="#" onclick="ps3dPsow(); return false;"><img name="ps3dPsimage" src="http://image.providesupport.com/image/1bxosejpcvrrc1sce9weowyg8f/online-1854293040.png" width="163" height="29" border="0"'+tt+'></a>';
		}
	} else {
		ps3dPsd.innerHTML = '';
	}
}
var ps3dPsop = false;
function ps3dPsco() {
	var w1 = ps3dPsci.width - 1;
	ps3dPsol = (w1 & 1) != 0;
	ps3dPssb(ps3dPsol ? "http://image.providesupport.com/image/1bxosejpcvrrc1sce9weowyg8f/online-1854293040.png" : "http://image.providesupport.com/image/1bxosejpcvrrc1sce9weowyg8f/offline-1267108542.png");
	ps3dPsscf((w1 & 2) != 0);
	var h = ps3dPsci.height;

	if (h == 1) {
		ps3dPsop = false;

	// manual invitation
	} else if ((h == 2) && (!ps3dPsop)) {
		ps3dPsop = true;
		ps3dPssi(1);
		//alert("Chat invitation in standard code");
		
	// auto-invitation
	} else if ((h == 3) && (!ps3dPsop)) {
		ps3dPsop = true;
		ps3dPssi(2);
		//alert("Auto invitation in standard code");
	}
}
var ps3dPsci = new Image();
ps3dPsci.onload = ps3dPsco;
var ps3dPspm = false;
var ps3dPscp = ps3dPspm ? 30 : 60;
var ps3dPsct = null;
function ps3dPsscf(p) {
	if (ps3dPspm != p) {
		ps3dPspm = p;
		ps3dPscp = ps3dPspm ? 30 : 60;
		if (ps3dPsct != null) {
			clearTimeout(ps3dPsct);
			ps3dPsct = null;
		}
		ps3dPsct = ps3dPsst("ps3dPsrc()", ps3dPscp);
	}
}
function ps3dPsrc() {
	ps3dPsct = ps3dPsst("ps3dPsrc()", ps3dPscp);
	try {
		ps3dPsci.src = "http://image.providesupport.com/cmd/1bxosejpcvrrc1sce9weowyg8f?" + "ps_t=" + ps3dPsn() + "&ps_l=" + escape(document.location) + "&ps_r=" + escape(document.referrer) + "&ps_s=" + ps3dPssid + "" + "";
	} catch(e) {
	}
}
ps3dPsrc();
var ps3dPscb = "http://image.providesupport.com/image/1bxosejpcvrrc1sce9weowyg8f/online-1854293040.png";
function ps3dPssb(b) {
	if (ps3dPscb != b) {
		var i = document.images['ps3dPsimage'];
		if (i != null) {
			i.src = b;
		}
		ps3dPscb = b;
	}
}

