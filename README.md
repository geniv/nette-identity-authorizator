Identity authorizator
=====================

Installation
------------

```sh
$ composer require geniv/nette-identity-authorizator
```
or
```json
"geniv/nette-identity-authorizator": ">=1.0.0"
```

require:
```json
"php": ">=7.0.0",
"nette/nette": ">=2.4.0",
"dibi/dibi": ">=3.0.0"
```

Include in application
----------------------

### available source drivers:
- Identity\Authorizator\Drivers\ArrayDriver  (array configure)
- Identity\Authorizator\Drivers\NeonDriver (neon file)
- Identity\Authorizator\Drivers\DibiDriver (dibi + cache)

### policy:
- `allow` - all is deny, allow part
- `deny` - all is allow, deny part
- `none` - all is allow, ignore part

neon configure:
```neon
# identity authorizator
identityAuthorizator:
#   autowired: true
#   policy: allow
#   driver: Identity\Authorizator\Drivers\ArrayDriver([],[],[],[])
#   driver: Identity\Authorizator\Drivers\NeonDriver(%appDir%/acl.neon)
    driver: Identity\Authorizator\Drivers\DibiDriver(%tablePrefix%)
```

neon configure extension:
```neon
extensions:
    identityAuthorizator: Identity\Authorizator\Bridges\Nette\Extension
```

presenters:
```php
$acl = $this->user->getAuthorizator();
$acl->isAllowed('role', 'resource', 'privilege');

$this->user->isAllowed('resource', 'privilege');
```

usage:
```latte
<span n:if="$user->isAllowed('resource', 'privilege')">...</span>
```

generic usage on security base presenter:
```php
$acl = $this->user->getAuthorizator();
// manual set allowed with internal resolve policy
$acl->setAllowed(IAuthorizator::ALL, 'Homepage');
$acl->setAllowed(IAuthorizator::ALL, 'Login');

if (!$this->user->isAllowed($this->name, $this->action)) {
    // NOT ALLOWED
}
```
