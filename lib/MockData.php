<?php

$GLOBALS['MockData'] = array(
  'devel' => array( 
    'users' => array(
      'xtof' => array( 'name' => 'Christophe VG',
                       'pass' => md5('xtof') )
    ),
    'content' => array(
      'default' => array( 'author'   => 'xtof',
                          'type'     => 'page',
                          'data'     => 'Hello World',
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
