<?php

namespace Tisseo\EndivBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TisseoEndivExtension extends Extension Implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yml');
        $loader->load('ogive.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        // mapping functions for DQL
        $DQLExtensions = array(
            'string_functions' => array(
                'unaccent'      => 'Tisseo\EndivBundle\Doctrine\Query\Unaccent',
                'group_concat'  => 'Oro\ORM\Query\AST\Functions\String\GroupConcat',
                'cast'          => 'Oro\ORM\Query\AST\Functions\Cast'
            )
        );

        // mapping types to objects
        $DBALTypes = array(
            'geometry'              => 'CrEOF\Spatial\DBAL\Types\GeometryType',
            'point'                 => 'CrEOF\Spatial\DBAL\Types\Geometry\PointType',
            'calendar_operator'     => 'Doctrine\DBAL\Types\StringType',
            'line_version_status'   => 'Doctrine\DBAL\Types\StringType',
            'calendar_type'         => 'Doctrine\DBAL\Types\StringType',
            'date_id'               => 'Tisseo\EndivBundle\Types\DateIdType'
        );

        // mapping custom types
        $mappingTypes = array(
            'calendar_operator'     => 'string',
            'line_version_status'   => 'string',
            'calendar_type'         => 'string',
            '_text'                 => 'string'
        );

        $container->prependExtensionConfig('doctrine',
            array(
                'dbal' => array(
                    'types' => $DBALTypes,
                    'connections' => array(
                        '%endiv_database_connection%' => array(
                            'driver'        => '%endiv_database_driver%',
                            'host'          => '%endiv_database_host%',
                            'port'          => '%endiv_database_port%',
                            'dbname'        => '%endiv_database_name%',
                            'user'          => '%endiv_database_user%',
                            'password'      => '%endiv_database_password%',
                            'charset'       => '%endiv_database_charset%',
                            'mapping_types' => $mappingTypes
                        )
                    )
                ),
                'orm' => array(
                    'entity_managers' => array(
                        '%endiv_database_connection%' => array(
                            'dql' => $DQLExtensions,
                            'connection' => '%endiv_database_connection%'
                        )
                    )
                )
            )
        );
    }
}
