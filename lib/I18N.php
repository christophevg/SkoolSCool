<?php

/**
 * Static class that contains a set of messages send out by the system.
 * These can be modified to allow for translation into a desired language.
 */

class I18N {
  public static $FAILED_LOGON = "Log on failed.";
  public static $UNKNOWN_FEDERATED_LOGIN = "Unknown Federated Login.";

  public static $UNKNOWN_CONTENT_TYPE = "Unknown Content Type.";
  
  public static $ARCHIVE_TOO_BIG   = "Uploaded archive is too big.";

  public static $FILENAME_MISSING = "File name is missing...";

  public static $ALBUMNAME_MISSING = "Album name is missing...";
  public static $CREATE_ALBUM = "Creating album...";
  public static $RESIZE_PHOTO = "Resizing photo";
  public static $UPLOAD_PHOTO = "Uploading photo";
  public static $NO_FILES_IN_ARCHIVE = "No files in archive";

  public static $FILE_TRANSFER_FAILED = "File transfer failed.";
  
  public static $RECAPTCHA_FAILURE = "The recaptcha test failed. Please try again.";
  public static $CONTACT_SUCCESS   = "We've successfully received your messages.";
}
