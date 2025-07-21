<?php
namespace App\Tests\Unitaire\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{
    private $emMock;
    private $repoStub;
    private TaskService $service;

    protected function setUp(): void
    {
        // Mocks et stubs
        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->repoStub = $this->createStub(TaskRepository::class); 

        $this->service = new TaskService($this->repoStub, $this->emMock);
    }

    public function testCreateTaskPersistsAndFlushes(): void
    {
        $taskMock = $this->createMock(Task::class);
        $userMock = $this->createMock(User::class);

        $taskMock->expects($this->once())->method('setUser')->with($userMock);
        $this->emMock->expects($this->once())->method('persist')->with($taskMock);
        $this->emMock->expects($this->once())->method('flush');

        $this->service->createTask($taskMock, $userMock);
    }

    public function testUpdateTaskOnlyFlushes(): void
    {
        $task = new Task();

        $this->emMock->expects($this->once())->method('flush');

        $this->service->updateTask($task);
    }

    public function testDeleteTaskRemovesAndFlushes(): void
    {
        $task = new Task();

        $this->emMock->expects($this->once())->method('remove')->with($task);
        $this->emMock->expects($this->once())->method('flush');

        $this->service->deleteTask($task);
    }

    public function testGetTasksForUserUsesRepositoryStub(): void
    {
        $user = new User();

        // Stub: on redéfinit le comportement de la méthode findBy
        $this->repoStub
            ->method('findBy')
            ->with(['user' => $user])
            ->willReturn(['fake_task']);

        $result = $this->service->getTasksForUser($user);
        $this->assertSame(['fake_task'], $result);
    }

    public function testGetTaskByIdReturnsCorrectTask(): void
    {
        $task = new Task();

        // Stub
        $this->repoStub
            ->method('find')
            ->with(10)
            ->willReturn($task);

        $this->assertSame($task, $this->service->getTaskById(10));
    }
//couverture limits
    public function testGetTaskByIdReturnsNullIfNotFound(): void
    {
        $this->repoStub
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $this->assertNull($this->service->getTaskById(999));
    }

    public function testGetTasksForUserReturnsEmptyArray(): void
    {
        $user = new User();

        $this->repoStub
            ->method('findBy')
            ->with(['user' => $user])
            ->willReturn([]);

        $this->assertSame([], $this->service->getTasksForUser($user));
    }
//couverture erreur
    public function testCreateTaskThrowsOnFlushFailure(): void
    {
        $taskMock = $this->createMock(Task::class);
        $userMock = $this->createMock(User::class);

        $taskMock->expects($this->once())->method('setUser')->with($userMock);
        $this->emMock->expects($this->once())->method('persist')->with($taskMock);
        $this->emMock
            ->method('flush')
            ->will($this->throwException(new \Exception('Erreur Doctrine')));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erreur Doctrine');

        $this->service->createTask($taskMock, $userMock);
    }


    protected function tearDown(): void
    {
        unset($this->service, $this->emMock, $this->repoStub);
    }
}
