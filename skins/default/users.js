function showLogon() {
  document.getElementById("userActions").style.display="none";
  document.getElementById("logon").style.display="block";
}

function showUserActions() {
  document.getElementById("userActions").style.display="block";
  document.getElementById("logon").style.display="none";
}

// registration popup
function showRegister() {
	document.getElementById("register-overlay").style.display = "block";
}

function hideRegister() {
	document.getElementById("register-overlay").style.display = "none";
}

addHandler( window, "resize", function() {
	document.getElementById("register-overlay").style.height =
		parseInt(document.offsetHeight)+"px";
} );