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


$result = accounts::findAll();
echo "<table style='border: solid 1px black;'>";
foreach ($result as $record){
    echo "<tr>";
    foreach( $record as $col){
        echo "<td style='width:150px;border:1px solid black;'>".$col."</td>";
    }
    echo "</tr>";
}
echo "</table>";





class model
{
   // protected $tableName;
    public function runQuery($sql){
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $statement->execute();
        echo 'Data inserted </br>';
    }


    public function insert($fieldList,$valueList)
    {
        $fields = '('.implode(',', $fieldList) .')';
        $values= "'" . implode("','", $valueList) . "'";
        $sql = 'insert into '.static::$tableName. $fields.' values ('.$values.");";
        echo $sql;
        $this->runQuery($sql);

    }

    public function update($data,$id)
    {
        $cols = array();

        foreach($data as $key=>$val) {
            $cols[] = "$key = '$val'";
        }
        //$sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE $where";

        $sql = 'update '.static::$tableName.' set ' . implode(', ', $cols) . " where id =" .$id;

        echo $sql;
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

    public function dataList_to_insert(){

        $valueList = array('15','cs214@njit.edu','15','2017-11-09','2017-11-26','Hello4','1');
        $fieldList = array('id','owneremail','ownerid','createddate','duedate','message','isdone');
        $this->insert($fieldList,$valueList);
    }

    public function dataList_to_update($id){

        $data = array("ownerid"=> "10", "message"=> "Update1");
        $this->$id = $id;
        //$valueList = array('13','bb21@njit.edu','13','2017-11-09','2017-11-26','Hello2','0');
        //$fieldList = array('id','owneremail','ownerid','createddate','duedate','message','isdone');
        $this->update($data,$id);
    }

    

}

$input = new todo();
$input->dataList_to_insert();
$input->dataList_to_update(11);


