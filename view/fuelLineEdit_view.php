<?php


echo '<div class="journeyEdit">';
echo '<div class="journeyEditLeft">';
echo form_open('journey/editFuelLine');
echo '<table border="0" cellspacing="0" cellpadding="0">';
echo '<tbody>';

echo '<tr><td> <p>'._t('Data').'</p></td>';
echo '<td> <p>';
echo $this->fuelItem[0]['qty'] . ', ' . $this->fuelItem[0]['qty'] . ', ' . $this->fuelItem[0]['fuelType'] . ', ' . $this->fuelItem[0]['station'] . ', ' . $this->fuelItem[0]['lineAmount'];
echo '</p></td></tr>';

echo '<tr><td> <p>'._t('Description').'</p></td>';
echo '<td> <p>';
echo '<input class="journeyInputString" name="EXCLUDEDTXT" maxlength="100" value="'. rtrim($this->fuelItem[0]['EXCLUDEDTXT']) . '">';
echo '</p></td></tr>';

echo '<input type="hidden" name="LINEID" value="'.$this->fuelItem[0]['lineId'].'"">';
echo '<input type="hidden" name="CARID" value="'.$this->fuelItem[0]['CARID'].'"">';

echo '<td align="center" valign="top">&nbsp;</td>';
echo '<td valign="top">';
echo '</td>';

echo '</tbody></table>';

echo form_submit('sisestamine', _t('modify'));
echo form_close();
echo '</div>';
