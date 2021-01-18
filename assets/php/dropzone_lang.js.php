<?php
/*
                            _
   ____                    | |
  / __ \__      _____  _ __| | __ ____
 / / _` \ \ /\ / / _ \| '__| |/ /|_  /
| | (_| |\ V  V / (_) | |  |   <  / /
 \ \__,_| \_/\_/ \___/|_|  |_|\_\/___|
  \____/

        http://www.atworkz.de
           info@atworkz.de
_______________________________________

       Screenly OSE Monitoring
       Dropzone Language File
_______________________________________
*/

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');

header('Content-Type: application/javascript');
echo '
Dropzone.prototype.defaultOptions.dictDefaultMessage            = "'.Translation::of('dropzone.dictDefaultMessage').'";
Dropzone.prototype.defaultOptions.dictFallbackMessage           = "'.Translation::of('dropzone.dictFallbackMessage').'";
Dropzone.prototype.defaultOptions.dictFallbackText              = "'.Translation::of('dropzone.dictFallbackText').'";
Dropzone.prototype.defaultOptions.dictFileTooBig                = "'.Translation::of('dropzone.dictFileTooBig').'";
Dropzone.prototype.defaultOptions.dictInvalidFileType           = "'.Translation::of('dropzone.dictInvalidFileType').'";
Dropzone.prototype.defaultOptions.dictResponseError             = "'.Translation::of('dropzone.dictResponseError').'";
Dropzone.prototype.defaultOptions.dictCancelUpload              = "'.Translation::of('dropzone.dictCancelUpload').'";
Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation  = "'.Translation::of('dropzone.dictCancelUploadConfirmation').'";
Dropzone.prototype.defaultOptions.dictRemoveFile                = "'.Translation::of('dropzone.dictRemoveFile').'";
Dropzone.prototype.defaultOptions.dictMaxFilesExceeded          = "'.Translation::of('dropzone.dictMaxFilesExceeded').'";
';
