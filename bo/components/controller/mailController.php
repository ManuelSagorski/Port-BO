<?php
namespace bo\components\controller;

use bo\components\classes\helper\imap;

include '../config.php';

$imap = new imap();

$imap->getMails();

?>