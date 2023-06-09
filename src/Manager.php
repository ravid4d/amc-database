<?php

namespace AmcLab\AmcDatabase;

use AmcLab\AmcDatabase\Exceptions\ManagerException;
use AmcLab\Baseline\Contracts\PackageStore;
use AmcLab\Baseline\Contracts\PersistenceManager;
use AmcLab\Environment\Contracts\Resolver;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Manager implements PersistenceManager {

    protected $app;
    protected $store;
    protected $resolver;

    protected $db;
    protected $wrapper;
    protected $serverIdentity;

    public function __construct(Application $app, PackageStore $store, Resolver $resolver) {
        $this->app = $app;
        $this->store = $store;
        $this->resolver = $resolver;
    }

    public function getStore() {
        return $this->store;
    }

    public function setDatabaseConnector(ConnectionResolverInterface $db) {
        $this->db = $db;
        return $this;
    }

    public function setServerIdentity($serverIdentity) {

        $this->store->setPathway('database-server', $serverIdentity);

        try {
            $response = $this->store->read();
        }

        catch (Exception $e) {
            $this->unsetIdentity();
            throw $e;
        }

        $this->resolver->bootstrap([\AmcLab\Environment\Hooks\DatabaseHook::class]);

        $this->resolver->populate($response['disclosed'], [
            'database' => [
                'connection' => 'server_manager',
                'connector' => $this->db,
            ]
        ]);

        $this->serverIdentity = $serverIdentity;

        return $this;
    }

    public function unsetServerIdentity() {
        $this->store->unsetPathway();
        $this->serverIdentity = null;
        return $this;
    }

    public function create($pathway) {

        if (!$this->serverIdentity) {
            throw new ManagerException('No server set', 1000);
        }

        // // FIXME: queste 4 righe andrebbero sistemate aggiungento un attributo "type" al Pathfinder
        // // NOTE: se sistemato, sistemare anche StoryTellerJob@handle!!
        // if ($pathway['resourceId'][1] === 'tenant') {
        //     unset($pathway['resourceId'][1]);
        //     $pathway['resourceId'] = array_values($pathway['resourceId']);
        // }


        $params = [
            $hostname = $this->resolver->use('database')->getConfig()['host'],
            //$database = strtoupper(join('_', $pathway['resourceId'])) . '_DB',
            $database = strtoupper($pathway['linkableResourceId']) . '_DB',
            $username = 'user_' . strtoupper(Arr::last($pathway['normalized'])) . '_' . strtolower(Str::random(4)),
            $password = Str::random(16),
        ];

        $wrapper = $this->app->make('db.wrapper.' . class_basename(get_class($this->db->connection())) );

        // NOTE: i parametri potrebbero arrivare già popolati da fuori, in futuro,
        // e non è detto che siano necessariamente questi (es.: mongodb usa "database" e "collection")...
        return $wrapper->createDatabase($this->resolver->use('database'), ...$params);


    }

    public function destroy() {

        throw new ManagerException('TODO!');

    }

}
