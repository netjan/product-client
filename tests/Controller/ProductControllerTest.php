<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private string $path = '/product';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path.'/');

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
        $this->client->request('GET', $this->path.'/?stock=10');
        self::assertResponseStatusCodeSame(422);
        self::assertPageTitleContains('Product index');
    }

    public function testNew(): void
    {
        $crawler = $this->client->request('GET', $this->path.'/new');

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('New Product');

        $formCrawler = $crawler->filter('form[name="product"]');
        $form = new Form($formCrawler->getNode(0), $formCrawler->getUri());
        $form->setValues([
            'product' => [
                'name' => 'Name 1',
                'amount' => 5,
            ],
        ]);
        $this->client->submit($form);
        self::assertResponseStatusCodeSame(303);
    }

    public function testShow(): void
    {
        $this->client->request('GET', $this->path.'/1');

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Product');
    }

    public function testShouldThrowNotFoundHttpExceptionWhenGetInvalidId404(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->client->request('GET', $this->path.'/404');
    }

    public function testShouldThrowNotFoundHttpExceptionWhenGetInvalidId500(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->client->request('GET', $this->path.'/500');
    }

    public function testShouldThrowNotFoundHttpExceptionWhenGetOther(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->client->request('GET', $this->path.'/non-existant');
    }

    public function testEdit(): void
    {
        $crawler = $this->client->request('GET', $this->path.'/1/edit');

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Edit Product');

        $formCrawler = $crawler->filter('form[name="product"]');
        $form = new Form($formCrawler->getNode(0), $formCrawler->getUri());
        $form->setValues([
            'product' => [
                'name' => 'Name 1',
                'amount' => 5,
            ],
        ]);
        $this->client->submit($form);
        self::assertResponseStatusCodeSame(303);
    }

    public function testDelete(): void
    {
        $this->client->request('GET', $this->path.'/1');
        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Product');

        $this->client->submitForm('Delete');
        self::assertResponseStatusCodeSame(303);
    }
}
