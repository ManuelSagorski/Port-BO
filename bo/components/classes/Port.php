<?php
namespace bo\components\classes;

use bo\components\classes\helper\Logger;
use bo\components\classes\helper\Query;

class Port extends AbstractDBObject
{
    public const TABLE_NAME = "port_bo_port";
    
    private $id;
    private $project_id;
    private $name;
    private $short;
    private $mtLink;
    
    public function __construct($data = null) {
        if(!empty($data)) {
            $this->name = $data['portName'];
            $this->short = $data['portShort'];
            $this->mtLink = $data['portMTLink'];
        }
    }
    
    /*
     * Funktion die einen neuen Hafen anlegt
     */
    public function addPort() {
        $this->insertDB([
            "name" => $this->name,
            "short" => $this->short,
            "mtLink" => $this->mtLink
        ]);
        
        $port = Port::getSingleObjectByCondition(Array("name" => $this->name, "short" => $this->short));
        
        (new Query("insert"))
            ->table(UserToPort::TABLE_NAME)
            ->values([
                "user_id" => $_SESSION['user'], 
                "port_id" => $port->getID()
            ])->execute();
        
        Logger::writeLogCreate('port', 'Neuen Hafen angelegt: ' . $this->name);
    }
    
    /*
     * Aktualisiert einen Hafen in der Datenbank
     */
    public function updatePort($data) {
        $this->updateDB([
            "name" => $data['portName'],
            "short" => $data['portShort'],
            "mtLink" => $data['portMTLink']
        ], ["id" => $this->id]);
    }
    
    /*
     * Static Funktion die den Namen zu einer PortID liefert
     */
    public static function getPortName($id, $project = null) {
        $row = (new Query("select"))
            ->table(self::TABLE_NAME)
            ->condition(["id" => $id])
            ->project($project)
            ->execute()
            ->fetch();

        return $row['name'] ?? '';
    }
    
    /*
     * Static Funktion die die ID zu einem Hafen liefert
     */
    public static function getPortID($name) {
        $row = (new Query("select"))
            ->table(self::TABLE_NAME)
            ->condition(["name" => $name])
            ->execute()
            ->fetch();

        return $row['id'] ?? '0';
    }
    
    /*
     * Liefert alle User zur??ck die in einem bestimmten Hafen t??tig sind
     */
    public static function getUsersForPort($id) {
        return (new Query("select"))
            ->fields("u.*")
            ->table(User::TABLE_NAME, "u")
            ->join(UserToPort::TABLE_NAME, "up", "id", "user_id")
            ->condition(["up.port_id" => $id, "u.inactive" => 0])
            ->fetchAll(User::class);
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
    public function getMtLink() {
        return $this->mtLink;
    }
}

?>