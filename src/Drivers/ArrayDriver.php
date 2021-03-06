<?php declare(strict_types=1);

namespace Identity\Authorizator\Drivers;

use Identity\Authorizator\Authorizator;
use Nette\DI\Container;


/**
 * Class ArrayDriver
 *
 * @author  geniv
 * @package Identity\Authorizator\Drivers
 */
class ArrayDriver extends Authorizator
{
    /** @var array */
    private $_role = [], $_resource = [], $_privilege = [], $_acl = [];


    /**
     * ArrayDriver constructor.
     *
     * @param array     $role
     * @param array     $resource
     * @param array     $privilege
     * @param array     $acl
     * @param Container $container
     */
    public function __construct(array $role, array $resource, array $privilege, array $acl, Container $container)
    {
        $this->_role = $role;
        $this->_resource = $resource;
        $this->_privilege = $privilege;
        $this->_acl = $acl;

        parent::__construct($container);
    }


    /**
     * Init data.
     */
    protected function init()
    {
        if ($this->policy != self::POLICY_NONE) {
            // set role
            foreach ($this->_role as $role) {
                $this->role[$role] = ['id' => $role, 'role' => $role];

                $this->permission->addRole($role);
            }

            // set resource
            foreach ($this->_resource as $resource) {
                $this->resource[$resource] = ['id' => $resource, 'resource' => $resource];

                $this->permission->addResource($resource);
            }

            // set privilege
            foreach ($this->_privilege as $privilege) {
                $this->privilege[$privilege] = ['id' => $privilege, 'privilege' => $privilege];
            }

            // for deny enable all
            if ($this->policy == self::POLICY_DENY) {
                $this->permission->allow();
            }

            // set acl
            foreach ($this->_acl as $role => $resources) {
                if (is_array($resources)) {
                    foreach ($resources as $resource => $privilege) {
                        // fill acl array
                        foreach ($privilege as $item) {
                            // strict define keys!
                            $this->acl[] = [
                                'id_role'      => $role, 'role' => $role,
                                'id_resource'  => $resource, 'resource' => $resource,
                                'id_privilege' => $item, 'privilege' => $item,
                            ];
                        }

                        // automatic remove acl if not exist role in role array (remove all acl by role)
                        if (!isset($this->role[$role])) {
                            $this->saveAcl($role, []);
                            continue;
                        }

                        // automatic remove acl resource if resource not exist in resource array (remove acl resource by role)
                        if (!isset($this->resource[$resource])) {
                            unset($this->_acl[$role][$resource]);
                            $this->saveAcl($role, $this->_acl[$role]);
                            continue;
                        }

                        // convert acl all to permission all
                        if (in_array('all', $privilege)) {
                            $privilege = self::ALL;
                        }

                        $this->setAllowed($role, $resource, $privilege);
                    }
                } else {
                    //vse
                    if ($resources == 'all') {
                        // automatic remove acl if not exist role in role array (remove all acl by role)
                        if (!isset($this->role[$role])) {
                            $this->saveAcl($role, []);
                        } else {
                            $this->acl[] = [
                                'id_role'      => $role, 'role' => $role,
                                'id_resource'  => null, 'resource' => null,
                                'id_privilege' => null, 'privilege' => null,
                            ];

                            $this->setAllowed($role);
                        }
                    }
                }
            }
        }
    }


    /**
     * Save role.
     *
     * @param array $values
     * @return int
     */
    public function saveRole(array $values): int
    {
        return 0;
    }


    /**
     * Save resource.
     *
     * @param array $values
     * @return int
     */
    public function saveResource(array $values): int
    {
        return 0;
    }


    /**
     * Save privilege.
     *
     * @param array $values
     * @return int
     */
    public function savePrivilege(array $values): int
    {
        return 0;
    }


    /**
     * Save acl.
     *
     * @param string $idRole
     * @param array  $values
     * @param bool   $deleteBeforeSave
     * @return int
     */
    public function saveAcl(string $idRole, array $values, bool $deleteBeforeSave = true): int
    {
        return 0;
    }
}
