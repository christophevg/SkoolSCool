// map of lazy loaded editor objects
var editors = {};

/**
 * factory function
 * TODO: create real class/object with methods in stead of style changes
 */
function getEditor(cid) {
  if( typeof editors[cid] == "undefined" ) {
    editors[cid] = {
      container : document.getElementById(cid + "Container"),
      controls  : {
        all     : document.getElementById(cid + "Controls"),
        edit    : document.getElementById(cid + "EditCommand"),
        save    : document.getElementById(cid + "SaveCommand"),
        cancel  : document.getElementById(cid + "CancelCommand"),
        saving  : document.getElementById(cid + "SavingState"),
      },
      view      : document.getElementById(cid + "View"),      
      editor    : document.getElementById(cid + "Editor"),
      raw       : document.getElementById(cid + "Raw"),
      current   : document.getElementById(cid + "Raw").value
    }
  }
  // update editor settings
  // lock container height to avoid reflowing when switching view <-> editor
  editors[cid].container.style.height = editors[cid].container.offsetHeight - 2 + "px";
  // make editor as big as the view
  editors[cid].editor.style.height = editors[cid].view.offsetHeight - 10 + "px";
  // prepare the raw editor (reserve 20px for the editorcontrols
  editors[cid].raw.style.height = editors[cid].view.offsetHeight - 30 + "px";

  return editors[cid];
}

function editContent(cid) {
  with( getEditor(cid) ) {
    current = raw.value;
    view.style.display = "none";
    controls.all.style.display = "none";
    editor.style.display = "block";
  }
}

function cancelContent(cid) {
  with( getEditor(cid) ) {
    raw.value = current;
    renderContent(cid);
    view.style.display = "block";
    // match new height of view
    container.style.height = view.offsetHeight + "px";
    controls.all.style.display = "block";
    editor.style.display = "none";
    controls.save.className = "command inactive";
    controls.cancel.className = "command inactive";
  }
}

var converter = null;

function renderContent(cid) {
  if( !converter ) { converter = new Breakdown.converter(); }
  with( getEditor(cid) ) {
    view.innerHTML = converter.makeHtml( raw.value );
  }
}

function previewContent(cid) {
  renderContent(cid);
  with( getEditor(cid) ) {
    view.style.display = "block";
    // match new height of view
    container.style.height = view.offsetHeight + "px";
    controls.all.style.display = "block";
    editor.style.display = "none";
    if( raw.value != current ) {
      // activate save/cancel actions
      controls.save.className = "command active";
      controls.cancel.className = "command active";
    }
  }
}

function saveContent(cid) {
  with( getEditor(cid) ) {
    controls.edit.className   = "command inactive";
    controls.save.className   = "command inactive";
    controls.cancel.className = "command inactive";
    controls.saving.className = "state active";
    __remote__.store( cid, raw.value, function(cid) {
      return function confirmSave( response ) {
        with( getEditor(cid) ) {
          if( response != "ok" ) { 
            notify( "saving failed: " + response );
            controls.save.className = "command active";
            controls.cancel.className = "command active";
          }
          controls.edit.className = "command active";
          controls.saving.className = "state inactive";
        }
      }
    }(cid) );
  }
}

function get_param( name ) {
  var expr = "[\\?&]"+name+"=([^&#]*)";
  var results = new RegExp(expr).exec( window.location.href );
  if( results == null ) { return null; }
  return results[1];
}

if( get_param( 'mode' ) == 'edit' ) {
  window.onload = function() { editContent(bodyContent); };
}

function showAddContent() {
	document.getElementById("addcontent-overlay").style.display = "block";
}

function hideAddContent() {
	document.getElementById("addcontent-overlay").style.display = "none";
}
