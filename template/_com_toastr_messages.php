<?php
$message = render_get_message(false);
if (empty($message) === false) {
    echo "<script>\n";
    foreach ($message as $type => $item) {
        foreach ($item as $text) {
            echo "toastr." . $type . "('$text')\n";
        }
    }
    echo "</script>\n";
}
