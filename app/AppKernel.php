<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            
            new AppBundle\AppBundle(),
            new UserBundle\UserBundle(),
            new AdminBundle\AdminBundle(),
            
            new FOS\UserBundle\FOSUserBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle(),
           // new FOS\ElasticaBundle\FOSElasticaBundle(),
            new Oneup\UploaderBundle\OneupUploaderBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Bazinga\Bundle\JsTranslationBundle\BazingaJsTranslationBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Phobetor\RabbitMqSupervisorBundle\RabbitMqSupervisorBundle(),
          //  new Snc\RedisBundle\SncRedisBundle(),
            
            new Gos\Bundle\WebSocketBundle\GosWebSocketBundle(),
            new Gos\Bundle\PubSubRouterBundle\GosPubSubRouterBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle()
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}