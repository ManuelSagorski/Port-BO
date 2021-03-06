<?php
namespace bo\components\classes;

use bo\components\classes\helper\DBConnect;
use bo\components\classes\helper\Logger;
use bo\components\classes\helper\Query;

/**
 * Klasse Agency
 * @author Manuel Sagorski
 *
 */
class Agency extends AbstractDBObject
{
    public const TABLE_NAME = "port_bo_agency";
    
    private $id;
    private $project_id;
    private $name;
    private $short;
    private $agencyPortInfo = [];
    
    /**
     * Konstruktor
     *
     * @param array $data
     * @param int $id
     */
    public function __construct($data = null, $id = null){
        if(empty($data)) {
            $this->loadAgencyPortInfo();
        }
        else {
            $this->id       = $id;
            $this->name     = $data['agencyName'];
            $this->short    = $data['agencyShort'];
        }
    }
    
    /**
     * function addAgency()
     *
     * Hinzufügen einer neuen Agentur
     */
    public function addAgency() {
        if($msg = $this->validateNewAgencyInput()) {
            return ["type" => "error", "msg" => $msg];
        }
        else {            
            $this->insertDB(["name" => $this->name, "short" => $this->short]);
            
            Logger::writeLogCreate('agency', 'Neue Agentur angelegt: ' . $this->name);
            return array("type" => "added", "name" => $this->name);
        }
    }

    /**
     * function editAgency()
     *
     * Ändern der Daten einer Agentur
     */
    public function editAgency() {
        if($msg = $this->validateNewAgencyInput()) {
            return ["type" => "error", "msg" => $msg];
        }
        else {
            $this->updateDB(["name" => $this->name, "short" => $this->short], ["id" => $this->id]);
            self::setTS($this->id);
            
            Logger::writeLogCreate('agency', 'Agentur bearbeitet: ' . $this->name);
            return ["type" => "changed"];
        }
    }
    
    /**
     * function getAgentName($id)
     *
     * Liefert den Namen zu einer AgentID
     *
     * @param int $id
     * @return string
     */
    public static function getAgentName($id) {
        $result = (new Query("select"))
            ->table(self::TABLE_NAME)
            ->condition(["id" => $id])
            ->execute();
        
        $row = $result->fetch();
        return $row['name'] ?? '';
    }
    
    /**
     * function getAgentShort($id)
     *
     * Liefert die Abkürzung zu einer AgentID
     *
     * @param int $id
     * @return string
     */
    public static function getAgentShort($id) {
        $result = (new Query("select"))
            ->table(self::TABLE_NAME)
            ->condition(["id" => $id])
            ->execute();
        
        $row = $result->fetch();
        return $row['short'] ?? '';
    }
    
    /**
     * function getAgentID($name)
     *
     * Liefert die ID zu dem Namen eines Agenten
     *
     * @param string $name
     * @return string
     */
    public static function getAgentID($name) {
        $result = (new Query("select"))
            ->table(self::TABLE_NAME)
            ->condition(["name" => $name])
            ->execute();
        
        $row = $result->fetch();
        return $row['id'] ?? '0';
    }
    
    /**
     * static function setTS($id)
     *
     * Timestamp einer Agency wird aktualisiert.
     *
     * @param int $id ID der Agency
     */
    public static function setTS($id) {
        (new Query("update"))
            ->table(self::TABLE_NAME)
            ->values(["ts_erf" => date('Y-m-d H:i:s')])
            ->condition(["id" => $id])
            ->execute();
    }
    
    public static function getLastContactToAgent($agencyID, $portID) {
        $sqlstrg = "select * from port_bo_vesselContact where agent_id = ? and port_id = ? and date<CURDATE() order by date desc limit 1";
        $result = DBConnect::execute($sqlstrg, array($agencyID, $portID));
        $row = $result->fetch();
        return $row['date'] ?? '-';
    }
    
    private function loadAgencyPortInfo() {
        $this->agencyPortInfo = AgencyPortInfo::getMultipleObjects(Array("agency_id" => $this->id), "port_id");
    }
    
    private function validateNewAgencyInput() {
        global $t;
        
        $query = (new Query("select"))
            ->table(self::TABLE_NAME)
            ->condition(["name" => $this->name]);
        if(!empty($this->id)){
            $query->conditionNot(["id" => $this->id]);
        }
        if(($query->execute())->rowCount() > 0) {
            return array("field" => "agencyName", "msg" => $t->_('agent-already-existing'));
        }

        $query = (new Query("select"))
            ->table(self::TABLE_NAME)
            ->condition(["short" => $this->short]);
        if(!empty($this->id)){
            $query->conditionNot(["id" => $this->id]);
        }
        if(($query->execute())->rowCount() > 0) {
            return array("field" => "agencyShort", "msg" => $t->_('agent-short-already-existing'));
        }
    }
    
    /*
     Getter und Setter
     */
    public function getID() {
        return $this->id;
    }
    public function getProjectId() {
        return $this->project_id;
    }
    public function getName() {
        return $this->name;
    }
    public function getShort() {
        return $this->short;
    }
    public function getAgencyPortInfo() {
        return $this->agencyPortInfo;
    }
}

?>