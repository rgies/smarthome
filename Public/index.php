<?php
    // Init application
    require_once '../Lib/Core/App.php';
    $app = new Lib_Core_App('../');

    echo $app->render();
