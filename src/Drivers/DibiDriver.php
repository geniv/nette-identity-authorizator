<?php declare(strict_types=1);

namespace Identity\Authorizator\Drivers;

use Dibi\Connection;
use Identity\Authorizator\Authorizator;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\DI\Container;


/**
 * Class DibiDriver
 *
 * @author  geniv
 * @package Identity\Authorizator\Drivers
 */
class DibiDriver extends Authorizator
{
    // define constant table names
    const
        TABLE_NAME = 'acl',
        TABLE_NAME_PRIVILEGE = 'acl_privilege',
        TABLE_NAME_RESOURCE = 'acl_resource',
        TABLE_NAME_ROLE = 'acl_role';

    /** @var Cache */
    private $cache;
    /** @var Connection */
    private $connection;
    /** @var string */
    private $tableAcl, $tablePrivilege, $tableResource, $tableRole;


    /**
     * DibiDriver constructor.
     *
     * @param string     $tablePrefix
     * @param Container  $container
     * @param Connection $connection
     * @param IStorage   $storage
     */
    public function __construct(string $tablePrefix, Container $container, Connection $connection, IStorage $storage)
    {
        $this->connection = $connection;
        $this->cache = new Cache($storage, 'cache-Identity-Authorizator-Drivers-DibiDriver');
        // define table names
        $this->tableAcl = $tablePrefix . self::TABLE_NAME;
        $this->tablePrivilege = $tablePrefix . self::TABLE_NAME_PRIVILEGE;
        $this->tableResource = $tablePrefix . self::TABLE_NAME_RESOURCE;
        $this->tableRole = $tablePrefix . self::TABLE_NAME_ROLE;

        parent::__construct($container);
    }


    /**
     * Init.
     *
     * @throws \Throwable
     */
    protected function init()
    {
        if ($this->policy != self::POLICY_NONE) {
            // cache role
            $this->role = $this->cache->load('role');
            if ($this->role === null) {
                $this->role = $this->connection->select('id, role')
                    ->from($this->tableRole)
                    ->fetchAssoc('role');

                $this->cache->save('role', $this->role, [Cache::TAGS => ['DibiDriver']]);  // cachovani bez expirace
            }

            // cache resource
            $this->resource = $this->cache->load('resource');
            if ($this->resource === null) {
                $this->resource = $this->connection->select('id, resource')
                    ->from($this->tableResource)
                    ->fetchAssoc('resource');

                $this->cache->save('resource', $this->resource, [Cache::TAGS => ['DibiDriver']]);  // cachovani bez expirace
            }

            // cache privilege
            $this->privilege = $this->cache->load('privilege');
            if ($this->privilege === null) {
                $this->privilege = $this->connection->select('id, privilege')
                    ->from($this->tablePrivilege)
                    ->fetchAssoc('privilege');

                $this->cache->save('privilege', $this->privilege, [Cache::TAGS => ['DibiDriver']]);  // cachovani bez expirace
            }

            // set permission role
            foreach ($this->role as $item) {
                $this->permission->addRole($item['role']);
            }

            // set permission resource
            foreach ($this->resource as $item) {
                if ($item['resource']) {
                    $this->permission->addResource($item['resource']);
                }
            }

            // for deny enable all
            if ($this->policy == self::POLICY_DENY) {
                $this->permission->allow();
            }

            // cache acl
            $this->acl = $this->cache->load('acl');
            if ($this->acl === null) {
                // strict define keys!
                $this->acl = $this->connection->select('acl.id,' .
                    'role.id id_role, role.role,' .
                    'resource.id id_resource, resource.resource,' .
                    'privilege.id id_privilege, privilege.privilege')
                    ->from($this->tableAcl)->as('acl')
                    ->join($this->tableRole)->as('role')->on('role.id=acl.id_role')
                    ->leftJoin($this->tableResource)->as('resource')->on('resource.id=acl.id_resource')
                    ->leftJoin($this->tablePrivilege)->as('privilege')->on('privilege.id=acl.id_privilege')
                    ->where(['acl.active' => true,])
                    ->orderBy(['acl.position' => 'asc'])
                    ->fetchAll();

                $this->cache->save('acl', $this->acl, [Cache::TAGS => ['DibiDriver']]);  // cachovani bez expirace
            }

            // set permission acl
            foreach ($this->acl as $item) {
                if ($item['role'] && $item['resource'] && $item['privilege']) {
                    $this->setAllowed($item['role'], $item['resource'], $item['privilege']);
                } else if ($item['role'] && $item['resource']) {
                    $this->setAllowed($item['role'], $item['resource']);
                } else if ($item['role']) {
                    $this->setAllowed($item['role']);
                }
            }
        }
    }


    /**
     * General save.
     *
     * @param array  $values
     * @param string $table
     * @return int
     * @throws UniqueConstraintViolationException
     * @throws \Dibi\Exception
     */
    private function generalSave(array $values, string $table): int
    {
        $id = $values['id'];
        unset($values['id']);

        try {
            if (!$id) {
                // add
                return $this->connection->insert($table, $values)->execute();
            } else {
                // update
                if ($values) {
                    return $this->connection->update($table, $values)->where(['id' => $id])->execute();
                } else {
                    // delete
                    return $this->connection->delete($table)->where(['id' => $id])->execute();
                }
            }
        } catch (\Dibi\UniqueConstraintViolationException $e) {
            throw new UniqueConstraintViolationException('Item already exist!');
        }
    }


    /**
     * Save role.
     *
     * @param array $values
     * @return int
     * @throws UniqueConstraintViolationException
     * @throws \Dibi\Exception
     */
    public function saveRole(array $values): int
    {
        $result = $this->generalSave($values, $this->tableRole);
        $this->cache->clean([Cache::TAGS => ['DibiDriver']]);
        return $result;
    }


    /**
     * Save resource.
     *
     * @param array $values
     * @return int
     * @throws UniqueConstraintViolationException
     * @throws \Dibi\Exception
     */
    public function saveResource(array $values): int
    {
        $result = $this->generalSave($values, $this->tableResource);
        $this->cache->clean([Cache::TAGS => ['DibiDriver']]);
        return $result;
    }


    /**
     * Save privilege.
     *
     * @param array $values
     * @return int
     * @throws UniqueConstraintViolationException
     * @throws \Dibi\Exception
     */
    public function savePrivilege(array $values): int
    {
        $result = $this->generalSave($values, $this->tablePrivilege);
        $this->cache->clean([Cache::TAGS => ['DibiDriver']]);
        return $result;
    }


    /**
     * Save acl.
     *
     * @param string $idRole
     * @param array  $values
     * @param bool   $deleteBeforeSave
     * @return int
     * @throws \Dibi\Exception
     */
    public function saveAcl(string $idRole, array $values, bool $deleteBeforeSave = true): int
    {
        $res = 0;
        if ($deleteBeforeSave) {
            // delete all acl for idRole
            $res = (int) $this->connection->delete($this->tableAcl)->where(['id_role' => $idRole])->execute();
        }

        if (isset($values['all']) && $values['all']) {
            return (int) $this->connection->insert($this->tableAcl, [
                'id_role' => $idRole,
                'active'  => true,
            ])->execute();
        }

        foreach ($values as $idResource => $item) {
            if (is_array($item)) {
                foreach ($item as $idPrivilege) {
                    $res = (int) $this->connection->insert($this->tableAcl, [
                        'id_role'      => $idRole,
                        'id_resource'  => $idResource,
                        'id_privilege' => ($idPrivilege == 'all' ? null : $idPrivilege),
                        'active'       => true,
                    ])->execute();

                    if (!$res) {
                        return $res;
                    }
                }
            }
        }
        $this->cache->clean([Cache::TAGS => ['DibiDriver']]);
        return $res;
    }
}
