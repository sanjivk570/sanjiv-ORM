<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Database
 *
 * @author sanjiv
 */

namespace Database;

//require_once 'config.php';
//require_once 'Libraries/Logger.php';

use Exception;
use Logger;
use RuntimeException;
use InvalidArgumentException;
use DatabaseInterface;

//use DatabaseInterface;

class Database implements DatabaseInterface {

    public $config = array();
    public $link;
    public $logger;
    public $result;

    public function __construct() {
        $this->logger = new Logger("development.log", Logger::INFO);
        $configInfo = DB;
        if (\count($configInfo) !== 4) {
            $this->logException('Invalid number of connection parameters.');
        }
        $this->config = $configInfo;
    }

    public function logException($message, $excType = 'Exception') {
        if (empty($message)) {
            throw new Exception("Message can't empty");
        }
        if ($excType === 'Exception') {
            $this->logger->info(new Exception($message));
            throw new Exception($message);
        } else if ($excType === 'RuntimeException') {
            $this->logger->info(new RuntimeException($message));
            throw new RuntimeException($message);
        } else if ($excType === 'InvalidArgumentException') {
            $this->logger->info(new InvalidArgumentException($message));
            throw new InvalidArgumentException($message);
        }
        throw new Exception("Logging functionality not working");
    }

    public function connect() {
// connect only once
        if ($this->link === null) {
            $host = $this->config['HOST'];
            $user = $this->config['USER'];
            $password = $this->config['PASS'];
            $database = $this->config['NAME'];

            if (!$this->link = \mysqli_connect($host, $user, $password, $database)) {
                $this->logException('Error connecting to the server : ' . mysqli_connect_error(), 'RuntimeException');
            }
            unset($host, $user, $password, $database);
        }
        return $this->link;
    }

    public function query($query) {
        if (!is_string($query) || empty($query)) {
            $this->logException('The specified query is not valid.', 'InvalidArgumentException');
        }
// lazy connect to MySQL
        $this->connect();
        if (!$this->result = \mysqli_query($this->link, $query)) {
            $this->logException('Error executing the specified query ' . $query . mysqli_error($this->link));
        }
        return $this->result;
    }

    public function select($table, $where = '', $fields = '*', $order = '', $limit = null, $offset = null) {
        $query = 'SELECT ' . $fields . ' FROM ' . $table
                . (($where) ? ' WHERE ' . $where : '')
                . (($limit) ? ' LIMIT ' . $limit : '')
                . (($offset && $limit) ? ' OFFSET ' . $offset : '')
                . (($order) ? ' ORDER BY ' . $order : '');
        $this->query($query);
//return $this->countRows();
    }

    public function insert($table, array $data) {
        $fields = implode(',', array_keys($data));
        $values = implode(',', array_map(array($this, 'quoteValue'), array_values($data)));
        $query = 'INSERT INTO ' . $table . ' (' . $fields . ') ' . ' VALUES (' . $values . ')';
        $this->query($query);
        return $this->getInsertId();
    }

    /**
     * Perform an UPDATE statement
     */
    public function update($table, array $data, $where = '') {
        $set1 = [];
        foreach ($data as $field => $value) {
            $set1[] = $field . '=' . $this->quoteValue($value);
        }
        $set2 = implode(',', $set1);
        $query = 'UPDATE ' . $table . ' SET ' . $set2
                . (($where) ? ' WHERE ' . $where : '');
        $this->query($query);
        return $this->getAffectedRows();
    }

    /**
     * Perform a DELETE statement
     */
    public function delete($table, $where = '') {
        $query = 'DELETE FROM ' . $table
                . (($where) ? ' WHERE ' . $where : '');
        $this->query($query);
        return $this->getAffectedRows();
    }

    /**
     * Escape the specified value
     */
    public function quoteValue($value) {
        $this->connect();
        if ($value === null) {
            $value = 'NULL';
        } else if (!is_numeric($value)) {
            $value = "’" . mysqli_real_escape_string($this->link, $value) . "’";
        }
        return $value;
    }

    /**
     * Fetch a single row from the current result set (as an associative array)
     */
    public function fetch() {
        if ($this->result !== null) {
            if (($row = mysqli_fetch_array($this->result, MYSQLI_ASSOC)) === false) {
                $this->freeResult();
            }
            return $row;
        }
        return false;
    }

    /**
     * Get the insertion ID
     */
    public function getInsertId() {
        return $this->link !== null ? mysqli_insert_id($this->link) : null;
    }

    /**
     * Get the number of rows returned by the current result set
     */
    public function countRows() {
        return $this->result !== null ? mysqli_num_rows($this->result) : 0;
    }

    /**
     * Get the number of affected rows
     */
    public function getAffectedRows() {
        return $this->link !== null ? mysqli_affected_rows($this->link) : 0;
    }

    /**
     * Free up the current result set
     */
    public function freeResult() {
        if ($this->result === null) {
            return false;
        }
        mysqli_freeresult($this->result);
        return true;
    }

    /**
     * Close explicitly the database connection
     */
    public function disconnect() {
        if ($this->link === null) {
            return false;
        }
        mysqli_close($this->link);
        $this->link = null;
        return true;
    }

    /**
     * Close automatically the database connection when the instance of the class is destroyed
     */
    public function __destruct() {
        $this->disconnect();
    }

}

//print_r($object->config);
