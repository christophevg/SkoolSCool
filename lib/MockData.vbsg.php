<?php

function initMockData() {
  if( ! isset($_GET['initMockData'])
      and is_array(SessionManager::getInstance()->MockData) ) { return; }
  SessionManager::getInstance()->MockData = array(
    'users' => array(
      'system' => array( 'login'  => 'system',
                         'type'   => 'User',
                         'name'   => 'system',
                         'pass'   => '',
                         'email'  => 'system@local',
                         'rights' => 'admin' ),
      'xtof' => array( 'login'  => 'xtof',
                       'type'   => 'User',
                       'name'   => 'Christophe VG',
                       'pass'   => md5('xtof'),
                       'email'  => 'xtof@astroboy.local',
                       'rights' => 'contributor,admin' ),
      'kristien' => array( 'login'  => 'kristien',
                           'type'   => 'User',
                           'name'   => 'Kristien T',
                           'pass'   => md5('kristien'),
                           'email'  => 'kristien@astroboy.local',
                           'rights' => 'contributor' )
    ),
    'content' => array(
      '404'  => array( 'cid'      => '404',
                       'author'   => 'system',
                       'type'     => 'PageContent',
                       'time'     => 0,
                       'data'     => <<<EOT
# Whoops

Seems you were looking for a page we don't know about.
EOT
,                      'children' => array()
                     ),
      'unknownContent'  => array( 'cid'      => 'unknownContent',
                                  'author'   => 'system',
                                  'type'     => 'PageContent',
                                  'time'     => 0,
                                  'data'     => <<<EOT
# This page hasn't been created yet...

If you want to create this content go [{{cid}}?create|here].
EOT
,                      'children' => array()
                     ),
      'newContent'  => array( 'cid'      => 'newContent',
                              'author'   => 'system',
                              'type'     => 'PageContent',
                              'time'     => 0,
                              'data'     => <<<EOT
# Create New Content

Please choose the kind of content you want to add...

* [?create&type=PageContent&mode=edit|Page]
* [?create&type=AlbumContent&mode=edit|Album]
* [?create&type=PictureContent&mode=edit|Picture]
EOT
,                      'children' => array()
                     ),
        
      'navigation' => array( 'cid' => 'navigation',
                             'author' => 'system',
                             'type'   => 'PageContent',
                             'time'   => 0,
                             'data'   => <<<EOT
* [onze school]
** [onze missie]
** [ons team]
** [vestiging]
** [schoolbrocure]
** [oudercomite]
** [schoolkrant]
** [links]
* [de klassen]
* [fotoboek]
* [faq]
* [nieuws]
* [kalender]
* [vrijwilligers]
EOT
,                            'children' => array() ),
      'footer' => array( 'cid' => 'footer',
                         'author' => 'system',
                         'type'   => 'PageContent',
                         'time'   => 0,
                         'data'   => <<<EOT
&copy; 2011 - Vrije Basisschool van Schriek en Grootlo
EOT
,                            'children' => array() ),
      'changes' => array( 'cid'      => 'changes',
                          'author'   => 'system',
                          'type'     => 'PageContent',
                          'time'     => 0,
                          'data'     => '',
                          'children' => array() ),
      'home' => array( 'cid'      => 'home',
                       'author'   => 'xtof',
                       'type'     => 'PageContent',
                       'time'     => 1299614027,
                       'data'     => <<<EOT
[include:skins/vbsg/content/nieuws.html?embed]
[include:bericht?embed]

[style:postit belangrijk|inschrijven? waar. hoe, wanneer?]
[style:postit|Het nieuwe schooljaar start op donderdag 1 september 2011!]

[include:album1?embed]
[include:skins/vbsg/content/kalender.html?embed]
EOT
,                      'children' => array()
                     ),
      'bericht' => array( 'cid'      => 'bericht',
                          'author'   => 'xtof',
                          'type'     => 'PageContent',
                          'time'     => 1299614027,
                          'data'     => <<<EOT
# vrijwilligers: bedankt!

Vrijwilliger, wat ben ik onder de indruk van je werk. Telkens weer sta je er voor onze kinderen. Je warmte, inzet, hulp, begrip,... er zijn geen woorden voor. Zo fantastisch dat je dat allemaal doet. Vrijwilliger, je verdient minstens 1000 pluimen op je hoed! Bedankt!

We willen jullie bedanken voor alles wat julle voor onze kinderen, de juffen en meesters en voro de school hebben gedaan. Allemaal welkom op dinsdag 27 juni van 20:30u tot 23u inde Magneet in Grootlo.
EOT
,                      'children' => array( 'comment1', 'comment2' )
                     )
     ,'comment1'    => array( 'cid'      => 'comment1',
                              'author'   => 'xtof',
                              'type'     => 'CommentContent',
                              'time'     => 1299614127,
                              'data'     => "Hallo iedereen,\n\n" .
                                            "we zijn terug van vakantie.\n".
                                            "Hoe was het hier ?\n\n" .
                                            "Niet te veel ambetante kindjes?",
                              'children' => array()
                        )
     ,'comment2'    => array( 'cid'      => 'comment2',
                              'author'   => 'kristien',
                              'type'     => 'CommentContent',
                              'time'     => 1299614187,
                              'data'     => 'Hello Comments Again',
                              'children' => array()
                        )
     ,'comment3'    => array( 'cid'      => 'comment3',
                              'author'   => 'kristien',
                              'type'     => 'CommentContent',
                              'time'     => 1299614587,
                              'data'     => 'What a nice picture',
                              'children' => array()
                        )
     ,'info'    => array( 'cid'      => 'info',
                          'author'   => 'xtof',
                          'type'     => 'PageContent',
                          'time'     => 1299624987,
                          'data'     => <<<EOT
# About our nice school

We really like our school.
EOT
,                         'children' => array()
                        )
     ,'pictures'    => array( 'cid'      => 'pictures',
                              'author'   => 'xtof',
                              'type'     => 'PageContent',
                              'time'     => 1299625987,
                              'data'     => <<<EOT
# Pictures of our school

Below are a bunch of albums with pictures of fun things we do at school...
EOT
,                             'children' => array( 'album1' )
                        )
     ,'album1'    => array( 'cid'      => 'album1',
                            'author'   => 'kristien',
                            'type'     => 'AlbumContent',
                            'time'     => 1299624187,
                            'data'     => 'a:3:{s:4:"body";s:24:"This is our first album.";s:5:"label";s:11:"First Album";s:3:"key";s:4:"pic1";}',
                            'children' => array( 'pic1', 'pic2', 'pic3' )
                        )
     ,'pic1'    => array( 'cid'      => 'pic1',
                          'author'   => 'kristien',
                          'type'     => 'PictureContent',
                          'time'     => 1299624117,
                          'data'     => 'a:2:{s:4:"file";s:13:"picture1.jpeg";s:5:"label";s:6:"Foto 1";}',
                          'children' => array( 'comment3' )
                        )
     ,'pic2'    => array( 'cid'      => 'pic2',
                          'author'   => 'xtof',
                          'type'     => 'PictureContent',
                          'time'     => 1299729117,
                          'data'     => 'a:2:{s:4:"file";s:13:"picture2.jpeg";s:5:"label";s:6:"Foto 2";}',
                          'children' => array( '' )
                        )
     ,'pic3'    => array( 'cid'      => 'pic3',
                          'author'   => 'xtof',
                          'type'     => 'PictureContent',
                          'time'     => 1299939117,
                          'data'     => 'a:2:{s:4:"file";s:13:"picture3.jpeg";s:5:"label";s:6:"Foto 3";}',
                          'children' => array( '' )
                        )
        )
    );
}

initMockData();

// register this store with the Objects Stores
Objects::addStore( 'persistent', new SessionStore( 'MockData' ) );
