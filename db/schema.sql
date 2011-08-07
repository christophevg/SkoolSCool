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
( 'User', 'patrick', 'Meester Patrick', '', '', 'contributor,admin' );

-- PAGECONTENT
INSERT INTO allObjects ( type, id, author, children, body )
VALUES
( 'PageContent', '404', 'system', '', 
  '# Oei...

Je hebt een pagina gevraagd die we niet kennen.' ),
( 'PageContent', 'unknownContent', 'system', '', 
  '# Deze pagina is nog niet aangemaakt...

Om inhoud te maken voor deze pagina, volg [{{id}}?create|deze link].' ),
( 'PageContent', 'newContent', 'system', '', '# Maak nieuwe inhoud aan...

Kies het soort inhoud dat je graag wil aanmaken:

## Een pagina

... laat je toe om een tekstuele inhoud toe te voegen aan de website.
Je kan vervolgens naar deze pagina een link leggen vanuit andere pagina\'s of
vanuit de navigatie.

[{{id}}?create&type=PageContent&mode=edit|maak een pagina...]

## Een fotoboek

... is een verzameling van foto\'s. Je kan er foto\'s aan toewijzen, er
in ordenen, ...

[{{id}}?create&type=AlbumContent&mode=edit|maak een fotoboek...]

## Een foto

Wil je een gewone losse foto toevoegen aan de site, dan kan ke dat langs deze
weg. Foto\'s kunnen nadien in pagina\'s gebruikt worden of toegewezen worden aan
een fotoboek.

[{{id}}?create&type=PictureContent&mode=edit|voeg een foto toe...]' ),
( 'PageContent', 'navigation', 'system', '', '* [onze school]
** [onze school/onze missie|onze missie]
** [onze school/ons team|ons team]
** [onze school/vestigingen|vestigingen]
** [onze school/schoolbrocure|schoolbrochure]
** [onze school/oudercomite|oudercomite]
** [onze school/schoolkrant|schoolkrant]
** [onze school/links|links]
* [de klassen]
* [fotoboek]
* [faq]
* [nieuws]
* [kalender]
* [vrijwilligers]' ),
( 'PageContent', 'footer', 'system', '', 
  '&copy; 2011 - Vrije Basisschool van Schriek en Grootlo' ),
( 'PageContent', 'home', 'system', '',
  '[include:skins/vbsg/content/nieuws.html?embed]
[include:bedankt2011?embed]

[style:postit belangrijk|inschrijven? waar. hoe, wanneer?]
[style:postit|Het nieuwe schooljaar start op donderdag 1 september 2011!]

[include:album1?embed]
[include:skins/vbsg/content/kalender.html?embed]' ),
( 'PageContent', 'bedankt2011', 'system', '',
  '# vrijwilligers: bedankt!

Vrijwilliger, wat ben ik onder de indruk van je werk. Telkens weer sta je er voor onze kinderen. Je warmte, inzet, hulp, begrip,... er zijn geen woorden voor. Zo fantastisch dat je dat allemaal doet. Vrijwilliger, je verdient minstens 1000 pluimen op je hoed! Bedankt!

We willen jullie bedanken voor alles wat julle voor onze kinderen, de juffen en meesters en voro de school hebben gedaan. Allemaal welkom op dinsdag 27 juni van 20:30u tot 23u inde Magneet in Grootlo.
');
