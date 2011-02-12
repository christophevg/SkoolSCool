<?php

$GLOBALS['MockData'] = array(
  'devel' => array( 
    'users' => array(
      'xtof' => array( 'name'   => 'Christophe VG',
                       'pass'   => md5('xtof'),
                       'rights' => 'contributor,admin' )
    ),
    'content' => array(
      'default' => array( 'author'   => 'xtof',
                          'type'     => 'page',
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
      'sub1'    => array( 'author'   => 'xtof',
                          'type'     => 'comment',
                          'data'     => 'Hello Comments',
                          'children' => array()
                        ),
      'sub2'    => array( 'author'   => 'xtof',
                          'type'     => 'comment',
                          'data'     => 'Hello Comments Again',
                          'children' => array()
                        )
    )
  )
);
