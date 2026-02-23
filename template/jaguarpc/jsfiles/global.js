var shown = '';
var olfid = '1';

function win(url) {
	if(shown == '' || shown.closed) {
		shown = window.open(url,'win','scrollbars=no,status=no, high,width=550,height=320');
	} else {
		shown.close();
		shown = window.open(url,'win','scrollbars=no,status=no, high,width=550,height=320');
	}
}

function open_builder() {
	new__window=window.open ("http://sitestudio.demo.psoft.net:8081/demo","_blank", "width=860,height=540,resizable=1,scrollbars=1");
}

function fswitch(fid) {

	if(olfid != '') {
		if(olfid == '1') {
			document.getElementById('fid_' + olfid).className = 'dis tl';
		} else if(olfid == '14') {
			document.getElementById('fid_' + (olfid -1)).className = 'dis';
			document.getElementById('fid_' + olfid).className = 'dis bl';
		} else {
			if(olfid > 2) {
				document.getElementById('fid_' + (olfid -1)).className = 'dis';
			} else {
				document.getElementById('fid_' + (olfid -1)).className = 'dis tl';
			}
			document.getElementById('fid_' + olfid).className = 'dis';
		}
	}

	if(fid == '1') {
		document.getElementById('fid_' + fid).className = 'highlighted hightl';
	} else if(fid == '14') {
		document.getElementById('fid_' + fid).className = 'highlighted bl';
	} else {
		document.getElementById('fid_' + fid).className = 'highlighted';
	}
	
	if(fid > 2) {
		document.getElementById('fid_' + (fid -1)).className = 'dis highbot';
	} else if(fid == 2) {
		document.getElementById('fid_' + (fid -1)).className = 'dis highbot tl';
	}
	document.getElementById('display').innerHTML = document.getElementById('bid_' + fid).innerHTML;
	olfid = fid;
	$(document).ready(JT_init);
	
}

function loadFeatures() {
	document.getElementById('display').innerHTML = document.getElementById('bid_1').innerHTML;
	    $(document).ready(JT_init);	
}