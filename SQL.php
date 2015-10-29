<?php

namespace Panada\Database;

/*
 * Panada PDO Database Driver.
 *
 * @author	Azhari Harahap <azhari@harahap.us>
 * @since	Version 1.0
 */

use PDO;
use Panada\Resource;

class SQL extends SQLAbstract implements SQLInterface
{
    protected $dsn;
    public $link;
    protected $config = [
        'dsn' => 'sqlite::memory:',
        'username' => null,
        'password' => null,
        'options' => [
            PDO::ATTR_PERSISTENT => true,
        ],
    ];

    /**
     * Check if PDO enabled
     * Define all properties needed.
     */
    public function __construct($config = [], $connectNow = true)
    {
        $this->config = array_merge($this->config, $config);

        if ($connectNow) {
            $this->connect();
        }
    }

    public static function getInstance($type = 'default')
    {
        return new static(Resource\Config::database()[$type], false);
    }

    /**
     * Establish a new connection.
     *
     * @return connection resource
     */
    public function connect()
    {
        return $this->link = new PDO($this->config['dsn'], $this->config['username'], $this->config['password'], $this->config['options']);
    }

    /**
     * Initial for all process.
     */
    private function init()
    {
        if (is_null($this->link)) {
            $this->link = $this->connect();
        }
    }

    /**
     * Start transaction.
     */
    public function begin()
    {
        $this->link->beginTransaction();
    }

    /**
     * Commit transaction.
     */
    public function commit()
    {
        $this->link->commit();
    }

    public function rollback()
    {
        $this->link->rollBack();
    }

    /**
     * Escape all unescaped string.
     *
     * @param string $string
     */
    public function escape($string)
    {
        if (is_null($this->link)) {
            $this->init();
        }

        return $this->link->quote($string);
    }

    public function query($sql, $method = 'query')
    {
        if (is_null($this->link)) {
            $this->init();
        }

        $query = $this->link->$method($sql);
        $this->lastQuery = $sql;

        if (!$query) {
            $backtrace = debug_backtrace()[2];
            throw new \ErrorException($this->link->errorInfo()[2], 0, 1, $backtrace['file'], $backtrace['line']);
        }

        return $query;
    }

    public function exec($sql)
    {
        return $this->query($sql, 'exec');
    }

    /**
     * Get multiple records.
     *
     * @param string $query The sql query
     * @param string $type  return data type option. the default is "object"
     */
    public function results($query, $returnType = false)
    {
        $return = false;

        if ($returnType) {
            $this->returnType = $returnType;
        }

        if (is_null($query)) {
            $query = $this->command();
        }

        $result = $this->query($query);

        if ($this->returnType == 'object' || $this->returnType == 'array') {
            $fetch = $this->returnType == 'object' ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC;

            while ($row = $result->fetch($fetch)) {
                $return[] = $row;
            }

            return $return;
        }

        if ($this->returnType == 'iterator') {
            return $result;
        }

        return $return;
    }

    /**
     * Get single record.
     *
     * @param string $query The sql query
     * @param string $type  return data type option. the default is "object"
     */
    public function row($query, $returnType = false)
    {
        if ($returnType) {
            $this->returnType = $returnType;
        }

        if (is_null($query)) {
            $query = $this->command();
        }

        if (is_null($this->link)) {
            $this->init();
        }

        $result = $this->query($query);

        return $result->fetch($this->returnType == 'object' ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC);
    }

    /**
     * Get the id form last insert.
     *
     * @return int
     */
    public function insertId()
    {
        return $this->link->lastInsertId();
    }

    /**
     * Get this db version.
     */
    public function version()
    {
        return $this->link->getAttribute(constant('PDO::ATTR_SERVER_VERSION'));
    }

    /**
     * Close db connection.
     */
    public function close()
    {
        $this->link = null;
    }

    /**
     * Prepares a statement for execution and returns a statement object.
     *
     * @return PDOStatement
     */
    public function prepare($query)
    {
        if (is_null($this->link)) {
            $this->init();
        }

        return $this->link->prepare($query);
    }
}
