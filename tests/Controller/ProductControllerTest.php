<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Form;

class ProductControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private string $path = '/product/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Product index');

        $formCrawler = $crawler->filter('select[id="stock"]')->closest('form');
        $form = new Form($formCrawler->getNode(0), $formCrawler->getUri());
        $form->setValues([
            'stock' => 5,
        ]);
        $this->client->submit($form);
        self::assertResponseStatusCodeSame(200);
    }

    public function testSubmitInvalidStockValue(): void
    {
        $crawler = $this->client->request('GET', $this->path.'?stock=10');
        self::assertResponseStatusCodeSame(422);
        self::assertPageTitleContains('Product index');
    }
}
