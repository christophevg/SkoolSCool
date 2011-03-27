<?php

function initMockData() {
  if( ! $_GET['initMockData']
      and is_array(SessionManager::getInstance()->MockData) ) { return; }
  SessionManager::getInstance()->MockData = array(
    'devel' => array( 
      'users' => array(
        'xtof' => array( 'name'   => 'Christophe VG',
                         'pass'   => md5('xtof'),
                         'email'  => 'xtof@astroboy.local',
                         'rights' => 'contributor,admin' ),
        'kristien' => array( 'name'   => 'Kristien T',
                             'pass'   => md5('kristien'),
                             'email'  => 'kristien@astroboy.local',
                             'rights' => 'contributor' )
      ),
      'content' => array(
        'default' => array( 'cid'      => 'default',
                            'author'   => 'xtof',
                            'type'     => 'page',
                            'time'     => 1299614027,
                            'data'     => <<<EOT
Introduction paragraph

# Heading 1
## Heading 2

Some text containing **bold** and *italic* parts, as well as [a link to my 
website](http://christophe.vg) and another one : http://christophe.vg

And a second paragraph

---
* bullet 1
* bullet 2

And some more text in a second paragraph.
EOT
,
                            'children' => array( 'sub1', 'sub2' )
                          ),
        'sub1'    => array( 'cid'      => 'sub1',
                            'author'   => 'xtof',
                            'type'     => 'comment',
                            'time'     => 1299614127,
                            'data'     => "Hallo iedereen,\n\n" .
                                          "we zijn terug van vakantie.\n" .
                                          "Hoe was het hier ?\n\n" .
                                          "Niet te veel ambetante kindjes?",
                            'children' => array()
                          ),
        'sub2'    => array( 'cid'      => 'sub2',
                            'author'   => 'kristien',
                            'type'     => 'comment',
                            'time'     => 1299614187,
                            'data'     => 'Hello Comments Again',
                            'children' => array()
                          )
                        )
        )
    );
}

initMockData();
