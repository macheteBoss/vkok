<?php
require_once "config_class.php";

class DataBase {

    private $config;
    private $mysqli;

    public function __construct()
    {
        $this->config = new Config();
        $this->mysqli = new mysqli($this->config->host, $this->config->user, $this->config->password, $this->config->db);
        $this->mysqli->query("SET NAMES 'utf8'");
    }

    public function query($query) {
        return $this->mysqli->query($query);
    }

    public function select($table_name, $fields, $where = "", $order = "", $up = true, $limit = "", $dis = 1) {
        for($i = 0; $i < count($fields); $i++) {
            if((strpos($fields[$i], "(") === false) && ($fields[$i] != "*")) $fields[$i] = "`".$fields[$i]."`";
        }
        $fields = implode(",", $fields);
        if(!$order) $order = "ORDER BY `id`";
        else {
            if($order != "RAND()") {
                $order = "ORDER BY `$order`";
                if(!$up) $order .= " DESC";
            } else $order = "ORDER BY $order";
        }
        if($limit) $limit = "LIMIT $limit";
        if($where) $query = "SELECT $fields FROM $table_name WHERE $where $order $limit";
        else if($where && $dis == 1) $query = "SELECT DISTINCT($fields) AS $fields FROM $table_name WHERE $where $order $limit";
        else $query = "SELECT $fields FROM $table_name $order $limit";

        $result_set = $this->query($query);
        if(!$result_set) return false;
        $i = 0;
        while($row = $result_set->fetch_assoc()) {
            $data[$i] = $row;
            $i++;
        }
        $result_set->close();
        return $data;
    }

    public function insert($table_name, $new_values) {
        $query = "INSERT INTO $table_name (";
        foreach ($new_values as $field=>$value) $query .= "`".$field."`,";
        $query = substr($query, 0, -1);
        $query .= ") VALUES (";
        foreach ($new_values as $value) $query .= "'".addslashes($value)."',";
        $query = substr($query, 0, -1);
        $query .= ")";
        return $this->query($query);
    }

    public function update($table_name, $upd_fields, $where) {
        $query = "UPDATE $table_name SET ";
        foreach ($upd_fields as $field => $value) $query .= "`".$field."`='".addslashes($value)."',";
        $query = substr($query, 0, -1);
        if($where) {
            $query .= " WHERE $where";
            return $this->query($query);
        } else return false;
    }

    public function delete($table_name, $where = "") {
        if($where) {
            $query = "DELETE FROM $table_name WHERE $where";
            return $this->query($query);
        } else return false;
    }

    public function getField($table_name, $field_out, $field_in, $value_in) {
        $data = $this->select($table_name, array($field_out), "`".$field_in."`='".addslashes($value_in)."'");
        if(count($data) != 1) return false;
        return $data[0][$field_out];
    }

    public function getFieldOnID($table_name, $id, $field_out) {
        if(!$this->existsID($table_name, $id)) return false;
        return $this->getField($table_name, $field_out, "id", $id);
    }

    public function getAll($table_name, $where = "", $order, $up) {
        return $this->select($table_name, array("*"), $where, $order, $up);
    }

    public function deleteOnID($table_name, $id) {
        if(!$this->existsID($table_name, $id)) return false;
        return $this->delete($table_name, "`id`='".$id."'");
    }

    public function setField($table_name, $field, $value, $field_in, $value_in) {
        return $this->update($table_name, array($field=>$value), "`".$field_in."`='".addslashes($value_in)."'");
    }

    public function setFieldOnID($table_name, $id, $field, $value) {
        if(!$this->existsID($table_name, $id)) return false;
        return $this->setField($table_name, $field, $value, "id", $id);
    }

    public function getElementOnID($table_name, $id) {
        if(!$this->existsID($table_name, $id)) return false;
        $arr = $this->select($table_name, array("*"), "`id`='".$id."'");
        return $arr[0];
    }

    public function getElementOnField($table_name, $field, $value) {
        $arr = $this->select($table_name, array("*"), "`".$field."`='".addslashes($value)."'");
        return $arr[0];
    }

    public function getCount($table_name) {
        $data = $this->select($table_name, array("COUNT(`id`)"));
        return $data[0]["COUNT(`id`)"];
    }

    public function isExists($table_name, $field, $value) {
        $data = $this->select($table_name, array("id"), "`".$field."`='".addslashes($value)."'");
        if(count($data) === 0) return false;
        return true;
    }

    private function existsID($table_name, $id) {
        $data = $this->select($table_name, array("id"), "`id`='".addslashes($id)."'");
        if(count($data) === 0) return false;
        return true;
    }

    public function destruct() {
        if($this->mysqli) $this->mysqli->close();
    }

}
?>