<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Andmeladu_model extends MY_Model{

    public function __construct()
    {
        parent::__construct();
    }

    function getCarID($EMPLID)
    {
        $this->db->select('*');
        $this->db->from('MECRO_DW.dbo.a_MecEmplFuelCar');
        $this->db->where('EMPLID', $EMPLID);
        $this->db->limit(100);

        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

      function getCarData($CARID)
    {
        $this->db->select('*');
        $this->db->from('MECRO_DW.dbo.a_MecEmplFuelCar');
        $this->db->where('CARID', $CARID);
        $this->db->limit(100);

        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    function insertJourney($attr)
    {
        $this->insertTableRow('WEB.dbo.sync_MecJourneyList', $attr);
        $this->db->limit(1);
    }

    function getCarJourneyList($CARID, $DATAAREAID=null, $STARTDATE, $ENDDATE)
    {
        $this->db->select("EMPLID, TRANSDATE, JOBDISTANCE, PRIVATEDISTANCE, PURPOSEOFJOBTRIP, CARID, WEBAXRECID");
        $this->db->from('WEB.dbo.sync_MecJourneyList');
        $this->db->where('CARID', $CARID);
        $this->db->where("TRANSDATE!=",'1900-01-01'); 
        if( isset($DATAAREAID) ){
            $this->db->where("DATAAREAID",strtolower($DATAAREAID));
        }
        $this->db->where("TRANSDATE BETWEEN '$STARTDATE' AND '$ENDDATE'");
        $this->db->limit("100000");
        $this->db->order_by('TRANSDATE DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    function viewJourneyItem($WEBRECID)
    {
        $this->db->select("EMPLID, TRANSDATE, JOBDISTANCE, PRIVATEDISTANCE, PURPOSEOFJOBTRIP, CARID, WEBAXRECID");
        $this->db->from('WEB.dbo.sync_MecJourneyList');
        $this->db->where('WEBAXRECID', $WEBRECID);
        $this->db->limit(100);
        $this->db->order_by('TRANSDATE DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    function editJourneyItem($WEBRECID, $attr)
    {
        $this->db->where('WEBAXRECID', $WEBRECID);
        $this->db->update('WEB.dbo.sync_MecJourneyList', $attr);
        $this->db->limit(100);
        return;
    }

    function deleteJourneyItem($WEBRECID)
    {
        $this->db->where('WEBAXRECID', $WEBRECID);
        $this->db->delete('WEB.dbo.sync_MecJourneyList');
        $this->db->limit(1);
        return;
    }

    function getFuelConsumption($CARID, $DATAAREAID=null, $STARTDATE, $ENDDATE)
    {
        $this->db->select("EXCLUDED, EXCLUDEDTXT, qty, transDate, fuelType, lineAmount, station, lineId, DATAAREAID");
        $this->db->from('WEB.dbo.web_gasTrans');
        $this->db->where('CARID', $CARID);
        $this->db->where("transDate BETWEEN '$STARTDATE' AND '$ENDDATE'");
        if( in_array(strtoupper($DATAAREAID), array('SET','AET') ) ){
            $this->db->where_in("DATAAREAID",array('SET','AET')); 
        }else{
            $this->db->where("DATAAREAID",strtoupper($DATAAREAID)); 
        }
        $this->db->limit("100000");
        $this->db->order_by('transDate DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    function viewFuelItem($LINEID)
    {
        $this->db->select("EXCLUDED, EXCLUDEDTXT, qty, transDate, fuelType, lineAmount, station, lineId, CARID");
        $this->db->from('WEB.dbo.web_gasTrans');
        $this->db->where('lineId', $LINEID);
        $this->db->limit(1);
        $this->db->order_by('TRANSDATE DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    function editFuelItem($LINEID, $attr)
    {
        $this->db->where('lineId', $LINEID);
        $this->db->update('WEB.dbo.web_gasTrans', $attr);
        $this->db->limit(1);
        return;
    }
}
?>