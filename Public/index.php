<!DOCTYPE html>
<!-- Smarthome Copyright © 2014 by Robert Gies -->
<html>
<head>
    <title>Smarthome</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="images/favicon.ico" />
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png" />
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/smarthome.css" rel="stylesheet" />
</head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">
                <span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;Smarthome
            </a>
        </div>
    </nav>

    <div class="panel-body">

    <?php
    // Init application
    require_once '../Config/Init.php';

    // Load config
    $config = new Lib_Core_Config();

    // Check connection
    if ($config->getHost())
    {
        $hm = new Lib_Core_Homematic();
        $err = $hm->checkConnection();
        if ($err !== true)
        {
            echo '<div class="alert alert-danger">' . $err . '</div>';
        }
    }

    // Alerts
    echo '<div id="alertBody">';
    echo Lib_Core_HtmlHelper::renderAlerts($config->getAlerts());
    echo '</div>';

    // Draw grid with panels
    $id = 0;
    $rowNr = 0;
    $panels = array();
    $grid = $config->getGrid();

    foreach ($grid->children() as $row)
    {
        $colNr = 0;
        $rowNr++;
        echo '<div class="row">';

        if ($row->children()->count())
        {
            $colWidth = 12 / $row->children()->count();
            foreach ($row->children() as $panel)
            {
                $id++;
                $colNr++;
                $panelBodyId = 'panelBody' . $id;
                $panels[$panelBodyId] = array($rowNr, $colNr, $panel);
                $collapsed = (isset($panel['collapsed']) && $panel['collapsed']=='1') ? '' : ' in';

                echo '<div class="col-sm-' . $colWidth . '">';
                echo '<div class="panel panel-default">';

                echo '<div class="panel-heading">';
                echo '<h4 class="panel-title">';
                echo '<a data-toggle="collapse" data-parent="#accordion" onclick="sm_updatePanelBody(' . $id
                    . ',' . $rowNr . ',' . $colNr . ')" href="#collapse' . $id . '">';
                echo htmlentities($panel['title'], ENT_QUOTES, 'UTF-8');
                echo '</a>';
                echo '</h4>';
                echo '</div>';

                echo '<div id="collapse' . $id . '" class="panel-collapse collapse' . $collapsed . '">';
                echo '<div id="' . $panelBodyId . '" class="panel-body">';

                // Render panel
                if ($collapsed != '')
                {
                    echo Lib_Core_HtmlHelper::renderPanel($panel);
                }

                echo '</div>';
                echo '</div>';

                echo '</div>';
                echo '</div>';
            }
        }
        echo '</div>';
    }
    ?>

    </div>

    <p style="text-align: center;color: #777777"><small>Copyright © 2014 - Robert Gies</small></p>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.10.2.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Include smarthome Javascript -->
    <script src="js/smarthome.js"></script>
    <!-- Alert refresh script -->
    <script language="JavaScript"><?php echo Lib_Core_HtmlHelper::renderAlertRefreshJs();?></script>
    <!-- Panel refresh script -->
    <script language="JavaScript"><?php echo Lib_Core_HtmlHelper::renderPanelRefreshJs($panels);?></script>
    <!-- Preload loader image -->
    <script>$('<img src="images/ajax-loader.gif"/>');</script>
</body>
</html>