<?php declare(strict_types=1);

namespace Identity\Authorizator;


/**
 * Trait AuthorizatorTrait
 *
 * @author  geniv
 * @package Identity\Authorizator
 */
trait AuthorizatorTrait
{
    /** @var IIdentityAuthorizator @inject */
    public $identityAuthorizator;


    /**
     * Handle add acl.
     *
     * @param string      $role
     * @param string|null $resource
     * @param string|null $privilege
     */
    public function handleAddAcl(string $role, string $resource = null, string $privilege = null)
    {
        $idRole = $this->identityAuthorizator->getIdRoleByName($role);
        $idResource = null;
        if ($resource) {
            $idResource = $this->identityAuthorizator->getIdResourceByName($resource);
        }

        $idPrivilege = null;
        if ($privilege) {
            $idPrivilege = $this->identityAuthorizator->getIdPrivilegeByName($privilege);
            if (!$idPrivilege) {
                $this->identityAuthorizator->savePrivilege(['id' => null, 'privilege' => $privilege]);
                $idPrivilege = $this->identityAuthorizator->getIdPrivilegeByName($privilege);
            }
        } else {
            $idPrivilege = 'all';
        }

        if ($idResource && $idPrivilege) {
            if ($this->identityAuthorizator->saveAcl($idRole, ['all' => null, $idResource => [$idPrivilege]], false)) {
                $this->flashMessage('Save');
            }
        }
        $this->redirect('this');
    }
}

