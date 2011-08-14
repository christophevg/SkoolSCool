-- Objects : collapsed inheritance table with history
DROP TABLE IF EXISTS allObjects;
CREATE TABLE allObjects (
  -- OBJECT
  id       VARCHAR(128)  NOT NULL,
  ts       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  type     VARCHAR(128)  NOT NULL,

  -- USER
  name     VARCHAR(128),
  pass     VARCHAR(128),
  email    VARCHAR(128),
  rights   VARCHAR(128),

  -- CONTENT
  author   VARCHAR(128), -- references another Object's id
  children TEXT,         -- CSV-list of Object ids

  -- PAGECONTENT
  body     TEXT,
  
  -- NEWSCONTENT
  date     TIMESTAMP,

  PRIMARY KEY (id, ts)
);

CREATE OR REPLACE VIEW current AS
  SELECT id AS cid, MIN(ts) AS created, MAX(ts) AS updated 
    FROM allObjects
GROUP BY id;

CREATE OR REPLACE VIEW objects AS
  SELECT * FROM allObjects 
  JOIN current ON id = cid AND ts = updated;

-- USERS
INSERT INTO allObjects ( type, id, name, pass, email, rights )
VALUES
( 'User', 'system',  'system',          '', '', 'admin'             ),
( 'User', 'xtof',    'Christophe VG',   '', '', 'contributor,admin' ),
( 'User', 'patrick', 'Meester Patrick', '', '', 'contributor,admin' ),
( 'User', 'user',    'Simple User',     '', '', '' );

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
[include:bedankt2011?embed]

[style:postit belangrijk|inschrijven? waar. hoe, wanneer?]
[style:postit|Het nieuwe schooljaar start op donderdag 1 september 2011!]

[include:laatste-foto?embed]
[include:agenda?embed]' ),

( 'PageContent', 'bedankt2011', 'system',
  '# vrijwilligers: bedankt!

Vrijwilliger, wat ben ik onder de indruk van je werk. Telkens weer sta je er voor onze kinderen. Je warmte, inzet, hulp, begrip,... er zijn geen woorden voor. Zo fantastisch dat je dat allemaal doet. Vrijwilliger, je verdient minstens 1000 pluimen op je hoed! Bedankt!

We willen jullie bedanken voor alles wat julle voor onze kinderen, de juffen en meesters en voro de school hebben gedaan. Allemaal welkom op dinsdag 27 juni van 20:30u tot 23u inde Magneet in Grootlo.
');

-- HTML
INSERT INTO allObjects ( type, id, name, body )
VALUES
( 'HtmlContent', 'agenda', 'system',
  '<h1>Agenda</h1>
  
  <ul id="agenda">
  </ul>
  
  <script>
    var myGoogle = Cal.providers.google
      .connect( "gvbs.schriek-grootlo%40scarlet.be" );

    new Cal.calendar()
       .useDataProvider( myGoogle )
       .processWith    ( display  )
       .findEvents     ();

    function display( events ) {
      var html = "";
      for( var day in events ) {
        for( var e=0; e<events[day].length; e++ ) {
          html += "<li>" + day + " " + events[day][e].subject + "</li>";
        }
      }
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
    var myCalendar = Cal.activate                ( "c1"                  )
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

    var myNavigator = Cal.activate            ( "c2"           )
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
  </script>' );

-- SPECIALS
INSERT INTO allObjects ( type, id, name )
VALUES
( 'NewsList', 'nieuws', 'system' );
