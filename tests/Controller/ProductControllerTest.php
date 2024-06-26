<?php

namespace App\Test\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ProductRepository $repository;
    private string $path = '/market/place/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Product::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Product index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'product[idUser]' => 'Testing',
            'product[name]' => 'Testing',
            'product[descreption]' => 'Testing',
            'product[isDeleted]' => 'Testing',
            'product[price]' => 'Testing',
            'product[quantity]' => 'Testing',
            'product[state]' => 'Testing',
            'product[timestamp]' => 'Testing',
            'product[type]' => 'Testing',
            'product[category]' => 'Testing',
        ]);

        self::assertResponseRedirects('/market/place/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Product();
        $fixture->setIdUser('My Title');
        $fixture->setName('My Title');
        $fixture->setDescreption('My Title');
        $fixture->setIsDeleted('My Title');
        $fixture->setPrice('My Title');
        $fixture->setQuantity('My Title');
        $fixture->setState('My Title');
        $fixture->setTimestamp('My Title');
        $fixture->setType('My Title');
        $fixture->setCategory('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Product');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Product();
        $fixture->setIdUser('My Title');
        $fixture->setName('My Title');
        $fixture->setDescreption('My Title');
        $fixture->setIsDeleted('My Title');
        $fixture->setPrice('My Title');
        $fixture->setQuantity('My Title');
        $fixture->setState('My Title');
        $fixture->setTimestamp('My Title');
        $fixture->setType('My Title');
        $fixture->setCategory('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'product[idUser]' => 'Something New',
            'product[name]' => 'Something New',
            'product[descreption]' => 'Something New',
            'product[isDeleted]' => 'Something New',
            'product[price]' => 'Something New',
            'product[quantity]' => 'Something New',
            'product[state]' => 'Something New',
            'product[timestamp]' => 'Something New',
            'product[type]' => 'Something New',
            'product[category]' => 'Something New',
        ]);

        self::assertResponseRedirects('/market/place/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getIdUser());
        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getDescreption());
        self::assertSame('Something New', $fixture[0]->getIsDeleted());
        self::assertSame('Something New', $fixture[0]->getPrice());
        self::assertSame('Something New', $fixture[0]->getQuantity());
        self::assertSame('Something New', $fixture[0]->getState());
        self::assertSame('Something New', $fixture[0]->getTimestamp());
        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getCategory());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Product();
        $fixture->setIdUser('My Title');
        $fixture->setName('My Title');
        $fixture->setDescreption('My Title');
        $fixture->setIsDeleted('My Title');
        $fixture->setPrice('My Title');
        $fixture->setQuantity('My Title');
        $fixture->setState('My Title');
        $fixture->setTimestamp('My Title');
        $fixture->setType('My Title');
        $fixture->setCategory('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/market/place/');
    }
}
