<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Cache-Control, Pragma, Origin, Authorization, Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: GET, PUT, POST, OPTIONS');
/**
 * Description of Index
 *
 * @author Jose Alfredo Rodriguez <alfredo@jarscr.com>
 * @copyright  JARS Costa Rica
 */


require_once "RHApi.php";
$RHApi = new RHApi();
$RHApi->API();
