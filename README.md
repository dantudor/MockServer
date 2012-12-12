MockServer
==========

[![Build Status](https://secure.travis-ci.org/dantudor/MockServer.png)](http://travis-ci.org/dantudor/MockServer)

Introduction
---
MockServer is a tool to mock third-party API responses in your Symfony2 functional tests. 
Using MockServer allows you to easily stub your API responses and manage those stubs within 
your codebase in a meaningful structure and format.


Installation (via composer)
---
To install via composer add ``dantudor/mockfs`` as a dependency in your ``composer.json`` file:

    {
        "require": {
            "dantudor/mock-server": "dev/master"
        }
    }
    
Setup
---

Generate a new MockServerBundle

    app/console mock:bundle:generate --namespace Acme/MockBundle
    
    
Usage
---

For now you can see a working example in https://github.com/dantudor/MockServerExample which demonstrates 
partial mocking of the GitHub API.


Outstanding Development
---

MockServer intends to prime data into the API responses direct from the functional test.
