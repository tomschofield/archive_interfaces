<?php
// outputs e.g.  somefile.txt was last modified: December 29 2002 22:16:23.

$filename = 'edit1.xml';
if (file_exists($filename)) {
    echo "$filename was last modified: " . date ("F d Y H:i:s.", filemtime($filename));
}
?>