<?php

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

<!-- Alert refresh script -->
<script><?php echo Lib_Core_HtmlHelper::renderAlertRefreshJs();?></script>
<!-- Panel refresh script -->
<script><?php echo Lib_Core_HtmlHelper::renderPanelRefreshJs($panels);?></script>
