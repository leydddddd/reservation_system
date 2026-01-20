<?php

$ineedthis = new mysqli("localhost", "root", "", "tekape_workspace");
if ($ineedthis->connect_errno) {
    echo "Failed to connect to MySQL: (" . $ineedthis->connect_errno . ") " . $ineedthis->connect_error;
}
$ineedthis->set_charset("utf8mb4");
