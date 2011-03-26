var converter = null;

function showBody() {
  if( !converter ) { converter = new Breakdown.converter(); }
  document.getElementById("bodyContent").innerHTML = 
  converter.makeHtml(document.getElementById("bodyRaw").value);
}

var currentBody;

function editBody() {
  var view = document.getElementById("bodyView");
  var edit = document.getElementById("bodyEdit");
  var text = document.getElementById("bodyRaw");
  
  text.style.width  = view.offsetWidth + "px";
  text.style.height = ( view.offsetHeight - 10 ) + "px";

  view.style.display = "none";
  currentBody = text.value;
  edit.style.display = "block";
}

function previewBody() {
  document.getElementById("bodyView").style.display = "block";
  document.getElementById("bodyEdit").style.display = "none";
  if( currentBody != document.getElementById("bodyRaw").value ) {
    document.getElementById("bodySaveAction").style.display = "block";
    document.getElementById("bodyCancelAction").style.display = "block";
  }
  showBody();
}

function saveBody() {
  document.getElementById("bodyEditAction").style.display = "none";  
  document.getElementById("bodySaveAction").style.display = "none";  
  document.getElementById("bodyCancelAction").style.display = "none";  
  document.getElementById("bodySavingAction").style.display = "block";
  __remote__.store( __page__, 
                    document.getElementById('bodyRaw').value,
                    confirmSave );
}

function confirmSave( response ) {
  if( response != "ok" ) { 
    notify( "saving failed: " + response );
    document.getElementById("bodySaveAction").style.display = "block";  
    document.getElementById("bodyCancelAction").style.display = "block";  
  }
  document.getElementById("bodyEditAction").style.display = "block";
  document.getElementById("bodySavingAction").style.display = "none";
}

function cancelBody() {
  document.getElementById("bodyView").style.display = "block";
  document.getElementById("bodyEdit").style.display = "none";
  document.getElementById("bodyRaw").value = currentBody;
  document.getElementById("bodySaveAction").style.display = "none";
  document.getElementById("bodyCancelAction").style.display = "none";
}
