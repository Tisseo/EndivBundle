README
======

Description
-----------

ENDIV is a French acronym for "ENtrepôt de Données de l'Information Voyageur".
It is also known as [TID](https://github.com/Tisseo/TID)
for "Travellers Information Datawarehouse" project.

EndivBundle is providing mapping and services in order to access
TID database. It is used by [BoaBundle](https://github.com/Tisseo/BoaBundle)
and [PaonBundle](https://github.com/Tisseo/PaonBundle) which manage
TID database through different scopes.

Requirements
------------

- PHP 5.4+
- Symfony 2.6.x
- https://github.com/djlambert/doctrine2-spatial
- https://github.com/orocrm/doctrine-extensions
- https://github.com/schmittjoh/serializer
- https://github.com/Tisseo/TID (ENDIV)

Installation
------------

1. composer.json

```
"repositories": [
    {
        "type": "git",
        "url": "https://github.com/Tisseo/EndivBundle.git"
    },
    //...
],
"require": {
    "tisseo/endiv-bundle": "dev-master",
    // ...
}
```

2. AppKernel.php

```
$bundles = array(
    new Tisseo\EndivBundle\TisseoEndivBundle(),
    // ...
);
```

Configuration
-------------

You need to declare some parameters and configuration values in your main
Symfony 2 application.

- parameters.yml

```
parameters:
    endiv_database_connection:              endiv
    endiv_database_driver:                  pdo_pgsql
    endiv_database_host:                    localhost
    endiv_database_port:                    5432
    endiv_database_name:                    endiv
    endiv_database_user:                    endiv_owner
    endiv_database_password:                endiv_password
```

In config.yml file, you must provide the configuration for the specific
connection to ENDIV database and some mapping types information.

- config.yml

```
doctrine:
    dbal:
        types:
            geometry:                       CrEOF\Spatial\DBAL\Types\GeometryType
            point:                          CrEOF\Spatial\DBAL\Types\Geometry\PointType
            calendar_operator:              Doctrine\DBAL\Types\StringType
            line_version_status:            Doctrine\DBAL\Types\StringType
            calendar_type:                  Doctrine\DBAL\Types\StringType
            date_id:                        Tisseo\EndivBundle\Types\DateIdType
        connections:
            %endiv_database_connection%:
                driver:                     "%endiv_database_driver%"
                host:                       "%endiv_database_host%"
                port:                       "%endiv_database_port%"
                dbname:                     "%endiv_database_name%"
                user:                       "%endiv_database_user%"
                password:                   "%endiv_database_password%"
                charset:                    UTF8
                mapping_types:
                    calendar_operator:      string
                    line_version_status:    string
                    calendar_type:          string
                    _text:                  string
    orm:
        auto_generate_proxy_classes:        "%kernel.debug%"
        default_entity_manager:             default
        entity_managers:
            auto_mapping:                   true
            %endiv_database_connection%:
                connection:                 "%endiv_database_connection%"
                mappings:
                    TisseoEndivBundle:      ~
                dql:
                    string_functions:
                        group_concat:       Oro\ORM\Query\AST\Functions\String\GroupConcat
                        cast:               Oro\ORM\Query\AST\Functions\Cast
```

Contributing
------------

- Vincent Passama - vincent.passama@gmail.com
- Rodolphe Duval - rdldvl@gmail.com
- Pierre-Yves Claitte - pierre.cl@gmail.com
