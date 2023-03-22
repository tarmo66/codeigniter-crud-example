<?php

echo '<div class="journeyEdit">';
echo '<div class="journeyEditLeft">';
echo form_open('journey/editJourneyItem');
echo '<table border="0" cellspacing="0" cellpadding="0">';
echo '<tbody>';

echo '<tr><td class="journeyLabel"> <p>'._t('CARID').'</p></td>';
echo '<td class="journeyLabel">';
    echo "<select name='CARID' class='dropDown'>";
        echo "<option value='" . $this->journeyItem[0]['CARID'] . "'>" . $this->journeyItem[0]['CARID'] . "</option>";
        foreach ($this->cars as $key => $value) {
            if ($value != $this->journeyItem[0]['CARID']) {
                echo "<option value='".$value."' ".($this->input->post('CARID')==$value ? 'selected' : ''). ">".$value."</option>";
            }
        }
    echo '</select>';
echo '</td></tr>';

echo '<tr><td> <p>'._t('DATE').'</p></td>';
echo '<td colspan="2"> <p>';
echo '<input id="DATE_START" class="datepickerStart" type="text" name="TRANSDATE" value="'. $this->journeyItem[0]['TRANSDATE'] . '">';
echo '</p></td></tr>';

echo '<tr><td> <p>'._t('DISTANCE').'</p></td>';
echo '<td>';
echo "<select name='DISTANCE' class='dropDown'>";
    echo "<option value='" . $this->journeyItem['SELECTED'] . "'>" . $this->journeyItem['SELECTED'] . "</option>";
    echo "<option value='" . $this->journeyItem['NOTSELECTED'] . "'>" . $this->journeyItem['NOTSELECTED'] . "</option>";
echo '</select>';
echo '</td><td>';
echo '<input class="journeyInputInt" maxlength="5" name="DISTANCESELECTED" value="'. $this->journeyItem['DISTANCE'] . '"> km';
echo '</td></tr>';

echo '<tr><td> <p>'._t('PURPOSEOFJOBTRIP').'</p></td>';
echo '<td colspan="2"> <p>';
echo '<input class="journeyInputString" name="PURPOSEOFJOBTRIP" maxlength="100" value="'. rtrim($this->journeyItem[0]['PURPOSEOFJOBTRIP']) . '">';
echo '</p></td></tr>';

echo '<input type="hidden" name="WEBRECID" value="'.$this->journeyItem[0]['WEBAXRECID'].'"">';

echo '<td align="center" valign="top">&nbsp;</td>';
echo '<td valign="top">';
echo '</td>';

echo '</tbody></table>';

echo form_submit('sisestamine', _t('modify'));
echo form_close();

echo '</div>';
echo '<div class="journeyEditRight">';

echo form_open('journey/deleteJourneyItem',array('id'=>'deletebutton'));
echo '<input type="hidden" name="WEBRECID" value="'.$this->journeyItem[0]['WEBAXRECID'].'"">';
echo '<input type="hidden" name="CARID" value="'.$this->journeyItem[0]['CARID'].'"">';
echo form_submit('kustutamine', ucfirst(_t('delete')));
echo form_close();

echo '</div>';
echo '</div>';
