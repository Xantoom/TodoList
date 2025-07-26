<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
	#[Route('/tasks', name: 'task_list')]
	public function list(TaskRepository $taskRepository): Response
	{
		return $this->render('task/list.html.twig', [
			'tasks' => $taskRepository->findAll()
		]);
	}

	#[Route('/tasks/create', name: 'task_create')]
	public function create(Request $request, EntityManagerInterface $entityManager): Response
	{
		$task = new Task();
		$form = $this->createForm(TaskType::class, $task);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$currentUser = $this->getUser();
			if ($currentUser instanceof User) {
				$task->setUserEntity($currentUser);
			} else {
				$this->addFlash('error', 'Vous devez être connecté pour créer une tâche.');
				return $this->redirectToRoute('task_list');
			}

			$entityManager->persist($task);
			$entityManager->flush();

			$this->addFlash('success', 'La tâche a été bien été ajoutée.');

			return $this->redirectToRoute('task_list');
		}

		return $this->render('task/create.html.twig', [
			'form' => $form->createView()
		]);
	}

	#[Route('/tasks/{id}/edit', name: 'task_edit')]
	public function edit(Task $task, Request $request, EntityManagerInterface $entityManager): Response
	{
		$form = $this->createForm(TaskType::class, $task);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$currentUser = $this->getUser();
			if ($currentUser instanceof User) {
				$task->setUserEntity($currentUser);
			} else {
				$this->addFlash('error', 'Vous devez être connecté pour créer une tâche.');
				return $this->redirectToRoute('task_list');
			}

			$entityManager->flush();

			$this->addFlash('success', 'La tâche a bien été modifiée.');

			return $this->redirectToRoute('task_list');
		}

		return $this->render('task/edit.html.twig', [
			'form' => $form->createView(),
			'task' => $task,
		]);
	}

	#[Route('/tasks/{id}/toggle', name: 'task_toggle')]
	public function toggleTask(Task $task, EntityManagerInterface $entityManager): Response
	{
		$task->toggle(!$task->isDone());
		$entityManager->flush();

		$this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

		return $this->redirectToRoute('task_list');
	}

	#[Route('/tasks/{id}/delete', name: 'task_delete')]
	public function deleteTask(Task $task, EntityManagerInterface $entityManager): Response
	{
		if (!$task->getUserEntity()) {
			$this->addFlash('error', 'Vous ne pouvez pas supprimer une tâche qui n\'a pas d\'utilisateur associé.');
			return $this->redirectToRoute('task_list');
		}

		if ($task->getUserEntity() !== $this->getUser()) {
			$this->addFlash('error', 'Vous ne pouvez supprimer que vos propres tâches.');
			return $this->redirectToRoute('task_list');
		}

		$task->getUserEntity()?->removeTask($task);

		$entityManager->remove($task);
		$entityManager->flush();

		$this->addFlash('success', 'La tâche a bien été supprimée.');

		return $this->redirectToRoute('task_list');
	}
}
