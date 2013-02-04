function addContent() {
  var form = document.getElementById('addcontent-form');
  var name = document.getElementById('addcontent-name').value;

  if( name.match( /[a-z]+/ ) ) {
    if(document.getElementById("selectContentType").value == "AlbumContent"
       || document.getElementById("selectContentType").value == "FileContent")
    {
      lock_button();
      upload_feedback( "Uw bestand wordt overgebracht naar de server...", 5 );
    } else {
      form.action = name.replace( / /g, "-" );
    }
    form.submit();
  } else {
    alert( "Gelieve een naam te kiezen." );
    document.getElementById("addcontent-name").focus();
  }
  return false;
}

function clear_upload_feedback() {
  document.getElementById( 'progress-msg' ).innerHTML = "";
  document.getElementById( 'progress-bar' ).style.width = "0%";
}

function upload_feedback( msg, pct ) {
  document.getElementById( 'progress-msg' ).innerHTML = msg;
  document.getElementById( 'progress-bar' ).style.width = pct + "%";
}

function upload_abort( error ) {
  reset_form();
  upload_feedback( error, 100);
}

function upload_done() {
  upload_feedback( "Klaar ...", 100 );
  if(document.getElementById('selectContentType').value == "AlbumContent") {
    alert( "Uw album is overgebracht naar de server. De foto's zijn weldra " + 
           "beschikbaar in het fotoboek. Dit kan tot 15 minuten duren." );
  } else {
    window.prompt( "Uw bestand is beschikbaar via de website op onderstaande " +
                   "referentie. ", "bestanden/" + 
                   document.getElementById('addcontent-name').value );
  }
  reset_form();
}

function lock_button() {
  var button = document.getElementById( 'addcontent-submit' );
  button.value = "Bezig...";
  button.disabled = true;
}

function reset_button() {
  var button = document.getElementById( 'addcontent-submit' );
  button.value = "voeg toe...";
  button.disabled = false;
}

function reset_file() {
  document.getElementById( 'album-file' ).value = "";
  document.getElementById( 'file-file' ).value = "";
}

function reset_form() {
  document.getElementById( 'addcontent-form' ).reset();
  reset_button();
  reset_file();
  clear_upload_feedback();
}

function changeContent() {
  var type = document.getElementById("selectContentType").value;
  document.getElementById( "addcontent-form" ).className = type;
  document.getElementById( "addcontent-name" ).value = "";
  document.getElementById( "addcontent-name-error" ).innerHTML = "";
  clear_upload_feedback();
  switch( type ) {
    case "PageContent":
    case "NewsContent":
    case "HtmlContent":
      document.getElementById( "addcontent-form" ).method="GET";
      document.getElementById( "addcontent-form" ).enctype="";
      document.getElementById( "addcontent-form" ).target="";
      break;
    case "FileContent":
    case "AlbumContent":
      document.getElementById( "addcontent-form" ).method="POST";
      document.getElementById( "addcontent-form" ).enctype="multipart/form-data";
      document.getElementById( "addcontent-form" ).encoding="multipart/form-data";
      document.getElementById( "addcontent-form" ).target="iframe";
      break;
  }
}

function checkName() {
  var input = document.getElementById("addcontent-name");
  var value = input.value.replace(/^\s+/, "").replace(/\s+$/, ""); // trim
  if( document.getElementById('selectContentType').value == "FileContent" ) {
    value = "bestanden/" + value;
  }
  var spinner = document.getElementById("addcontent-name-spinner");
  var error   = document.getElementById("addcontent-name-error");
  error.innerHTML = "";
  if( value != '' ) {
    spinner.className = "icon wait state active";
    __remote__.fetch( value, function(response) {
      spinner.className = "icon wait state inactive";
      if( response == value ) {
        error.innerHTML = "Deze naam is reeds in gebruik";
        input.focus();
        input.select();
      }
    } );
  }
}

var waiting_before_checking_name_change = null;

function watchChangeName() {
  if( waiting_before_checking_name_change != null ) { 
    clearTimeout( waiting_before_checking_name_change );
  }
  waiting_before_checking_name_change = setTimeout( checkName, 1000 );
}

function validate_file(file) {
  if( file.value.match(/\.(pdf|PDF|png|PNG|jpg|JPG|jpeg|JPEG)$/) ) {
    var result = "ok";
  } else {
    var result = "nok";
    file.value = "";
  }
  document.getElementById("addcontent-form").className = "FileContent-" + result;
  document.getElementById("addcontent-name").value = 
    file.value.replace(/ /, "_")
              .replace( /^([^\\\/]*[\\\/])*/g, "" );
  checkName();
}

function validate_album(file) {
  if( file.value.match(/\.(zip|ZIP)$/) ) {
    var result = "ok";
  } else {
    var result = "nok";
    file.value = "";
  }
  document.getElementById("addcontent-form").className = "AlbumContent-" + result;
  document.getElementById("addcontent-name").value = 
    file.value.replace( /\..*$/, "").replace( /^([^\\\/]*[\\\/])*/g, "" );
}
