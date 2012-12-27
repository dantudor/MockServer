MockServer
==========

[![Build Status](https://secure.travis-ci.org/dantudor/MockServer.png)](http://travis-ci.org/dantudor/MockServer)

Contributions and feedback welcome.

Introduction
---

MockServer is a tool to mock third-party API responses in your Symfony2 functional tests. 
Using MockServer allows you to easily stub your API responses and manage those stubs within 
your codebase in a meaningful structure and format.


Installation
---

To install via composer add ``dantudor/mock-server`` as a dependency in your ``composer.json`` file:

    {
        "require": {
            "dantudor/mock-server": "dev/master"
        }
    }


Setup
---

Generate a new mocking bundle

    app/console mock:bundle:generate --namespace Acme/MockBundle
    
    
Usage
---

For now you can see a working example in https://github.com/dantudor/MockServerExample which demonstrates 
partial mocking of the GitHub API.


Outstanding Development
---

MockServer intends to prime data into the API responses direct from the functional test.

[![githalytics.com alpha](https://cruel-carlota.pagodabox.com/1b0414e837c760de2314ffc8f6f0f62c "githalytics.com")](http://githalytics.com/dantudor/MockServer)
