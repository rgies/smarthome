<!DOCTYPE html>
<!-- Smarthome Copyright © 2014 by Robert Gies -->
<html>
<head>
    <title>Smarthome</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png" />
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/smarthome.css" rel="stylesheet" />
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.10.2.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Include smarthome Javascript -->
    <script src="js/smarthome.js"></script></head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">
                <span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;Smarthome
            </a>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="panel-body"><?php echo $contents; ?></div>

    <p style="text-align: center;color: #777777"><small>Copyright © 2014 - Robert Gies</small></p>

    <!-- Preload loader image -->
    <script>$('<img src="images/ajax-loader.gif"/>');</script>
</body>
</html>