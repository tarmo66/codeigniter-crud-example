<?php

echo '<table border="0" cellspacing="0" cellpadding="0" class="journeyTable">';

echo "<tr>";
    echo "<th>" . _t('CARID') . "</th>";
    echo "<th>" . _t('DATE') . "</th>";
    echo "<th>" . ucfirst(_t('JOBDISTANCE')) . "</th>";
    echo "<th>" . _t('PRIVATEDISTANCE') . "</th>";
    echo "<th>" . _t('PURPOSEOFJOBTRIP') . "</th>";
    echo "<th>" . _t('modify') . "</th>";
echo "</tr>";


if (isset($this->journeyList) && count($this->journeyList) > 0) {
    foreach ($this->journeyList as $key => $vv) {

        if ($vv['CARID'] == 'Fuel') {
            echo "<tr class='journeyFuelLine'>";
        }else{
           echo "<tr>"; 
        }
            echo "<td class='journeyColumn'>";
            if (isset($vv['CARID'])) echo $vv['CARID'];
            echo "</td>";
            echo "<td class='journeyColumn'>";
            if (isset($vv['TRANSDATE'])) echo date("d.m.Y", strtotime($vv['TRANSDATE']));
            echo "</td>";
            echo "<td class='journeyColumn'>";
            if (isset($vv['JOBDISTANCE'])) echo $vv['JOBDISTANCE'];
            echo "</td>";
            echo "<td class='journeyColumn'>";
            if (isset($vv['PRIVATEDISTANCE'])) echo $vv['PRIVATEDISTANCE'];
            echo "</td>";
            echo "<td>";
            if (isset($vv['PURPOSEOFJOBTRIP'])) echo $vv['PURPOSEOFJOBTRIP'];
            echo "</td>";
            echo "<td class='journeyColumn'>";
            if (isset($vv['WEBAXRECID']) && $vv['LOCK'] == '0') {echo "<a href='/journey/viewJourneyItem/" . $vv['WEBAXRECID'] . "' >" . $vv['WEBAXRECID'] . "</a>";}
            echo "</td>";
        echo "</tr>";
    }
}

echo '</table>';