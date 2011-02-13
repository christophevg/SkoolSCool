var converter = null;

function showBody() {
  if( !converter ) { converter = new Breakdown.converter(); }
  document.getElementById("bodyContent").innerHTML = 
  converter.makeHtml(document.getElementById("bodyRaw").value);
}

function editBody() {
  document.getElementById("bodyView").style.display = "none";
  document.getElementById("bodyEdit").style.display = "block";
}

function previewBody() {
  document.getElementById("bodyView").style.display = "block";
  document.getElementById("bodyEdit").style.display = "none";
  document.getElementById("bodySave").style.display = "block";
  showBody();
}

function cancelBody() {
  document.getElementById("bodyView").style.display = "block";
  document.getElementById("bodyEdit").style.display = "none";
}

window.onload = showBody;
