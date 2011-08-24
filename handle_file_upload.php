<?php

function flushIt( $count = 256) {
  echo str_repeat( "<br>\n", $count );
  flush(); ob_flush();
}

function start() {
  // needed or else the browser won"t do it synchronously and still cache
  echo "<h1>Progress</h1>\n\n"; 
  flushIt();
}

$total = 0;

function report_progress( $msg, $pct = 0 ) {
  global $total;
  $total += $pct;
  if( $total > 100 ) { $total = 100; }
  print "<script> window.parent.upload_feedback( \"$msg\", $total ); </script>";
  flushIt();
}

function report_error( $msg ) {
  report_progress( "ERROR $msg", 100 );
}

function terminate( $msg ) {
  report_error( $msg );
  exit();
}

function accept_file( $uploaddir = '/tmp' ) {
  mkdir( $uploaddir );
  $uploadfile = $uploaddir . '/' . basename($_FILES['file']['name']);

  if( move_uploaded_file( $_FILES['file']['tmp_name'], $uploadfile ) ) {
    return $uploadfile;
  }
  return false;
}

function unzip_file( $file ) {
  $dir = dirname($file);

  `cd $dir; unzip -j -d . $file`;

  // unzip in directory
  $files = array();
  if( $handle = opendir( $dir ) ) {
    while( false !== ( $file = readdir($handle) ) ) {
      if( preg_match( "/^[^\.].*\.[jpegJPEGjpgJPG]+$/", $file ) ) {
        $files[] = $dir . '/' . $file;
      }
    }
    closedir($handle);
  }
  
  return $files;
}

function resize_photo( $file, $max_large, $max_small ) {
	list( $width, $height ) = getimagesize( $file );

  if( $width >= $height ) {
    $large = $width;
    $small = $height;
  } else {
    $large = $height;
    $small = $width;
  }

  if( $large <= $max_large && $small <= $max_small ) { 
    print "nothing todo\n";
    return;
  }

  // one or both are larger determine the scale
  $scale_large = $max_large / $large;
  $scale_small = $max_small / $small;

  $scale = $scale_large < $scale_small ? $scale_large : $scale_small;

  // apply scale
  $newWidth  = $width * $scale;
  $newHeight = $height * $scale;
  
	$src = imagecreatefromjpeg($file);
	$newWidth = 600;

	$newHeight = ($height / $width) * $newWidth;
	$tmp = imagecreatetruecolor($newWidth, $newHeight);
	imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

  // replace it
	unlink($file);
	imagejpeg($tmp, $file, 90);
}

// PROCESSING ...

if( isset( $_FILES['file'] ) && $_FILES['file']['name'] ) {
  $albumName = trim($_POST['name']);
  if( $albumName == "" ) {
    terminate( I18N::$ALBUMNAME_MISSING );
  }

  if( $_FILES['file']['error'] == 1 ) {
    terminate( I18N::$ARCHIVE_TOO_BIG );
  }

  // confirm start
  start();

  // accept the file
  if( ! file_exists( '/tmp/vbsg' ) ) { mkdir( '/tmp/vbsg' ); }
  $workdir = '/tmp/vbsg/' . md5(time() . rand());
  $file = accept_file( $workdir );
  if( ! file_exists( $file ) ) { terminate( I18N::$FILE_TRANSFER_FAILED ); }

  // unzip the file
  $files = unzip_file( $file );
  if( count( $files ) < 1 ) { terminate( I18N::$NO_FILES_IN_ARCHIVE  ); }

  // login
  $gp = new gPhoto( Config::$googleAccount, Config::$googlePass );

  // create album
  report_progress( I18N::$CREATE_ALBUM . " $albumName", 5 );
  $albumId = $gp->createAlbum( $albumName );

  $photoCount = count($files);
  $photoPct = ( (100 - $total) / $photoCount ) / 2;

  // process all foto's
  for( $i=1; $i<=$photoCount; $i++) {
    $photoFile = $files[$i-1];

    // resize
    report_progress( I18N::$RESIZE_PHOTO . "($i/$photoCount)", $photoPct );
    resize_photo( $photoFile, 800, 600 );
    
    // upload
    report_progress( I18N::$UPLOAD_PHOTO . "($i/$photoCount) ", $photoPct );
    $photoId = $gp->addPhoto( $albumId,  $photoFile );
  }

  print '<script>window.parent.upload_done();</script>';
  flushIt();

  print "<pre>";
  print_r( get_included_files() );
  flushIt();

  exit; 
}
