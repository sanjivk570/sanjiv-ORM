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
//namespace DatabaseInterface;
interface DatabaseInterface {

    function connect();

    function disconnect();

    function query($query);

    function fetch();

    function select($table, $conditions = '', $fields = '*', $order = '', $limit = null, $offset = null);

    function insert($table, array $data);

    function update($table, array $data, $conditions);

    function delete($table, $conditions);

    function getInsertId();

    function countRows();

    function getAffectedRows();
}
