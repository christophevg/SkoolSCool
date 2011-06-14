<?php

function initMockData() {
  if( ! isset($_GET['initMockData'])
      and is_array(SessionManager::getInstance()->MockData) ) { return; }
  SessionManager::getInstance()->MockData = array(
    'devel' => array( 
      'users' => array(
        'system' => array( 'login'  => 'system',
                           'name'   => 'system',
                           'pass'   => '',
                           'email'  => 'system@local',
                           'rights' => 'admin' ),
        'xtof' => array( 'login'  => 'xtof',
                         'name'   => 'Christophe VG',
                         'pass'   => md5('xtof'),
                         'email'  => 'xtof@astroboy.local',
                         'rights' => 'contributor,admin' ),
        'kristien' => array( 'login'  => 'kristien',
                             'name'   => 'Kristien T',
                             'pass'   => md5('kristien'),
                             'email'  => 'kristien@astroboy.local',
                             'rights' => 'contributor' )
      ),
      'content' => array(
        '404'  => array( 'cid'      => '404',
                         'author'   => 'system',
                         'type'     => 'page',
                         'time'     => 0,
                         'data'     => <<<EOT
# Whoops

Seems you were looking for a page we don't know about.
EOT
,                        'children' => array()
                       ),
        'navigation' => array( 'cid' => 'navigation',
                               'author' => 'system',
                               'type'   => 'page',
                               'time'   => 0,
                               'data'   =>
'navigation: [home] | [info] | [pictures] | [changes]',
                               'children' => array() ),
        'changes' => array( 'cid'      => 'changes',
                            'author'   => 'system',
                            'type'     => 'page',
                            'time'     => 0,
                            'data'     => '',
                            'children' => array() ),
        'home' => array( 'cid'      => 'home',
                         'author'   => 'xtof',
                         'type'     => 'page',
                         'time'     => 1299614027,
                         'data'     => <<<EOT
Introduction paragraph

# Heading 1
## Heading 2

Some text containing **bold** and *italic* parts, as well as 
[http://christophe.vg|a link to my website] and another one : http://christophe.vg

Internal links are also very simple : see our [pictures].

And a second paragraph

---
* bullet 1
* bullet 2

And some more text in a second paragraph.
EOT
,
                            'children' => array( 'comment1', 'comment2' )
                          )
       ,'comment1'    => array( 'cid'      => 'comment1',
                                'author'   => 'xtof',
                                'type'     => 'comment',
                                'time'     => 1299614127,
                                'data'     => "Hallo iedereen,\n\n" .
                                              "we zijn terug van vakantie.\n".
                                              "Hoe was het hier ?\n\n" .
                                              "Niet te veel ambetante kindjes?",
                                'children' => array()
                          )
       ,'comment2'    => array( 'cid'      => 'comment2',
                                'author'   => 'kristien',
                                'type'     => 'comment',
                                'time'     => 1299614187,
                                'data'     => 'Hello Comments Again',
                                'children' => array()
                          )
       ,'comment3'    => array( 'cid'      => 'comment3',
                                'author'   => 'kristien',
                                'type'     => 'comment',
                                'time'     => 1299614587,
                                'data'     => 'What a nice picture',
                                'children' => array()
                          )
       ,'info'    => array( 'cid'      => 'info',
                            'author'   => 'xtof',
                            'type'     => 'page',
                            'time'     => 1299624987,
                            'data'     => <<<EOT
# About our nice school

We really like our school.
EOT
,                           'children' => array()
                          )
       ,'pictures'    => array( 'cid'      => 'pictures',
                                'author'   => 'xtof',
                                'type'     => 'page',
                                'time'     => 1299625987,
                                'data'     => <<<EOT
# Pictures of our school

Below are a bunch of albums with pictures of fun things we do at school...
EOT
,                               'children' => array( 'album1' )
                          )
       ,'album1'    => array( 'cid'      => 'album1',
                              'author'   => 'kristien',
                              'type'     => 'album',
                              'time'     => 1299624187,
                              'data'     => 'a:3:{s:4:"body";s:24:"This is our first album.";s:5:"label";s:11:"First Album";s:3:"key";s:4:"pic1";}',
                              'children' => array( 'pic1', 'pic2', 'pic3' )
                          )
       ,'pic1'    => array( 'cid'      => 'pic1',
                            'author'   => 'kristien',
                            'type'     => 'picture',
                            'time'     => 1299624117,
                            'data'     => 'a:2:{s:4:"file";s:13:"picture1.jpeg";s:5:"label";s:6:"Foto 1";}',
                            'children' => array( 'comment3' )
                          )
       ,'pic2'    => array( 'cid'      => 'pic2',
                            'author'   => 'xtof',
                            'type'     => 'picture',
                            'time'     => 1299729117,
                            'data'     => 'a:2:{s:4:"file";s:13:"picture1.jpeg";s:5:"label";s:6:"Foto 2";}',
                            'children' => array( '' )
                          )
       ,'pic3'    => array( 'cid'      => 'pic3',
                            'author'   => 'xtof',
                            'type'     => 'picture',
                            'time'     => 1299939117,
                            'data'     => 'a:2:{s:4:"file";s:13:"picture1.jpeg";s:5:"label";s:6:"Foto 3";}',
                            'children' => array( '' )
                          )

                        )
        )
    );
}

initMockData();
