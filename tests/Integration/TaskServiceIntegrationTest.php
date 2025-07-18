<?php

namespace App\Tests\Integration;

use App\Entity\Task;
use App\Entity\User;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @covers \App\Service\TaskService
 */
class TaskServiceIntegrationTest extends KernelTestCase
{
    private TaskService $taskService;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->taskService = static::getContainer()->get(TaskService::class);

        // Nettoyage avant test
        $this->entityManager->createQuery('DELETE FROM App\Entity\Task t')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User u')->execute();
    }

    public function testCreateTaskPersistsToDatabase(): void
    {
        // Arrange : créer un utilisateur et une tâche
        $user = new User();
        $user->setEmail('test@test.com');
        $user->setPassword('hashedpass');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $task = new Task();
        $task->setTitle('Integration test task');
        $task->setDescription('Check if persisted');
        $task->setPriority(2);

        // Act : appel du service
        $this->taskService->createTask($task, $user);

        // Assert : vérifier si la tâche est en base
        $taskFromDb = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'Integration test task']);
        $this->assertNotNull($taskFromDb);
        $this->assertSame('Check if persisted', $taskFromDb->getDescription());
        $this->assertSame(2, $taskFromDb->getPriority());
        $this->assertSame($user->getId(), $taskFromDb->getUser()->getId());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
