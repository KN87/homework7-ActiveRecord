<?php
/**
 * Created by PhpStorm.
 * User: keka
 * Date: 11/8/2017
 * Time: 3:05 PM
 */
ini_set('display_errors','On');
error_reporting(E_ALL);

define('connection','sql2.njit.edu');
define('username','kn262');
define('password','NvlYN5s5');
define('dbname','kn262');

function tableConst($result){

    echo "<table style='border: solid 1px black;'>";
    foreach ($result as $record){
        echo "<tr>";
        foreach( $record as $col){
            echo "<td style='width:150px;border:1px solid black;'>".$col."</td>";
        }
        echo "</tr>";
    }
    echo "</table>";

}


class dbConn{

    protected static $db;

    private function __construct()
    {
        try{
            self::$db = new PDO('mysql:host='.connection.';dbname='.dbname,username,password);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        }
        catch (PDOException $e){
            echo"Connection Error".$e->getMessage();

        }
    }
    public static function getConnection(){

        if(!self::$db){
            new dbConn();
        }
        return self::$db;
    }
}

class collection{

    static public function findAll(){

        $db = dbConn::getConnection();
        //$tableName = get_called_class();
        $query = 'Select * from '.static::$tableName;
        $stmt = $db->prepare($query);
        $stmt->execute();
        //$class = static::$modelName;
        $stmt ->setFetchMode(PDO::FETCH_ASSOC);
        $recordset = $stmt->fetchAll();
        return $recordset;


    }

    static public function findOne($id){

        $db = dbConn::getConnection();
        $query = 'select * from '.static::$tableName.' where id= '.$id;
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stmt ->setFetchMode(PDO::FETCH_ASSOC);
        $recordset = $stmt->fetchAll();
        return $recordset;


    }

}

class accounts extends collection {

    protected static $tableName = 'accounts';

}

class todos extends collection {

    protected static $tableName = 'todos';
}


$result_all = accounts::findAll();
echo "<h3> Select All Records </h3> <br>";
tableConst($result_all);

$result_one = accounts::findOne(10);
echo "<h3> Select One Record </h3> <br>";
tableConst($result_one);


class model
{

    public function save($flag=null){

        $data = get_object_vars($this);
        if(isset($flag)){

            echo "Here for Insert:: <br>";
            $sql = $this->insert($data);

        }
        else {

            echo "Here for Update:: <br>";
            $sql = $this->update($data);

        }
        $this->runQuery($sql);

    }

    public function runQuery($sql){
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $statement->execute();

    }

    public function insert($data)
    {

        $fieldList = array();
        $fieldList = array_keys($data);

        $valueList = array();
        $valueList = array_values($data);

        $fields = '('.implode(',', $fieldList) .')';
        $values= "'" . implode("','", $valueList) . "'";
        $sql = 'insert into '.static::$tableName. $fields.' values ('.$values.");";
        echo $sql;
        echo "<br>";
        return $sql;
        //$this->runQuery($sql);

    }

    public function update($data)
    {
        $cols = array();

        foreach($data as $key=>$val) {
            $cols[] = "$key = '$val'";
        }

        $sql = 'update '.static::$tableName.' set ' . implode(', ', $cols) . " where id =" .$data['id'];

        echo $sql;
        echo "<br>";
        return $sql;
        //$this->runQuery($sql);

    }

    public function delete(){

        echo "Here for delete:: <br>";
        $sql = 'delete from '.static::$tableName.' where id =' .$this->id;
        $this->runQuery($sql);
    }
}

class todo extends model {

    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    protected static $tableName = 'todos';

    public function __construct($id,$owneremail=null,$ownerid=null,$createddate=null,$duedate=null,$message=null,$isdone=null)
    {
        $this->id = $id;
        $this->owneremail = $owneremail;
        $this->ownerid = $ownerid;
        $this->createddate = $createddate;
        $this->duedate = $duedate;
        $this->message = $message;
        $this->isdone = $isdone;
    }



}

$insertObj = new todo("27","6t8o@njit.edu","27","2017-11-09","2017-11-26","Hello7i","0");
$insertObj->save(1);

$updateObj = new todo("19","mhb@njit.edu","17","2017-11-09","2017-11-26","Hello17","0");
$updateObj->save();

$deleteObj = new todo(14);
$deleteObj->delete();



