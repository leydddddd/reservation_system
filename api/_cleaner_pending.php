<?php
// filepath: c:\Users\barak\Documents\AKUKODING\PROKON\bckup\web\api\cleanup_holds.php
require_once 'connection.php';

function cleanupExpiredHolds($ineedthis)
{
    $sql = "UPDATE bookings SET status='cancelled' WHERE status='held' AND expires_at < NOW()";
    $ineedthis->query($sql);
}

cleanupExpiredHolds($ineedthis);
