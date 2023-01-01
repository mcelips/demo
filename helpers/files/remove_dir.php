<?php

function remove_dir($dir)
{
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        (is_dir("$dir/$file") && ! is_link("$dir/$file")) ? remove_dir("$dir/$file") : unlink("$dir/$file");
    }

    return rmdir($dir);
}
