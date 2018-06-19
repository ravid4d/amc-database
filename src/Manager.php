<?php

namespace AmcLab\AmcDatabase;

use AmcLab\AmcDatabase\Exceptions\ManagerException;
use AmcLab\Baseline\Contracts\PersistenceManager;

class Manager implements PersistenceManager {

    protected $server = [];

    public function setServer($server) {
        $this->server = $server;
        return $this;
    }

    public function unsetServer() {
        $this->server = [];
        return $this;
    }

    public function create($info) {

        $response = $this->requestCreateDatabase($info);

        return [
            'server' => $this->server,
            'package' =>  [
                'driver'    => $response['connection']['driver'],
                'host'      => $response['connection']['host'],
                'port'      => $response['connection']['port'],
                'database'  => $response['database']['name'],
                'charset'   => $response['database']['charset'],
                'collation' => $response['database']['collation'],
                'prefix'    => $response['database']['prefix'],
                'strict'    => $response['database']['strict'],
                'engine'    => $response['database']['engine'],
                'username'  => $response['credentials']['laravel']['username'],
                'password'  => $response['credentials']['laravel']['password'],
            ],
        ];
    }

    public function destroy() {

        throw new ManagerException('TODO!');

    }

    protected function requestCreateDatabase($info) {

        if (!$this->server) {
            throw new ManagerException('No server set');
        }

        // TODO: usando $server, dovrebbe effettuare una richiesta ed ottenere un output.
        // qui ne simulo uno...

        return [
            'connection' => [
                'driver' => 'mysql',
                'host' => 'mariadb' . random_int(1, 5) . '.example.com',
                'port' => '3306',
                'unix_socket' => '',
            ],

            'database' => [
                'name' => strtoupper(join('_', $info['resourceId'])) . '_DB',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => 'InnoDB',
                /*
                    'options' => [
                        # qui andrebbe la configurazione ssl.
                        # LASCIARLA NEL FILE /config/database.php
                        PDO::MYSQL_ATTR_SSL_KEY    => '/etc/ssl/BaltimoreCyberTrustRoot.crt.pem'
                    ],
                */
            ],

            'credentials' => [
                'laravel' => [
                    'username' => 'user_' . strtoupper(array_last($info['normalized'])) . '_' . strtolower(str_random(4)),
                    'password' => str_random(16),
                ],
            ],

        ];

    }

}
