<?php

class gPhoto {
  private $user;
  private $client;
  private $gp;

  function __construct($user, $pass) {
    $path = dirname(__FILE__);
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    
    require_once 'Zend/Loader.php';

    Zend_Loader::loadClass('Zend_Gdata_Photos');
    Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
    Zend_Loader::loadClass('Zend_Gdata_AuthSub');

    $serviceName = Zend_Gdata_Photos::AUTH_SERVICE_NAME;

    $this->user = $user;

    $this->client = 
      Zend_Gdata_ClientLogin::getHttpClient($this->user, $pass, $serviceName);

    $this->gp = new Zend_Gdata_Photos( $this->client, "VBSG-website-1.0" );
  }

  function createAlbum( $title, $summary = "" ) {
    $entry = new Zend_Gdata_Photos_AlbumEntry();
    $entry->setTitle       ( $this->gp->newTitle  ( $title   ) );
    $entry->setSummary     ( $this->gp->newSummary( $summary ) );
    $entry->setGphotoAccess( $this->gp->newAccess ('public'  ) );
    $album = $this->gp->insertAlbumEntry( $entry );
    return (string)$album->gphotoId;
  }

  function addPhoto( $albumId, $filename, $name = "", $summary = "", $tags = "") {
    if( $name == "" ) { $name = $filename; }
    $username = "default";

    $fd = $this->gp->newMediaFileSource($filename);
    $fd->setContentType("image/jpeg");

    // Create a PhotoEntry
    $photoEntry = $this->gp->newPhotoEntry();
    $photoEntry->setMediaSource($fd);
    $photoEntry->setTitle($this->gp->newTitle($name));
    $photoEntry->setSummary($this->gp->newSummary($summary));

    // add some tags
    $keywords = new Zend_Gdata_Media_Extension_MediaKeywords();
    $keywords->setText($tags);
    $photoEntry->mediaGroup = new Zend_Gdata_Media_Extension_MediaGroup();
    $photoEntry->mediaGroup->keywords = $keywords;

    // We use the AlbumQuery class to generate the URL for the album
    $albumQuery = $this->gp->newAlbumQuery();

    $albumQuery->setUser($this->user);
    $albumQuery->setAlbumId($albumId);

    // We insert the photo, and the server returns the entry representing
    // that photo after it is uploaded
    $photo = $this->gp->insertPhotoEntry($photoEntry, $albumQuery->getQueryUrl());
    return (string)$photo->gphotoId;
  }
}