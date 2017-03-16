<?php
namespace Tests\AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Client;

/**
 * Class DefaultControllerTest
 * 
 * @package Tests\AppBundle\Controller
 */
class DefaultControllerTest extends WebTestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Setup entity manager for class
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Tests the "all words" method
     */
    public function testAllWords()
    {
        $client = static::createClient();
        $client->request('GET', '/allwords');

        $this->doBasicTests($client);

        // compares repository and response counters
        $this->assertSame(
            count(json_decode($client->getResponse()->getContent())), 
            count($this->em->getRepository('AppBundle:Word')->findAll())
        );
    }

    /**
     * Tests the "top10" method
     */
    public function testTop10() {
        $client = static::createClient();
        $client->request('GET', '/top10');
        
        $this->doBasicTests($client);
        
        // compares repository and response counters
        $this->assertSame(
            count($this->em->getRepository('AppBundle:LawWord')->findTop10()),
            count(json_decode($client->getResponse()->getContent()))
        );
    }

    /**
     * Do some common basic tests (response status, type, etc).
     * 
     * @param Client $client
     */
    protected function doBasicTests(Client $client) {
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }
}
