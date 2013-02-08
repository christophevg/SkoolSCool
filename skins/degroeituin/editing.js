/**
 * editor class for managing editors on a page
 *
 * basic usage:
 *   Editor.get(id).add( field[, field,...] );
 *                 .edit();
 *                 .preview();
 *                 .save();
 *                 .cancel();
 *
 * TODO: - replace display setting with parent-level className and move all
 *         display changes to static CSS rules
 */

(function(globals){
  
  var
  
  // private functionality
  
  queryParameter = function queryParameter( name ) {
    var expr = "[\\?&]"+name+"=([^&#]*)";
    var results = new RegExp(expr).exec( window.location.href );
    if( results == null ) { return null; }
    return results[1];
  },

  log = function log( msg ) {
    if( console && typeof console.log == "function" ) {
      console.log( msg );
    }
  },
  
  // short-hand function to find editor-related elements
  get = function get( name ) {
    return document.getElementById
      ( ( typeof this.id != "undefined" ? this.id : "" ) + name );
  },
  
  // cache references to all components involved in the editor
  detectComponents = function detectComponents() {
    this.container = get.call( this, "Container" );
    this.view      = get.call( this, "View"      );
    this.editor    = get.call( this, "Editor"    );
    this.controls = {
        all     : get.call( this, "Controls"      )
      , edit    : get.call( this, "EditCommand"   )
      , save    : get.call( this, "SaveCommand"   )
      , cancel  : get.call( this, "CancelCommand" )
      , saving  : get.call( this, "SavingState"   )
    };
    // FIXME: subcontent isn't in the DOM yet
    // this.subcontent = get( "subcontent" ); 
  },

  // in the future, these quirks need to be solved properly
  prepare = function prepare() {
    // update editor settings
    // lock container height to avoid reflowing when switching view <-> editor
    this.container.style.height = this.container.offsetHeight - 2 + "px";

    // IE cannot calculate offsetHeight when the element isn't rendered
    if( this.view.offsetHeight ) {
      // make editor as big as the view
      this.editor.style.height = this.view.offsetHeight - 10 + "px";
    }
  },
  
  // concatenate all fields' content to build a state
  determineState = function determineState() {
    return asHash.apply(this);
  },

  // if we didn't previously
  keepState = function keepState() {
    this.state = this.state || determineState.apply(this);
  },
  
  restoreState = function restoreState() {
    var fieldCount = this.fields.length;
    for( var i=0; i<fieldCount; i++ ) {
      var name = this.fields[i].id.replace( this.id, "" );
      this.fields[i].value = this.state[name];
    }
  },

  // clear the state
  discardState = function discardState() {
    this.fieldState = null;
  },
  
  // compare the previously recorded state with the current to determine if
  // changes have been made
  isDirty = function isDirty() {
    var fieldCount = this.fields.length;
    for( var i=0; i<fieldCount; i++ ) {
      var name = this.fields[i].id.replace( this.id, "" );
      if( this.fields[i].value != this.state[name] ) { 
        return true;
      }
    }
    return false;
  },
  
  // constructs a hash with name => value pairs for all fields
  asHash = function asHash() {
    var fieldCount = this.fields.length;
    var hash = {};
    for( var i=0; i<fieldCount; i++ ) {
      var name = this.fields[i].id.replace( this.id, "" );
      hash[name] = this.fields[i].value;
    }
    return hash;
  },
  
  // displays an element through CSS
  show = function show( element ) {
    element.style.display = "block";
  },

  // hides an element through CSS
  hide = function hide( element ) {
    element.style.display = "none";
  },

  // displays an element through CSS-classes
  activate = function activate( command ) {
    command.className = command.className.replace( "inactive", "active" );
  };

  // hides an element through CSS-classes
  deactivate = function deactivate( command ) {
    command.className = command.className.replace( "active", "inactive" );
  };
  
  // renders the value of the body 
  renderContent = function renderContent() {
    var newContent = this.body ? this.body.value : "";
    
    // HtmlContent can be directly shown
    if( window.contentClass == "HtmlContent" ) {
      this.view.innerHTML = newContent;
      return;
    }

    // PageContent & NewsContent are breakdown based
    var converter = new Breakdown.converter();
    this.view.innerHTML = converter.makeHtml( newContent );
    converter.activateHtml( function() {
      // images might not yet be loaded at this point, causing incorrect
      // visual results. temporary solution, wait a bit before actually
      // resizing the container
      var container = this.container;
      setTimeout( function() {
          // match new height of view
          container.style.height = view.offsetHeight + "px";
      }, 100 );
    } );
  },
  
  // private constructor
  editor = function editor(id) {
    this.id         = id;
    this.fields     = [];
    detectComponents.apply(this);
    prepare.apply(this);
  },
  
  // public interface of editor class

  // adds a field to the scope of the editor
  editor.prototype.add = function add( name ) {
    var element = get.call( this, name );
    this.fields.push( element );
    return element;
  },

  // adds a field to the scope of the editor and marks it as the body
  editor.prototype.addBody = function addBody( name ) {
    return this.body = this.add( name );
  },

  // put the editor in edit mode
  editor.prototype.edit = function edit() {
    keepState.apply(this);
    hide( this.view         );
    hide( this.controls.all );
    show( this.editor       );
    // FIXME: hide( this.subcontent   );
  },

  // put the editor in preview mode
  editor.prototype.preview = function preview() {
    renderContent.apply(this);

    show( this.view         );
    show( this.controls.all );
    hide( this.editor       );

    this.container.style.height = this.view.offsetHeight + "px";

    // if the editor contains changed content, we offer the possibility to
    // save/cancel
    if( isDirty.apply(this) ) {
      activate( this.controls.save   );
      activate( this.controls.cancel );
    } else {
      deactivate( this.controls.save   );
      deactivate( this.controls.cancel );
    }
  },

  editor.prototype.save = function save() {
    deactivate( this.controls.edit   );
    deactivate( this.controls.save   );
    deactivate( this.controls.cancel );
    
    activate( this.controls.saving );
    
    var data = asHash.apply(this);
    
    __remote__.store( this.id, JSON.stringify(data), function(id) {
      return function confirmSave( response ) {
        with( Editor.get(id) ) {
          if( response != "ok" ) { 
            notify( "saving failed: " + response );
            // save failed, keep controls available
            activate( controls.save   );
            activate( controls.cancel );
          } else {
            // save was successful, reset state
            discardState();
          }
          // always provide edit command
          activate( controls.edit );
          // we're done with this call, hide the spinner
          deactivate( controls.saving );
        }
      }
    }(this.id) );
  },

  editor.prototype.cancel = function cancel() {
    restoreState.apply(this);
    renderContent.apply(this);

    show( this.view         );
    // FIXME: show( this.subcontent   ); 
    show( this.controls.all );
    hide( this.editor       );

    deactivate( this.controls.save   );
    deactivate( this.controls.cancel );

    // match new height of view
    this.container.style.height = this.view.offsetHeight + "px";
  },
  
  // expose the Editor namespace globally
  pub = globals.Editor = {},
  
  // cache of lazy-loaded editors
  editors = {};

  // public API
  
  pub.get = function get(id) {
    if( typeof editors[id] == "undefined" ) {
      editors[id] = new editor(id);
    }
    return editors[id];
  };
  
  // trigger the editor if mode=edit
  if( queryParameter( 'mode' ) == 'edit' ) {
    window.onload = function() { Editor.get(bodyContent).edit(); };
  }
  
})(window);
