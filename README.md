README
======

Description
-----------

EndivBundle is the core bundle providing mapping and services in order to access
ENDIV database. It is used by BoaBundle and PaonBundle which both pilot
ENDIV database in different scopes. EndivBundle is aimed to work in NMM
application environment. (https://github.com/CanalTP/NmmPortalBundle)

Requirements
------------

- PHP 5.4.3
- https://github.com/djlambert/doctrine2-spatial
- https://github.com/Tisseo/TID 

Installation
------------

1. composer.json:

'''
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
'''

2. AppKernel.php

'''
    $bundles = array(
        new Tisseo\EndivBundle\TisseoEndivBundle(),
        // ...
    );
'''

Configuration
-------------

You don't need to do this if you're working with the main bundle NMM which
provides all this configuration already.


In the main project which must be a Symfony NMM application provided by
https://github.com/CanalTP, you have to declare some parameters first :

- parameters.yml

'''
parameters:
    endiv_database_connection:              endiv
    endiv_database_driver:                  pdo_pgsql
    endiv_database_host:                    localhost
    endiv_database_port:                    5432
    endiv_database_name:                    endiv
    endiv_database_user:                    endiv_owner
    endiv_database_password:                ''
'''

In config.yml file, you must provide the configuration for the specific 
connection to ENDIV database and some mapping types information.

- config.yml

'''
doctrine:
    dbal:
        types:
            geometry:               CrEOF\Spatial\DBAL\Types\GeometryType
            calendar_operator:      Doctrine\DBAL\Types\StringType
            line_version_status:    Doctrine\DBAL\Types\StringType
            calendar_type:          Doctrine\DBAL\Types\StringType
        connections:
            %endiv_database_connection%:
                driver:         %endiv_database_driver%
                host:           %endiv_database_host%
                port:           %endiv_database_port%
                dbname:         %endiv_database_name%
                user:           %endiv_database_user%
                password:       %endiv_database_password%
                charset:        UTF8
                mapping_types:
                    calendar_operator:      string
                    line_version_status:    string
                    calendar_type:          string
                    _text:                  string
    orm:
        auto_generate_proxy_classes:                    %kernel.debug%
        default_entity_manager:                         default
        entity_managers:
            auto_mapping:                               true
            %endiv_database_connection%:
                connection:                             %endiv_database_connection%
                mappings:
                    TisseoEndivBundle:                  ~
        
'''

Contributing
------------

1. Vincent Passama - vincent.passama@gmail.com
2. Rodolphe Duval - rdldvl@gmail.com
3. Pierre-Yves Claitte - pierre.cl@gmail.com
