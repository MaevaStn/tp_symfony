<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Articles;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ArticlesRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Categorie;



class ArticlesController extends AbstractController
{
    #[Route('/articles', name: 'app_articles')]
    public function index(ArticlesRepository $articlesRepository): Response
    {
        $article = $articlesRepository->findAll();
        dump($article);
        return $this->render('articles/index.html.twig', [
            'controller_name' => 'ArticlesController',
            'articles' => $article,
        ]);
    }

    #[Route('/create', name: 'create_article')]
    function new(Request $request, PersistenceManagerRegistry $doctrine)
    {
        $form = $this->createFormBuilder()
            ->add('libelle', TextType::class)
            ->add('price', TextType::class)
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'libelle',
                'choice_value' => 'id',
            ])
            ->add('save', SubmitType::class, ['label' => 'Créer article'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $article = new Articles();
            $article->setLibelle($form->getData()['libelle']);
            $article->setPrice($form->getData()['price']);
            $article->setCategorie($form->getData()['categorie']);
            //... perform some action, such as saving the task to the database
            //for example, if Task is a Doctrine entity, save it!
            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('articles_success');
        }
        return $this->render('create/index.html.twig', array(
            'form' => $form->createView(), 'controller_name' => 'ArticlesController',
        ));
    }


    #[Route('/success', name: 'articles_success')]

    public function affiche_result(Request $request)
    {
        return $this->render('articles/success.html.twig', array(
            'article' => 'La tâche a bien été enregistrée',
        ));
    }

    #[Route('/articles/{id}', name: 'product_show')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $article = $doctrine->getRepository(Articles::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        return new Response('Check out this great product: ' . $article->getLibelle());

        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }
}
