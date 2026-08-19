<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Imagick Test</h2>";

if (extension_loaded('imagick')) {

    echo "Imagick is installed/enabled ✅<br>";

    $imagick = new Imagick();

    echo "Imagick object created successfully ✅";

} else {

    echo "Imagick is NOT installed/enabled ❌";

}