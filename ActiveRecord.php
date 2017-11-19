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

    $header = $result[0];

    $html = '<html>';
    $html .= '<link rel="stylesheet" href="Style.css">';
    $html .= '<table>';
    $html .= '<tr>';
    foreach ($header as $key=>$value){
        $html .= '<th>'.$key.'</th>';
    }
    $html .= '</tr>';
    foreach ($result as $record){
        $html .= '<tr>';
        foreach( $record as $col){
            $html .= '<td>'.$col.'</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';
    print_r($html);



}
class CreateStatusTable{

    function __construct()
    {
        echo "<br> <h3> Status table for Insert/update/delete </h3> <br>";
        $html = '<html>';
        $html .= '<link rel="stylesheet" href="Style.css">';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<th> Sl.No </th>';
        $html .= '<th> Title </th>';
        $html .= '<th> Status </th>';
        $html .= '</tr>';
        print_r($html);


    }

    public static function addRow($seq,$comment,$status){

        $html = '<html>';
        $html .= '<link rel="stylesheet" href="Style.css">';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<td>'. $seq.' </td>';
        $html .= '<td>'. $comment.' </td>';
        $html .= '<td>'. $status.' </td>';
        $html .= '</tr>';
        print_r($html);

    }

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


$result_all_acc = accounts::findAll();
echo "<h3> Select All Records from Accounts </h3>";
tableConst($result_all_acc);

$result_one_acc = accounts::findOne(10);
echo "<h3> Select One Record from Accounts </h3>";
tableConst($result_one_acc);

$result_all_todo = todos::findAll();
echo "<h3> Select All Records from Todos </h3>";
tableConst($result_all_todo);

$result_one_todo = todos::findOne(7);
echo "<h3> Select One Record from Todos </h3>";
tableConst($result_one_todo);


class model
{

    static $seq = 0;
    static $operation = "";


    public function save(){

        $data = get_object_vars($this);
        //print_r ($data);
        if(is_null($this->id)){

            self::$operation = "Insert";
            //echo "Here for Insert:: <br>";
            $sql = $this->insert($data);

        }
        else {
            self::$operation = "Update";
            //echo "Here for Update:: <br>";
            $sql = $this->update($data);

        }
        $this->runQuery($sql);

    }

    public function runQuery($sql){

        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $flag = $statement->execute();
        if($flag){
            self::$seq = self::$seq +1;
            $comment= 'Success for '.self::$operation. ' in table '.static::$tableName;
            $status = 'Completed';
            CreateStatusTable::addRow(self::$seq,$comment,$status);

        }
        else{
            echo 'Problem for '.self::$operation;

        }

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
        //echo $sql;
        //echo "<br>";
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

        //echo $sql;
        //echo "<br>";
        return $sql;
        //$this->runQuery($sql);

    }

    public function delete(){

        //echo "Here for delete for id:: <br>".$this->id;
        self::$operation = "Delete";
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


}

class account extends model {

    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    protected static $tableName = 'accounts';


}
$c = new CreateStatusTable();
$insertObj = new todo();
$insertObj->owneremail = 'k2k@india.com';
$insertObj->ownerid = '19';
$insertObj->createddate = "2017-11-09";
$insertObj->duedate = "2017-11-26";
$insertObj->message = "HelloMoto";
$insertObj->isdone = '0';
$insertObj->save();

$updateObj = new todo();
$updateObj->id= '19';
$updateObj->owneremail = 'gun@roses.com';
$updateObj->ownerid = '19';
$updateObj->createddate = "2017-11-09";
$updateObj->duedate = "2017-11-26";
$updateObj->message = "Hello4u";
$updateObj->isdone = '0';
$updateObj->save();

$deleteObj = new todo();
$deleteObj->id= '24';
$deleteObj->delete();

$insertObj1 = new account();
$insertObj1->email = 'as1@ggg.com';
$insertObj1->fname = 'Asw';
$insertObj1->lname = 'Ujim';
$insertObj1->phone = '221-111-033';
$insertObj1->birthday = '1999-10-01';
$insertObj1->gender = 'male';
$insertObj1->password = 'ray123';
$insertObj1->save();

$updateObj1 = new account();
$updateObj1->id = '13';
$updateObj1->email = 'lopt@kuy.com';
$updateObj1->fname = 'Hert';
$updateObj1->lname = 'Jay';
$updateObj1->phone = '221-122-033';
$updateObj1->birthday = '1999-10-01';
$updateObj1->gender = 'male';
$updateObj1->password = 'Jay0123';
$updateObj1->save();

$deleteObj1 = new account();
$deleteObj1->id= '11';
$deleteObj1->delete();





