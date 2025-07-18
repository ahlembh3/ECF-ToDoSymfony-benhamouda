<?php
namespace App\Tests\Unitaire\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private Task $task;

    protected function setUp(): void
    {
        $this->task = new Task();
    }

    protected function tearDown(): void
    {
        unset($this->task);
    }

    public function testCanSetAndGetTitle(): void
    {
        $this->task->setTitle('Faire les courses');
        $this->assertSame('Faire les courses', $this->task->getTitle());
    }

    public function testCanSetAndGetDescription(): void
    {
        $description = 'Acheter du lait, du pain et des œufs';
        $this->task->setDescription($description);
        $this->assertSame($description, $this->task->getDescription());
    }

    public function testCanSetAndGetDueDate(): void
    {
        $date = new \DateTimeImmutable('+1 day');
        $this->task->setDueDate($date);
        $this->assertSame($date, $this->task->getDueDate());
    }

    public function testCanAssociateUser(): void
    {
        $user = new User();
        $this->task->setUser($user);
        $this->assertSame($user, $this->task->getUser());
    }

    public function testNewTaskHasNullId(): void
    {
        $this->assertNull($this->task->getId());
    }
}
