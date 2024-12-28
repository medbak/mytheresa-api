<?php

declare(strict_types=1);
// tests/BaseTestCase.php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class BaseTestCase extends KernelTestCase
{
    protected ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Begin transaction
        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        // Rollback transaction
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }

        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
