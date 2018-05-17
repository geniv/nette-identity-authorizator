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


    public function handleAddAcl(string $role, string $resource = null, string $privilege = null)
    {
        dump($role, $resource, $privilege);

//        dump($this->identityAuthorizator);

        $idRole = $this->identityAuthorizator->getIdRoleByName($role);
        if ($resource) {
            dump($this->identityAuthorizator->getIdResourceByName($resource));
        }

        if ($privilege) {
            dump($this->identityAuthorizator->getIdPrivilegeByName($privilege));
        }

//        $this->identityAuthorizator->saveAcl($idRole);


//        $this->flashMessage('Bylo smazáno ' . $itemCount . ' položek');
//        $this->redirect('this');
    }
}

