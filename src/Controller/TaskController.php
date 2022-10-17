<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\Validator\Constraints as Assert;



class TaskController extends AbstractController
{
    #[Route('task', name: 'app_task')]
    function new(Request $request, PersistenceManagerRegistry $doctrine)
    {
        $form = $this->createFormBuilder()
            ->add('libelle', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Créer Tâche'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $task = new Task();
            $task->setLibelle($form->getData()['libelle']);
            $task->setDone(false);
            //... perform some action, such as saving the task to the database
            //for example, if Task is a Doctrine entity, save it!
            $entityManager = $doctrine->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute('task_success');
        }
        return $this->render('articles/index.html.twig', array(
            'form' => $form->createView(), 'controller_name' => 'TaskController',
        ));
    }
    /**
     * @Route("/task_success", name="task_success")
     *  */
    public function affiche_result(Request $request)
    {
        return $this->render('task/success.html.twig', array(
            'task' => 'La tâche a bien été enregistrée',
        ));
    }
}
