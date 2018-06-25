<?php

namespace AmcLab\AmcDatabase\Wrappers;

class MySqlWrapper {

    protected $settings;

    public function __construct() {

        // TODO: in futuro, gestire la possibilità di passare settaggi custom per ogni db passando da qui...

        $this->settings = [
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ];
    }

    public function createDatabase($connectionResolver, ...$params) {

        [$hostname, $database, $username, $password] = $params;

        $connectionResolver->statement("CREATE DATABASE $database CHARACTER SET {$this->settings['charset']} COLLATE {$this->settings['collation']};");
        $connectionResolver->statement("CREATE USER '$username'@'%' IDENTIFIED BY '$password';");
        $connectionResolver->statement("GRANT USAGE ON $database.* TO '$username'@'%';");
        $connectionResolver->statement("GRANT SELECT, SHOW VIEW, EXECUTE, ALTER, ALTER ROUTINE, CREATE, CREATE ROUTINE, CREATE TEMPORARY TABLES, CREATE VIEW, DELETE, DROP, EVENT, INDEX, INSERT, REFERENCES, TRIGGER, UPDATE, LOCK TABLES ON $database.* TO '$username'@'%' WITH GRANT OPTION;");

        return [
            'package' =>  [
                'driver'    => 'mysql',
                'host'      => $hostname,
                'port'      => '3306',
                'database'  => $database,
                'charset'   => $this->settings['charset'],
                'collation' => $this->settings['collation'],
                'prefix'    => '',
                'strict'    => true,
                'engine'    => 'InnoDB',
                'username'  => $username,
                'password'  => $password,
            ],
        ];

    }

    public function markDatabaseForDeletion() {
        // TODO:
    }


}
