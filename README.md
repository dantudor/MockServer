MockServer
==========
[![Build Status](https://secure.travis-ci.org/dantudor/MockServer.png)](http://travis-ci.org/dantudor/MockServer)

Introduction
---

Use MockServer to mock your third party API responses at source and not service level.

Installation
---

To install via composer add ``dantudor/mock-server`` as a dependency in your ``composer.json`` file:

    {
        "require": {
            "dantudor/mock-server": "dev/master"
        }
    }

Once installed register the ``MockServerBundle`` in the ``AppKernel`` for your dev and test environments:

    ...
    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        $bundles[] = new MockServer\MockServerBundle();
    }
    ...


Setup
---
Run the install command to create a new MockServer instance in your project:
    
    app/console mock:server:install --name <name>

This will create the following new configuration in your root symfony directory:

    app/MockKernel.php
    app/mock/<name>/config.yml
    app/mock/<name>/parameters.yml
    app/mock/<name>/routing.yml
    app/mock/<name>/security.yml
    
You will now need to create a new controller to be used by the Mock Server. Generate a new mocking bundle, but don't register then in the AppKernel and or default Routing as they'll need to be registered only in your MockKernel and mock routing.

    app/console bundle:generate --namespace Mock/FacebookBundle

Edit app/MockKernel.php and add the following bundle in the ``MockKernel::registerBundles()`` method:
 
    new Mock\FacebookBundle\MockFacebookBundle(),

Import the bundle's routing resource in the ``app/mock/routing.yml`` file:

    MockFacebookBundle:
        resource: "@MockFacebookBundle/Controller/"
        type:     annotation
        prefix:   /


Your new Mock Server instance is now ready to use.
   
