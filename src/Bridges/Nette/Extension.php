<?php declare(strict_types=1);

namespace Identity\Authorizator\Bridges\Nette;

use Identity\Authorizator\Bridges\Tracy\Panel;
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
        'debugger'  => true,
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

        $driver = null;
        if ($config['driver']) {
            $driver = $builder->addDefinition($this->prefix('driver'))
                ->setFactory($config['driver'])
                ->addSetup('setPolicy', [$config['policy']])
                ->setAutowired($config['autowired']);
        }

        // define panel
        if ($config['debugger'] && $driver) {
            $panel = $builder->addDefinition($this->prefix('panel'))
                ->setFactory(Panel::class, [$driver]);

            // linked panel to tracy
            $builder->getDefinition('tracy.bar')
                ->addSetup('addPanel', [$panel]);
        }
    }
}
