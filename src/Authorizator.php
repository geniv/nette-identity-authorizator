<?php declare(strict_types=1);

namespace Identity\Authorizator;

use Exception;
use Nette\DI\Container;
use Nette\Neon\Neon;
use Nette\Security\Permission;
use Nette\SmartObject;
use Nette\Utils\Strings;


/**
 * Class Authorizator
 *
 * @author  geniv
 * @package Identity\Authorizator
 */
abstract class Authorizator implements IIdentityAuthorizator
{
    use SmartObject;

    const
        PATH_LIST_CURRENT_ACL = '%s/config/listCurrentAcl-%s.neon';

    /** @var string */
    protected $policy;
    /** @var Permission */
    protected $permission;
    /** @var array */
    protected $role = [], $resource = [], $privilege = [], $acl = [];
    /** @var array */
    private $listCurrentAcl = [];
    /** @var string */
    private $appDir;


    /**
     * Authorizator constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->permission = new Permission;

        // dir for current list acl
        $this->appDir = $container->getParameters()['appDir'];
    }


    /**
     * Get policy.
     *
     * @return string
     */
    public function getPolicy(): string
    {
        return $this->policy;
    }


    /**
     * Set policy.
     *
     * @param string $policy
     * @throws Exception
     */
    public function setPolicy(string $policy)
    {
        // allow (all is deny, allow part) | deny (all is allow, deny part) | none (all is allow, ignore part)
        if (!in_array($policy, [self::POLICY_NONE, self::POLICY_ALLOW, self::POLICY_DENY])) {
            throw new Exception('Unsupported policy type: ' . $policy);
        }
        $this->policy = $policy;

        $this->init();   // init data after set policy (in extension)!
    }


    /**
     * Get role.
     *
     * @param string|null $id
     * @return array
     */
    public function getRole(string $id = null): array
    {
        if ($id) {
            if (is_numeric($id)) {
                $filter = array_filter($this->role, function ($row) use ($id) { return $row['id'] == $id; });
                return (array) array_pop($filter);
            }
            return (array) ($this->role[$id] ?? []);
        }
        return $this->role;
    }


    /**
     * Get roles.
     *
     * @return array
     */
    public function getRoles(): array
    {
        // return simple array roles
        return array_map(function ($item) {
            return $item['role'];
        }, $this->role);
    }


    /**
     * Get id role by name.
     *
     * @param string $name
     * @return string
     */
    public function getIdRoleByName(string $name): string
    {
        if (isset($this->role[$name])) {
            return (string) $this->role[$name]['id'];
        }
        return '';
    }


    /**
     * Get resource.
     *
     * @param string|null $id
     * @return array
     */
    public function getResource(string $id = null): array
    {
        if ($id) {
            if (is_numeric($id)) {
                $filter = array_filter($this->resource, function ($row) use ($id) { return $row['id'] == $id; });
                return (array) array_pop($filter);
            }
            return (array) ($this->resource[$id] ?? []);
        }
        return $this->resource;
    }


    /**
     * Get id resource by name.
     *
     * @param string $name
     * @return string
     */
    public function getIdResourceByName(string $name): string
    {
        if (isset($this->resource[$name])) {
            return (string) $this->resource[$name]['id'];
        }
        return '';
    }


    /**
     * Get privilege.
     *
     * @param string|null $id
     * @return array
     */
    public function getPrivilege(string $id = null): array
    {
        if ($id) {
            if (is_numeric($id)) {
                $filter = array_filter($this->privilege, function ($row) use ($id) { return $row['id'] == $id; });
                return (array) array_pop($filter);
            }
            return (array) ($this->privilege[$id] ?? []);
        }
        return $this->privilege;
    }


    /**
     * Get id privilege by name.
     *
     * @param string $name
     * @return string
     */
    public function getIdPrivilegeByName(string $name): string
    {
        if (isset($this->privilege[$name])) {
            return (string) $this->privilege[$name]['id'];
        }
        return '';
    }


    /**
     * Get acl.
     *
     * @param string|null $idRole
     * @param string|null $idResource
     * @return array
     */
    public function getAcl(string $idRole = null, string $idResource = null): array
    {
        if ($idRole) {
            $callback = function ($row) use ($idRole, $idResource) {
                if ($idRole && $idResource) {
                    return $row['id_role'] == $idRole && $row['id_resource'] == $idResource;
                }
                if ($idRole) {
                    return $row['id_role'] == $idRole;
                }
                return true;
            };
            return array_filter($this->acl, $callback);
        }
        return $this->acl;
    }


    /**
     * Get acl form.
     *
     * @param string $idRole
     * @return array
     */
    public function getAclForm(string $idRole): array
    {
        // support method - for load form
        $result = [];
        foreach ($this->resource as $item) {
            $acl = $this->getAcl($idRole, (string) $item['id']);

            if ($this->isAll($idRole, (string) $item['id'])) {
                // idRole, idResource, ALL
                $result[$item['id']] = 'all';
            } else {
                $result[$item['id']] = array_values(array_map(function ($row) { return $row['id_privilege']; }, $acl));
            }
        }

        if ($this->isAll($idRole)) {
            // idRole, ALL, ALL
            $result['all'] = true;
        }
        return ['idRole' => $idRole] + $result;
    }


    /**
     * Is all.
     *
     * @param string      $idRole
     * @param string|null $idResource
     * @return bool
     */
    public function isAll(string $idRole, string $idResource = null): bool
    {
        $acl = $this->getAcl($idRole);
        if ($idResource) {
            $callback = function ($row) use ($idResource) {
                if ($idResource) {
                    return $row['id_resource'] == $idResource;
                }
                return true;
            };
            $res = array_values(array_filter($acl, $callback));
            if (isset($res[0])) {
                return $res[0]['id_privilege'] == self::ALL;
            }
        }

        $aclAll = array_values($acl);
        if (isset($aclAll[0])) {
            return $aclAll[0]['id_resource'] == self::ALL && $aclAll[0]['id_privilege'] == self::ALL;
        }
        return false;
    }


    /**
     * Get permission.
     *
     * @return Permission
     */
    public function getPermission(): Permission
    {
        return $this->permission;
    }


    /**
     * Set allowed.
     *
     * @param $role
     * @param $resource
     * @param $privilege
     */
    public function setAllowed($role = self::ALL, $resource = self::ALL, $privilege = self::ALL)
    {
        if ($this->policy == self::POLICY_ALLOW) {
            $this->permission->allow($role, $resource, $privilege);
        } else {
            $this->permission->deny($role, $resource, $privilege);
        }
    }


    /**
     * Performs a role-based authorization.
     *
     * @param $role
     * @param $resource
     * @param $privilege
     * @return bool
     */
    public function isAllowed($role, $resource, $privilege): bool
    {
        // collect current acl
        $this->listCurrentAcl[$role . $resource . $privilege] = [
            'role'      => $role,
            'resource'  => $resource,
            'privilege' => $privilege,
        ];

        // for policy none
        if ($this->policy == self::POLICY_NONE) {
            return true;
        }

        // auto-fix missing resource in acl
        if ($resource && !isset($this->resource[$resource])) {
            $this->saveResource(['id' => null, 'resource' => $resource]);
        }
        return $this->permission->isAllowed($role, $resource, $privilege);
    }


    /**
     * Get list current acl.
     *
     * @return array
     */
    public function getListCurrentAcl(): array
    {
        return $this->listCurrentAcl;
    }


    /**
     * Get path list current acl.
     *
     * @return string
     */
    private function getPathListCurrentAcl(): string
    {
        return sprintf(self::PATH_LIST_CURRENT_ACL, $this->appDir, explode('\\', get_class($this))[3]);
    }


    /**
     * Save list current acl.
     *
     * @return int
     */
    public function saveListCurrentAcl(): int
    {
        $separate = $last = $this->loadListCurrentAcl();
        foreach ($this->listCurrentAcl as $item) {
            if (!isset($separate[$item['resource']])) {
                $separate[$item['resource']] = [];
            }

            if (!in_array($item['privilege'], $separate[$item['resource']])) {
                $separate[$item['resource']][] = $item['privilege'];
            }
        }
        // save only detect change
        if ($separate != $last) {
            return (int) file_put_contents($this->getPathListCurrentAcl(), Neon::encode($separate, Neon::BLOCK));
        }
        return 0;
    }


    /**
     * Load list current acl.
     *
     * @return array
     */
    public function loadListCurrentAcl(): array
    {
        $path = $this->getPathListCurrentAcl();
        if (file_exists($path)) {
            return Neon::decode(file_get_contents($path));
        }
        return [];
    }


    /**
     * Init.
     */
    abstract protected function init();


    /**
     * Save role.
     *
     * @param array $values
     * @return int
     */
    abstract public function saveRole(array $values): int;


    /**
     * Save resource.
     *
     * @param array $values
     * @return int
     */
    abstract public function saveResource(array $values): int;


    /**
     * Save privilege.
     *
     * @param array $values
     * @return int
     */
    abstract public function savePrivilege(array $values): int;


    /**
     * Save acl.
     *
     * @param string $idRole
     * @param array  $values
     * @param bool   $deleteBeforeSave
     * @return int
     */
    abstract public function saveAcl(string $idRole, array $values, bool $deleteBeforeSave = true): int;
}
