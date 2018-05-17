<?php declare(strict_types=1);

namespace Identity\Authorizator;

use Exception;
use Nette\Security\Permission;
use Nette\SmartObject;


/**
 * Class Authorizator
 *
 * @author  geniv
 * @package Identity\Authorizator
 */
abstract class Authorizator implements IIdentityAuthorizator
{
    use SmartObject;

    // define type policy
    const
        POLICY_NONE = 'none',
        POLICY_ALLOW = 'allow',
        POLICY_DENY = 'deny';

    const
        POLICY_DESCRIPTION = [
        self::POLICY_NONE  => 'all is allow, ignore part',
        self::POLICY_ALLOW => 'all is deny, allow part',
        self::POLICY_DENY  => 'all is allow, deny part',
    ];

    /** @var string */
    protected $policy;
    /** @var Permission */
    protected $permission;
    /** @var array */
    protected $role = [], $resource = [], $privilege = [], $acl = [];
    /** @var array */
    private $listCurrentAcl = [];


    /**
     * Authorizator constructor.
     */
    public function __construct()
    {
        $this->permission = new Permission;
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
            return ($this->role[$id] ?? []);
        }
        return $this->role;
    }


    /**
     * Get id role by name.
     *
     * @param string $name
     * @return string
     */
    public function getIdRoleByName(string $name): string
    {
        $filter = array_filter($this->role, function ($item) use ($name) {
            return $item['role'] == $name;
        });
        return implode(array_keys($filter));
    }


    /**
     * Get resource.
     *
     * @param null $id
     * @return array
     */
    public function getResource($id = null): array
    {
        if ($id) {
            return ($this->resource[$id] ?? []);
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
        $filter = array_filter($this->resource, function ($item) use ($name) {
            return $item['resource'] == $name;
        });
        return implode(array_keys($filter));
    }


    /**
     * Get privilege.
     *
     * @param null $id
     * @return array
     */
    public function getPrivilege($id = null): array
    {
        if ($id) {
            return ($this->privilege[$id] ?? []);
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
        $filter = array_filter($this->privilege, function ($item) use ($name) {
            return $item['privilege'] == $name;
        });
        return implode(array_keys($filter));
    }


    /**
     * Get acl.
     *
     * @param null $idRole
     * @param null $idResource
     * @return array
     */
    public function getAcl($idRole = null, $idResource = null): array
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
     * Is all.
     *
     * @param      $idRole
     * @param null $idResource
     * @return bool
     */
    public function isAll($idRole, $idResource = null): bool
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
        // collect acl
        $this->listCurrentAcl[$role . $resource . $privilege] = [
            'role'      => $role,
            'resource'  => $resource,
            'privilege' => $privilege,
        ];

        if ($this->policy == self::POLICY_NONE) {
            return true;
        }
        return $this->permission->isAllowed($role, $resource, $privilege);
    }


    /**
     * Get current acl list.
     *
     * @return array
     */
    public function getListCurrentAcl(): array
    {
        return $this->listCurrentAcl;
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
     * @param       $role
     * @param array $values
     * @return int
     */
    abstract public function saveAcl($idRole, array $values, bool $deleteBeforeSave = true): int;
}
