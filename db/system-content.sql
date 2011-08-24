-- USERS
INSERT INTO allObjects ( type, id, name, pass, email, rights )
VALUES
( 'User', 'system',  'system',          '', '', 'admin'             );

-- PAGECONTENT
INSERT INTO allObjects ( type, id, author, body )
VALUES
( 'PageContent', '404', 'system',
  '# Oei...

Je hebt een pagina gevraagd die we niet kennen.' ),

( 'PageContent', 'unknownContent', 'system',
  '# Deze pagina is nog niet aangemaakt...

Om inhoud te maken voor deze pagina, volg [{{id}}?create|deze link].' ),
( 'PageContent', 'newContent', 'system',
  '# Maak nieuwe inhoud aan...

Kies het soort inhoud dat je graag wil aanmaken:

## Een pagina

... laat je toe om een tekstuele inhoud toe te voegen aan de website.
Je kan vervolgens naar deze pagina een link leggen vanuit andere pagina\'s of
vanuit de navigatie.

[{{id}}?create&type=PageContent&mode=edit|maak een pagina...]

## Een nieuws item

... is een pagina, met een extra datum veld. Niews items worden ook opgenomen
in de lijst van het nieuws, zoals ook bvb op de home pagina.

[{{id}}?create&type=NewsContent&mode=edit|maak een nieuws item...]'),

( 'PageContent', 'navigation', 'system', 
  '* [onze school]
** [onze school/onze missie|onze missie]
** [onze school/ons team|ons team]
** [onze school/vestigingen|vestigingen]
** [onze school/schoolbrocure|schoolbrochure]
** [onze school/oudercomite|oudercomite]
** [onze school/schoolkrant|schoolkrant]
** [onze school/links|links]
* [de klassen]
* [fotoboek]
* [vragen]
* [nieuws]
* [kalender]
* [vrijwilligers]' ),

( 'PageContent', 'footer', 'system',
  '&copy; 2011 - Vrije Basisschool van Schriek en Grootlo' ),

( 'PageContent', 'home', 'system',
  '[include:nieuws?embed]
[include:nieuws/bedankt2011?embed]

[vragen/inschrijven|[style:postit belangrijk|inschrijven? waar. hoe, wanneer? Lees er alles over...]]
[style:postit|Het nieuwe schooljaar start op donderdag 1 september 2011!]

[include:laatste-foto?embed]
[include:agenda?embed]' );

-- HTML
INSERT INTO allObjects ( type, id, author, body )
VALUES
( 'HtmlContent', 'agenda', 'system',
  '<h1>Agenda</h1>
  
  <ul id="agenda">
    <div class="loading"></div>
  </ul>
  
  <script>
    var myGoogle = Cal.providers.google
      .connect( "gvbs.schriek-grootlo%40scarlet.be" );

    new Cal.calendar()
       .useDataProvider( myGoogle )
       .processWith    ( display  )
       .findEvents     (); // by default from now to now+1 month

    var months = [ "Jan", "Feb", "Maa", "Apr", "Mei", "Jun", 
                   "Jul", "Aug", "Sep", "Okt", "Nov", "Dec" ];

    function display( events ) {
      var html = "";

      var dates = [];
      for( var date in events ) {
          dates.push(date);
      }
      var sorted = dates.sort();

      for( var i=0; i<sorted.length; i++ ) {
        var d = new Date(sorted[i].replace(/-/g," "));
        var day = d.getDate() + " " + months[d.getMonth()] + " " + d.getFullYear();
        for( var e=0; e<events[sorted[i]].length; e++ ) {
          html += "<div class=\\"agenda\\">" 
               +  "<span class=\\"date\\">" + day + " - </span>" 
               +  "<span class=\\"item\\">" + events[sorted[i]][e].subject + "</span>"
               +  "</div>";
        }
      }
      html += "<p class=\\"more\\">"
           +  "<a href=\\"kalender\\">toon de volledige kalender...</a>"
           +  "</p>";
      document.getElementById( "agenda" ).innerHTML = html;
    }
  </script>' ),

( 'HtmlContent', 'kalender', 'system',
  '<div id="navigator">
    <div class="header">
      <span id="navigator-now"></span>
      <div class="controls">  
        <a href="javascript:" onclick="myNavigator.gotoPreviousMonth()">&lt;</a>
        <a href="javascript:" onclick="myNavigator.gotoNextMonth()">&gt;</a>
      </div>
    </div>

  <table id="c2" class="navigator">
    <tr>
      <td id="c21"></td>      <td id="c22"></td>      <td id="c23"></td>
      <td id="c24"></td>      <td id="c25"></td>
      <td id="c26" class="weekend"></td>      
      <td id="c27" class="weekend"></td>
    </tr>
    <tr>
      <td id="c28"></td>      <td id="c29"></td>      <td id="c210"></td>
      <td id="c211"></td>     <td id="c212"></td>
      <td id="c213" class="weekend"></td>      
      <td id="c214" class="weekend"></td>
    </tr>
    <tr>
      <td id="c215"></td>     <td id="c216"></td>     <td id="c217"></td>
      <td id="c218"></td>     <td id="c219"></td>
      <td id="c220" class="weekend"></td>      
      <td id="c221" class="weekend"></td>
    </tr>
    <tr>
      <td id="c222"></td>     <td id="c223"></td>     <td id="c224"></td>
      <td id="c225"></td>     <td id="c226"></td>
      <td id="c227" class="weekend"></td>      
      <td id="c228" class="weekend"></td>
    </tr>
    <tr>
      <td id="c229"></td>     <td id="c230"></td>     <td id="c231"></td>
      <td id="c232"></td>     <td id="c233"></td>
      <td id="c234" class="weekend"></td>      
      <td id="c235" class="weekend"></td>
    </tr>
    <tr>
      <td id="c236"></td>     <td id="c237"></td>     <td id="c238"></td>
      <td id="c239"></td>     <td id="c240"></td>
      <td id="c241" class="weekend"></td>      
      <td id="c242" class="weekend"></td>
    </tr>
  </table>
</div>

  <div id="calendar">
  <a href="javascript:" onclick="myCalendar.gotoPreviousMonth()">vorige</a> - 
  <a href="javascript:" onclick="myCalendar.gotoToday()">vandaag</a> - 
  <a href="javascript:" onclick="myCalendar.gotoNextMonth()">volgende</a>
  <span id="now"></span>
  <table id="c1" class="calendar">
    <tr>
      <td id="c11"></td>      <td id="c12"></td>      <td id="c13"></td>
      <td id="c14"></td>      <td id="c15"></td>
      <td id="c16" class="weekend"></td>      
      <td id="c17" class="weekend"></td>
    </tr>
    <tr>
      <td id="c18"></td>      <td id="c19"></td>      <td id="c110"></td>
      <td id="c111"></td>     <td id="c112"></td>
      <td id="c113" class="weekend"></td>      
      <td id="c114" class="weekend"></td>
    </tr>
    <tr>
      <td id="c115"></td>     <td id="c116"></td>     <td id="c117"></td>
      <td id="c118"></td>     <td id="c119"></td>
      <td id="c120" class="weekend"></td>      
      <td id="c121" class="weekend"></td>
    </tr>
    <tr>
      <td id="c122"></td>     <td id="c123"></td>     <td id="c124"></td>
      <td id="c125"></td>     <td id="c126"></td>
      <td id="c127" class="weekend"></td>      
      <td id="c128" class="weekend"></td>
    </tr>
    <tr>
      <td id="c129"></td>     <td id="c130"></td>     <td id="c131"></td>
      <td id="c132"></td>     <td id="c133"></td>
      <td id="c134" class="weekend"></td>      
      <td id="c135" class="weekend"></td>
    </tr>
    <tr>
      <td id="c136"></td>     <td id="c137"></td>     <td id="c138"></td>
      <td id="c139"></td>     <td id="c140"></td>
      <td id="c141" class="weekend"></td>      
      <td id="c142" class="weekend"></td>
    </tr>
  </table>
</div>
  
  <script>
    window.onload = function() {
    // setup a connection to Google, using one of the default providers
    var myGoogle = Cal.providers.google
      .connect( "gvbs.schriek-grootlo%40scarlet.be" );
    
    // create your calender object, pointing it to our HTML
    window.myCalendar = Cal.activate                ( "c1"                  )
                        .useDataProvider         ( myGoogle              )
                        .notifyOfDaySelection    ( processDaySelection   )
                        .notifyOfEventSelection  ( showEvent )
                        .gotoToday();

    var noDataProvider = {
      getData : function getData(start, end, cb, ctx) { 
        cb.apply( ctx, [ {} ] );
      }
    }
    
    function gotoDay(day) {
      updateLabel(day, document.getElementById("navigator-now"));
      this.gotoDate(day) // on the navigator
      myCalendar.gotoDate(day) // and on the calendar itself
    }

    window.myNavigator = Cal.activate            ( "c2"           )
                         .useDataProvider     ( noDataProvider )
                         .notifyOfDaySelection( gotoDay        )
                         .gotoToday();
                         
    // when a new date/day has been selected...
    // this handler is executed within the scope of the calendar, so "this"
    // _is_ the actual calendar, so we can call any method on this.
    function processDaySelection(day) {
      updateLabel(day, document.getElementById("now")); // update the label
      this.gotoDate(day); // we only got the event, now we want to set it
    }

    // add a textual representation of the month of the given date
    function updateLabel(date, label) {
      var monthLabel = [ "januari", "februari", "maart", "april", "mei",
                         "juni", "juli", "augustus", "september", "oktober",
                         "november", "december" ];
      label.innerHTML = 
        monthLabel[date.getMonth()] + " " + date.getFullYear();
    }

    // when an event has been selected ...
    function showEvent(event, elem) {
      if( console && typeof console.log == "function" ) {
        console.log( "TODO", event.subject, event.type, event.calendar, 
                     event.start, event.end );
      }
    }
  }
  </script>' ),
( 'HtmlContent', 'fotoboek', 'system',
  '
    <span id="back2albums" class="action"><a href="javascript:gotoAlbums();">&lt;&lt; albums</a></span>
  <span id="back2table"class="action"> | <a class="action"  href="javascript:gotoTable();">&lt; overview</a></span>
  <span id="tools">
    <span id="fit" class="action"> | <a href="javascript:fit();">&gt; fit &lt;</a></span>
    <span id="full"class="action"> | <a href="javascript:full();">&lt; full &gt;</a></span>
  </span>
  
  <div id="myViewer-albums" class="albums"></div>
  <div id="myViewer-table"  class="table"></div>
  <div id="myViewer-photo"  class="photo"></div>
  <div id="myViewer-thumbs" class="thumbs"></div>
  
  <script>
  
    function gotoAlbums() {
      myViewer.gotoAlbums();
    }
    
    function gotoTable() {
      myViewer.gotoTable();
    }
  
    function showAlbums() {
      // show albums
      document.getElementById("myViewer-albums").style.display = "block";
      // hide table and detailed view
      document.getElementById("myViewer-table").style.display  = "none";
      document.getElementById("myViewer-thumbs").style.display = "none";
      document.getElementById("myViewer-photo").style.display  = "none";
      // hide navigation
      document.getElementById( "back2table" ).style.display    = "none";
      document.getElementById( "back2albums" ).style.display   = "none";
      // hide tools
      document.getElementById( "tools" ).style.display         = "none";
    }

    function showTable() {
      // hide albums
      document.getElementById("myViewer-albums").style.display = "none";
      // show table
      document.getElementById("myViewer-table").style.display  = "block";
      // hide detailed view
      document.getElementById("myViewer-thumbs").style.display = "none";
      document.getElementById("myViewer-photo").style.display  = "none";
      // show/hide navigation
      document.getElementById( "back2albums" ).style.display   = "inline";
      document.getElementById( "back2table" ).style.display    = "none";
      // hide tools
      document.getElementById( "tools" ).style.display         = "none";
    }
    
    function showPhoto() {
      // hide albums
      document.getElementById("myViewer-albums").style.display = "none";
      // show table
      document.getElementById("myViewer-table").style.display  = "none";
      // hide detailed view
      document.getElementById("myViewer-thumbs").style.display = "block";
      document.getElementById("myViewer-photo").style.display  = "block";
      // show/hide navigation
      document.getElementById( "back2albums" ).style.display   = "inline";
      document.getElementById( "back2table" ).style.display    = "inline";
      // show tools
      document.getElementById( "tools" ).style.display         = "inline";
      // reset tools
      scale = 1;
      document.getElementById( "fit" ).style.display  = "inline";
      document.getElementById( "full" ).style.display = "none";
    }

    var myGoogle = Photo.providers.google.connect( "106409351044330241707" );
    
    var scale = 1;
    var isIE  = navigator.userAgent.match( /MSIE/ ) == "MSIE";
    
    function fit() {
      scale = 1;
      var photo = document.getElementById( "myViewer-photo" );
      if( ! photo || ! photo.firstChild ) { 
        return;
      }
      var img   = photo.firstChild.firstChild,
          scaleWidth = img.width / (photo.offsetWidth - 10),
          scaleHeight = img.height / (photo.offsetHeight - 10);
      // sometimes the photo isnt displayed correctly and the width = 0
      // and it seems I cant put this in the photo.js code itself ?!
      if( img.width < 5 ) { myViewer.refreshPhoto() }
      scale = scaleWidth > scaleHeight ? scaleWidth : scaleHeight;
      img.width  /= scale;
      if( isIE ) { img.height /= scale; }
      document.getElementById( "fit" ).style.display  = "none";
      document.getElementById( "full" ).style.display = "inline";
    }
    
    function full() {
      var photo = document.getElementById( "myViewer-photo" ),
          img   = photo.firstChild.firstChild;
      img.width  *= scale;
      if( isIE ) { img.height *= scale; }
      document.getElementById( "fit" ).style.display  = "inline";
      document.getElementById( "full" ).style.display = "none";
    }
    
    var myViewer = Photo.activate          ( "myViewer-" )
                        .useDataProvider   ( myGoogle    )
                        .onShowAlbums      ( showAlbums  )
                        .onAlbumSelection  ( showTable   )
                        .onPreviewSelection( showPhoto   )
                        .onPhotoSelection  ( showPhoto   )
                        .onPhotoLoad       ( fit         );
  </script>');

-- SPECIALS
INSERT INTO allObjects ( type, id, author )
VALUES
( 'NewsList', 'nieuws', 'system' );
