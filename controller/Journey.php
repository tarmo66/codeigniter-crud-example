<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Journey extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Andmeladu_model');
        $this->emplCheck();

        if(!empty($this->session->user['employee']['EMPLID'])) {
            $this->empl['EMPLID'] = $this->session->user['employee']['EMPLID'];
            
            $this->myEmployees = $this->andmeladu_model->emplEmployees(array('EMPLID' => $this->empl['EMPLID']));
            
            $this->emplCar = array();
            foreach ($this->myEmployees as $kk => $vv) {
                $emplCars = array_column($this->andmeladu_model->getCarID($vv['EMPLID']), 'CARID');
                foreach ($emplCars as $key => $value) array_push($this->emplCar, $value);
            }

            $car = array_column($this->andmeladu_model->getCarID($this->empl['EMPLID']), 'CARID');
            $this->cars = array_filter($car, 'strlen');

        } else {
            redirect('/journey/journey', 'refresh');
        }
    }

    public function journey()
    {
    $this->benchmark->mark('s1_start');

        $this->carid = $this->cars[0];
        if (isset($_GET['carid'])) {
            $this->carid = $_GET['carid'];
        }

        $this->carData = $this->andmeladu_model->getCarData($this->carid);
        if (empty($this->carData[0]['MECCARTYPE'])) {
            $carType = 1;
        } else {
            $carType = $this->carData[0]['MECCARTYPE'];
        }

        if (!empty($this->input->post('JOURNEY_DATE_START'))) {
            $_SESSION['user']['JOURNEYDATE1'] = $this->input->post('JOURNEY_DATE_START');
            $this->startdate = $_SESSION['user']['JOURNEYDATE1'];
        } else {
            $this->startdate = date("d.m.Y", strtotime("-6 months"));
        }

        if (!empty($this->input->post('JOURNEY_DATE_END'))) {
            $_SESSION['user']['JOURNEYDATE2'] = $this->input->post('JOURNEY_DATE_END');
            $this->enddate = $_SESSION['user']['JOURNEYDATE2'];
        } else {
            $this->enddate = date('d.m.Y');
        }

        if (!empty($_SESSION['user']['JOURNEYDATE1'])) {
            $this->startdate = $_SESSION['user']['JOURNEYDATE1'];
        }

        if (!empty($_SESSION['user']['JOURNEYDATE2'])) {
            $this->enddate = $_SESSION['user']['JOURNEYDATE2'];
        }

        if (isset($_GET['clearperiod']) && $_GET['clearperiod'] == '1') {
            unset($_SESSION['user']['JOURNEYDATE1']);
            $this->startdate = date("d.m.Y", strtotime("-6 months"));
            unset($_SESSION['user']['JOURNEYDATE2']);
            $this->enddate = date('d.m.Y');
        }

        $this->carFuel = $this->andmeladu_model->getFuelConsumption($this->carid, $this->DATAAREAID, $this->startdate, $this->enddate);

        $arrlength2 = count($this->carFuel);

        $fuelQty = array_sum(array_column($this->carFuel,'qty'));

        $this->checkCar($this->carid);

        $this->journeyList = $this->andmeladu_model->getCarJourneyList($this->carid, $this->DATAAREAID, $this->startdate, $this->enddate);

        $jobMilage = array_sum(array_column($this->journeyList,'JOBDISTANCE'));
        $privateMilage = array_sum(array_column($this->journeyList,'PRIVATEDISTANCE'));
        $milage = $jobMilage + $privateMilage;

        if ($milage == '0') $milage = 1;
        $this->fuelConsumption = number_format($fuelQty * 100 / $milage,2,",",".") ;

        foreach ($this->carFuel as $k => $v) {
            $a=array();
            $edit = 'Exclude';
            if ($v['EXCLUDED'] == '1') {
                $a['JOBDISTANCE'] = 'Excluded';
                $a['PRIVATEDISTANCE'] = $v['EXCLUDEDTXT'];
                $edit = 'Include';
            }
            $a['CARID'] = 'Fuel';

            $a['TRANSDATE'] = $v['transDate'];

            $a['PURPOSEOFJOBTRIP'] = round($v['qty'],2) . " " . _t('UNIT.L') . ", " . $v['fuelType'] . ", " . $v['station'] . ", " ._t('common.price') . ": " . round($v['lineAmount'],2);
            $a['WEBAXRECID'] = '<a href="/journey/viewFuelLine/' . $v['lineId'] . '">' . $edit . '</a>';

            $this->journeyList[]=$a;
        }


        function date_compare($element1, $element2) {
            $datetime1 = strtotime($element1['TRANSDATE']);
            $datetime2 = strtotime($element2['TRANSDATE']);
            return $datetime2 - $datetime1;
        } 

        usort($this->journeyList, 'date_compare');

        $arrlength2 = count($this->journeyList);
        for ($i=0; $i < $arrlength2; $i++) {
            $now = time();
            $your_date = strtotime($this->journeyList[$i]['TRANSDATE']);
            $datediff = $now - $your_date;
            $datediffint = round($datediff / (60 * 60 * 24));
            if ($datediffint > 7) {
                $this->journeyList[$i]['LOCK'] = '1';
            } else {
                $this->journeyList[$i]['LOCK'] = '0';
            }
        }

        $this->showSidebar = 0;
        $this->views[] = 'journey/journey';
        $this->template($this->views);
    }

    public function insertJourney()
    {
        if (is_null($this->input->post('CARID'))) {
            redirect('/journey/journey', 'refresh');
        }
        $this->checkCar($this->input->post('CARID'));

        if ($this->input->post('DISTANCE') == '0') {
            $this->jobdistance = $this->input->post('DISTANCESELECTED');
            $this->privatedistance = 0;
        }
        if ($this->input->post('DISTANCE') == '1') {
            $this->privatedistance = $this->input->post('DISTANCESELECTED');
            $this->jobdistance = 0;
        }

        if (!is_numeric($this->input->post('DISTANCESELECTED'))) {
            $this->privatedistance = 0;
            $this->jobdistance = 0;
        }

        if ($this->input->post('DISTANCESELECTED') == '') {
            $this->privatedistance = 0;
            $this->jobdistance = 0;
        }

        if ($this->input->post('PURPOSEOFJOBTRIP') == '') {
            $this->purpose = '-';
        }

        if (!empty($this->input->post('PURPOSEOFJOBTRIP'))) {
            $this->purpose = $this->input->post('PURPOSEOFJOBTRIP');
        }

        if ($_POST['DATE_SELECTION'] == '') {
            $this->date = date("Y-m-d");
        }

        if ($_POST['DATE_SELECTION'] > 0) {
            $this->date = $_POST['DATE_SELECTION'] ;
        }
        
        $attr = array(
            'CARID' => $this->input->post('CARID'),
            'EMPLID' => $this->empl['EMPLID'],
            'TRANSDATE' => $this->date,
            'JOBDISTANCE' => $this->jobdistance,
            'PRIVATEDISTANCE' => $this->privatedistance,
            'PURPOSEOFJOBTRIP' => $this->purpose,
            'IMPORTED'=> 0,
            'DATAAREAID'=>strtolower($this->DATAAREAID),
            'created'=> date('Y-m-d H:i:s')
        );

        $this->journeyItem = $this->andmeladu_model->insertJourney($attr);
        redirect('/journey/journey?carid=' . $this->input->post('CARID'), 'refresh');
    }

    public function viewJourneyItem($WEBRECID = null)
    {
        if (is_null($WEBRECID)) {
        redirect('/journey/journey', 'refresh');
        }
        $car = array_column($this->andmeladu_model->getCarID($this->empl['EMPLID']), 'CARID');
        $this->cars = array_filter($car, 'strlen');
        $this->journeyItem = $this->andmeladu_model->viewJourneyItem($WEBRECID);

        $this->checkCar($this->journeyItem[0]['CARID']);

        $this->carData = $this->andmeladu_model->getCarData($this->journeyItem[0]['CARID']);
        $carType = $this->carData[0]['MECCARTYPE'];

        if ($this->journeyItem[0]['JOBDISTANCE'] > 0) {
            $this->journeyItem['DISTANCE'] = $this->journeyItem[0]['JOBDISTANCE'];
            $this->journeyItem['SELECTED'] = ucfirst(_t('JOBDISTANCE'));
            $this->journeyItem['NOTSELECTED'] = _t('PRIVATEDISTANCE');
        }
        if ($this->journeyItem[0]['PRIVATEDISTANCE'] > 0) {
            $this->journeyItem['DISTANCE'] = $this->journeyItem[0]['PRIVATEDISTANCE'];
            $this->journeyItem['SELECTED'] = _t('PRIVATEDISTANCE');
            $this->journeyItem['NOTSELECTED'] = ucfirst(_t('JOBDISTANCE'));
        }

        if ($this->journeyItem[0]['JOBDISTANCE'] == '0' && $this->journeyItem[0]['PRIVATEDISTANCE'] == '0') {
           $this->journeyItem['SELECTED'] = ucfirst(_t('JOBDISTANCE'));
           $this->journeyItem['NOTSELECTED'] = _t('PRIVATEDISTANCE');
           $this->journeyItem['DISTANCE'] = '0';
        }

        $this->showSidebar = 0;
        $this->views[] = 'journey/journeyEdit';
        $this->template($this->views);
    }

    public function editJourneyItem()
    {
        if (empty($_POST['WEBRECID'])) {
            redirect('/journey/journey', 'refresh');
        }
        $this->checkCar($this->input->post('CARID'));

        if ($this->input->post('DISTANCE') == ucfirst(_t('JOBDISTANCE'))) {
            $this->jobdistance = $_POST['DISTANCESELECTED'];
            $this->privatedistance = '0';
        }
        if ($this->input->post('DISTANCE') == ucfirst(_t('PRIVATEDISTANCE'))) {
            $this->jobdistance = '0';
            $this->privatedistance = $_POST['DISTANCESELECTED'];
        }

        if (!is_numeric($_POST['DISTANCESELECTED'])) {
            $this->privatedistance = '0';
            $this->jobdistance = '0';
        }

        if ($_POST['DISTANCESELECTED'] == '') {
            $this->privatedistance = '0';
            $this->jobdistance = '0';
        }

        if ($this->input->post('PURPOSEOFJOBTRIP') == '') {
            $this->purpose = '-';
        }

        if (!empty($this->input->post('PURPOSEOFJOBTRIP'))) {
            $this->purpose = $this->input->post('PURPOSEOFJOBTRIP');
        }

        if ($this->input->post('TRANSDATE') == '') {
            $this->date = date("Y-m-d");
        }

        if ($this->input->post('TRANSDATE') > 0) {
            $this->date = $this->input->post('TRANSDATE') ;
        }


        $this->WEBRECID = $_POST['WEBRECID'];
            $attr = array(
            'CARID' => $_POST['CARID'],
            'TRANSDATE' => $_POST['TRANSDATE'],
            'JOBDISTANCE' => $this->jobdistance,
            'PRIVATEDISTANCE' => $this->privatedistance,
            'PURPOSEOFJOBTRIP' => $_POST['PURPOSEOFJOBTRIP']
        );
        
        if(!empty($this->empl['EMPLID'])) {
            $this->journeyItem = $this->andmeladu_model->editJourneyItem($this->WEBRECID, $attr);
        }

        redirect('/journey/journey?carid=' . $_POST['CARID'], 'refresh');

    }


    public function deleteJourneyItem()
    {
        if (empty($_POST['WEBRECID'])) {
            redirect('/journey/journey', 'refresh');
        }
        $this->checkCar($this->input->post('CARID'));
        if(!empty($this->empl['EMPLID'])) {
            $this->journeyDelete = $this->andmeladu_model->deleteJourneyItem($_POST['WEBRECID']);
            redirect('/journey/journey?carid=' . $_POST['CARID'], 'refresh');
        }
    }

    public function viewFuelLine($LINEID = null)
    {
        if (empty($LINEID)) {
            redirect('/journey/journey', 'refresh');
        }

        if(!empty($this->empl['EMPLID'])) {
            $LINEID = str_replace("%20", " ", $LINEID);
            $this->fuelItem = $this->andmeladu_model->viewFuelItem($LINEID);
        }

        $this->showSidebar = 0;
        $this->views[] = 'journey/fuelLineEdit';
        $this->template($this->views);
    }

    public function editFuelLine()
    {
        if (empty($_POST['LINEID'])) {
            redirect('/journey/journey', 'refresh');
        }
        if(!empty($this->empl['EMPLID'])) {
            $this->LINEID = $_POST['LINEID'];
            if (strlen($_POST['EXCLUDEDTXT']) > 0) {
                $text = $_POST['EXCLUDEDTXT'];
                $boolean = '1';
            }
            if (strlen($_POST['EXCLUDEDTXT']) == 0) {
                $text = NULL;
                $boolean = '0';
            }
            $attr = array(
            'EXCLUDED' => $boolean,
            'EXCLUDEDTXT' => $text
            );
      
            $this->fuelItem = $this->andmeladu_model->editFuelItem($this->LINEID, $attr);
        }
        redirect('/journey/journey?carid=' . $_POST['CARID'], 'refresh');
    }

    public function checkCar($CARID=0)
    {
        if ( !(in_array($CARID, $this->cars) or in_array($CARID, $this->emplCar)) ) redirect('/journey/journey', 'refresh');
    }

}