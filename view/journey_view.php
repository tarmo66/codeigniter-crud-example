<?php

echo '<p><h2>' .ucfirst(_t('journey-list')) . '</h2></p>';
echo '<div class="journeySections">';
echo '<div class="journeyLeft">';
echo form_open('journey/insertJourney');
echo '<table border="0" cellspacing="0" cellpadding="0">';
echo '<tbody>';

echo '<tr><td class="journeyLabel"> <p>'._t('CARID').'</p></td>';
echo '<td class="journeyLabel">';
    echo "<select name='CARID' class='dropDown' id='test'>";
        if (isset($this->carid) && in_array($this->carid, $this->cars)) {
            echo "<option value='".$this->carid."' ".($this->input->post('CARID')==$this->carid ? 'selected' : ''). "'>".$this->carid."</option>";
        }
        foreach ($this->cars as $key => $value) {
            if ($value != $this->carid) {
            echo "<option value='".$value."' ".($this->input->post('CARID')==$value ? 'selected' : ''). "'>".$value."</option>";
            }
        }
    echo '</select>';
echo '</td></tr>';

echo '<tr><td> <p>'._t('DATE').'</p></td>';
echo '<td colspan="2"> <p>';
echo '<input id="DATE_SELECTION" class="datepickerStart" type="text" name="DATE_SELECTION" value="">';
echo '</p></td></tr>';

echo '<tr><td> <p>'._t('DISTANCE').'</p></td>';
echo '<td>';
echo "<select name='DISTANCE' class='dropDown'>";
    echo "<option value='0'>" . ucfirst(_t('JOBDISTANCE')) . "</option>";
    echo "<option value='1'>" . _t('PRIVATEDISTANCE') . "</option>";
echo '</select>';
echo '</td><td>';
echo '<input class="journeyInputInt" maxlength="5" name="DISTANCESELECTED" value="'. $this->input->post('DISTANCESELECTED') . '"> km';
echo '</td></tr>';

echo '<tr><td> <p>'._t('PURPOSEOFJOBTRIP').'</p></td>';
echo '<td colspan="2"> <p>';
echo '<input class="journeyInputString" name="PURPOSEOFJOBTRIP"  maxlength="100" value="'. $this->input->post('PURPOSEOFJOBTRIP') . '">';
echo '</p></td></tr>';

echo '<td align="center" valign="top">&nbsp;</td>';
echo '<td valign="top">';
echo '</td>';

echo '</tbody></table>';

echo form_submit('sisestamine', ucfirst(_t('submit')));
echo form_close();

echo '<br>';
echo _t('FUELCONSUMPTION') . ': ' . $this->fuelConsumption;
echo '</div>';

echo '<div class="journeyCenter">';
if(count($this->myEmployees) >= 2 ) {
echo '<table border="0" cellspacing="0" cellpadding="0">';
    foreach ($this->myEmployees as $kk => $vv) {
        if ($vv['EMPLID'] != $this->empl['EMPLID']) {
            $carEmpl = array_column($this->Andmeladu_model->getCarID($vv['EMPLID']), 'CARID');
            foreach ($carEmpl as $key => $value) {
            echo "<tr>";
            echo "<td>" . $vv['NAME'] . "</td>";
            echo "<td><a href='journey/journey?carid=" . $value . "'>" . $value . "</a></td>";
            echo "</tr>";
            }
        }
    }
echo '</table>';
}
echo '</div>';

echo '<div class="journeyRight">';

echo '<div>';
echo form_open('journey/journey');
echo '<table border="0" cellspacing="0" cellpadding="0">';
echo '<tbody>';

echo '<tr><td> <p>' . _t('FROMDATE') . '</p></td>';
echo '<td> <p>';
echo '<input id="JOURNEY_DATE_START" class="datepickerStart" type="text" name="JOURNEY_DATE_START" value="' . $this->startdate . '">';
echo '</p></td></tr>';

echo '<tr><td> <p>' . _t('TODATE') . '</p></td>';
echo '<td> <p>';
echo '<input id="JOURNEY_DATE_END" class="datepickerStart" type="text" name="JOURNEY_DATE_END" value="' . $this->enddate  . '">';
echo '</p></td></tr>';
echo '</tbody></table>';
echo '<br>';
echo form_submit('date', ucfirst(_t('select-period')));
echo form_close();
echo '</div>';

echo '<div>';
echo form_open('journey/journey?clearperiod=1');
echo form_submit('cancel', ucfirst(_t('cancel')));
echo form_close();
echo '</div>';
echo '</div>';

echo '<div id="div1">';
$this->load->view("journey/journeyTable_view");
echo '</div>';
