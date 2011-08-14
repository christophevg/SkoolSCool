// map of lazy loaded editor objects
var editors = {};

/**
 * factory function
 * TODO: create real class/object with methods in stead of style changes
 */
function getEditor(id) {
  if( typeof editors[id] == "undefined" ) {
    editors[id] = {
      container : document.getElementById(id + "Container"),
      controls  : {
        all     : document.getElementById(id + "Controls"),
        edit    : document.getElementById(id + "EditCommand"),
        save    : document.getElementById(id + "SaveCommand"),
        cancel  : document.getElementById(id + "CancelCommand"),
        saving  : document.getElementById(id + "SavingState"),
      },
      view      : document.getElementById(id + "View"),      
      editor    : document.getElementById(id + "Editor"),
      raw       : document.getElementById(id + "Raw"),
      current   : document.getElementById(id + "Raw").value,
      subcontent: document.getElementById("subcontent")
    }
  }
  // update editor settings
  // lock container height to avoid reflowing when switching view <-> editor
  editors[id].container.style.height = editors[id].container.offsetHeight - 2 + "px";

  // IE cannot calculate offsetHeight when the element isn't rendered
  if( editors[id].view.offsetHeight ) {
    // make editor as big as the view
    editors[id].editor.style.height = editors[id].view.offsetHeight - 10 + "px";
    // prepare the raw editor (reserve 20px for the editorcontrols
    editors[id].raw.style.height = editors[id].view.offsetHeight - 30 + "px";
  }

  return editors[id];
}

function editContent(id) {
  with( getEditor(id) ) {
    current = raw.value;
    view.style.display = "none";
    controls.all.style.display = "none";
    editor.style.display = "block";
    subcontent.style.display = "none";
  }
}

function cancelContent(id) {
  with( getEditor(id) ) {
    raw.value = current;
    renderContent(id);
    view.style.display = "block";
    subcontent.style.display = "block";
    // match new height of view
    container.style.height = view.offsetHeight + "px";
    controls.all.style.display = "block";
    editor.style.display = "none";
    controls.save.className = "icon save command inactive";
    controls.cancel.className = "icon cancel command inactive";
  }
}

var converter = null;

function renderContent(id) {
  if( window.contentClass == "HtmlContent" ) {
    with( getEditor(id) ) {
      view.innerHTML = raw.value;
    }
    return;
  }
  
  if( !converter ) { converter = new Breakdown.converter(); }
  with( getEditor(id) ) {
    view.innerHTML = converter.makeHtml( raw.value );
    converter.activateHtml( function() {
      // images might not yet be loaded at this point, causing incorrect
      // visual results. temporary solution, wait a bit before actually
      // resizing the container
      setTimeout( function() {
          // match new height of view
          getEditor(id).container.style.height = view.offsetHeight + "px";
      }, 100 );
    } );
  }
}

function previewContent(id) {
  renderContent(id);
  with( getEditor(id) ) {
    view.style.display = "block";
    // match new height of view
    container.style.height = view.offsetHeight + "px";
    controls.all.style.display = "block";
    editor.style.display = "none";
    if( raw.value != current ) {
      // activate save/cancel actions
      controls.save.className = "icon save command active";
      controls.cancel.className = "icon cancel command active";
    }
  }
}

function saveContent(id) {
  with( getEditor(id) ) {
    controls.edit.className   = "icon edit command inactive";
    controls.save.className   = "icon save command inactive";
    controls.cancel.className = "icon cancel command inactive";
    controls.saving.className = "icon wait state active";
    __remote__.store( id, raw.value, function(id) {
      return function confirmSave( response ) {
        with( getEditor(id) ) {
          if( response != "ok" ) { 
            notify( "saving failed: " + response );
            controls.save.className = "icon save command active";
            controls.cancel.className = "icon cancel command active";
          }
          controls.edit.className = "icon edit command active";
          controls.saving.className = "icon wait state inactive";
        }
      }
    }(id) );
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
