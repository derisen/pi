<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Channel;
use AppBundle\DataFixtures\ORM\LoadChannel;
use Nines\UserBundle\DataFixtures\ORM\LoadUser;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class ChannelControllerTest extends BaseTestCase
{

    protected function getFixtures() {
        return [
            LoadUser::class,
            LoadChannel::class
        ];
    }
    
    public function testAnonIndex() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/channel/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }
    
    public function testUserIndex() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/channel/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }
    
    public function testAdminIndex() {
        $client = $this->makeClient(LoadUser::ADMIN);
        $crawler = $client->request('GET', '/channel/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('New')->count());
    }
    
    public function testAnonShow() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/channel/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }
    
    public function testUserShow() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/channel/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->selectLink('Delete')->count());
    }
    
    public function testAdminShow() {
        $client = $this->makeClient(LoadUser::ADMIN);
        $crawler = $client->request('GET', '/channel/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Edit')->count());
        $this->assertEquals(1, $crawler->selectLink('Delete')->count());
    }
    public function testAnonEdit() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/channel/1/edit');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }
    
    public function testUserEdit() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/channel/1/edit');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
    
    public function testAdminEdit() {
        $client = $this->makeClient(LoadUser::ADMIN);
        $formCrawler = $client->request('GET', '/channel/1/edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );        
        $form = $formCrawler->selectButton('Update')->form([
            // DO STUFF HERE.
            // 'channels[FIELDNAME]' => 'FIELDVALUE',
        ]);
        
        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect('/channel/1'));
        $responseCrawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // $this->assertEquals(1, $responseCrawler->filter('td:contains("FIELDVALUE")')->count());
    }
    
    public function testAnonNew() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/channel/new');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }
    
    public function testUserNew() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/channel/new');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAdminNew() {
        $client = $this->makeClient(LoadUser::ADMIN);
        $formCrawler = $client->request('GET', '/channel/new');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );        
        $form = $formCrawler->selectButton('Create')->form([
            // DO STUFF HERE.
            // 'channels[FIELDNAME]' => 'FIELDVALUE',
        ]);
        
        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $responseCrawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // $this->assertEquals(1, $responseCrawler->filter('td:contains("FIELDVALUE")')->count());
    }
    
    public function testAnonDelete() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/channel/1/delete');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }
    
    public function testUserDelete() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/channel/1/delete');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAdminDelete() {
        $preCount = count($this->em->getRepository(Channel::class)->findAll());
        $client = $this->makeClient(LoadUser::ADMIN);
        $crawler = $client->request('GET', '/channel/1/delete');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());
        $responseCrawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $this->em->clear();
        $postCount = count($this->em->getRepository(Channel::class)->findAll());
        $this->assertEquals($preCount - 1, $postCount);
    }

}
