<?php declare(strict_types=1);

namespace Identity\Authorizator\Bridges\Nette;

use Nette\DI\CompilerExtension;


/**
 * Class Extension
 *
 * @author  geniv
 * @package Identity\Authorizator\Bridges\Nette
 */
class Extension extends CompilerExtension
{
    /** @var array default values */
    private $defaults = [
        'autowired' => true,
        'policy'    => 'allow',   // allow|deny|none
        'driver'    => null,
    ];


    /**
     * Load configuration.
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        if ($config['driver']) {
            $builder->addDefinition($this->prefix('driver'))
                ->setFactory($config['driver'])
                ->addSetup('setPolicy', [$config['policy']])
                ->setAutowired($config['autowired']);
        }
    }
}
