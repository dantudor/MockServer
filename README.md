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


Basic Usage
---
Consider you have a class in your codebase that makes the following request to facebook.com:

    GET http://api.facebook.com/users/dantudor   

You need to mock the response for this request to return a json string that describes the user.
Create a new Controller in your MockFacebookBundle and associate an action with the route /users/{name} that responds with the JSON your application would expect.
    
    <?php
    // src/Mock/FacebookBundle/Controller/UserController.php
    
    namespace Mock\FacebookBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
    
    class DefaultController extends Controller
    {
        /**
         * @Route("/users/{name}")
         * @Method("GET")
         * @Template()
         */
        public function indexAction($name)
        {
            $data = array(
                'name' => $name,
                'email' => 'dtudor01@gmail.com',
                'location' => 'Location'
            );
            
            return new JsonResponse($data, 200);
        }
    }

Tell your test that you will be using the mock server in place of facebook and define a domain and port to listen on.
I do this in the BeforeScenario event of my behat FeatureContext:

    /**
     * Before
     *
     * @param ScenarioEvent $event
     *
     * @BeforeScenario
     */
    public function before(ScenarioEvent $event)
    {
        /** @var $serverManager \MockServer\Manager\ServerManager */
        $this->mocker = $this->kernel->getContainer()->get('mock_server.manager');
        $this->mocker->create('name', 8080, '127.0.0.1');
    }

NOTE: The first parameter of the create method of the mocker object is the name you gave your instance in the setup steps above.

The final step is to tell your application that facebook is now responding on ``http://127.0.0.1:8080``. If you have defined the facebook api domain in your config.yml files then you will need to update your config_test.yml file to use the mock server ip and port:

    facebook_wrapper:
        base_uri:  http://127.0.0.1:8888




