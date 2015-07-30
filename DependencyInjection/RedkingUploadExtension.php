<?php

namespace Redking\Bundle\UploadBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class RedkingUploadExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Chargement en paramètre des données Amazon S3
        if (!isset($config['amazon_s3']['prefix_url'])) {
            $config['amazon_s3']['prefix_url'] = 'https://s3-'.$config['amazon_s3']['region'].'.amazonaws.com/'.$config['amazon_s3']['bucket'];
        }
        foreach ($config['amazon_s3'] as $key => $value) {
            $container->setParameter('redking_upload.amazon_s3.'.$key, $value);
        }

        $container->setParameter('redking_upload.resizes', $config['resizes']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
