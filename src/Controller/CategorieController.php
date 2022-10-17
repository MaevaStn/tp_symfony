<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'categorie_list')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();
        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController',
            'categories' => $categories,
        ]);
    }

    #[Route('/createCategorie', name: 'create_categorie')]
    function new(Request $request, PersistenceManagerRegistry $doctrine)
    {
        $form = $this->createFormBuilder()
            ->add('libelle', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Créer catégorie'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $categorie = new Categorie();
            $categorie->setLibelle($form->getData()['libelle']);
            //... perform some action, such as saving the task to the database
            //for example, if Task is a Doctrine entity, save it!
            $entityManager = $doctrine->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();
            return $this->redirectToRoute('categorie_success');
        }
        return $this->render('createCategorie/index.html.twig', array(
            'form' => $form->createView(), 'controller_name' => 'CategorieController',
        ));
    }


    #[Route('/success', name: 'categorie_success')]

    public function affiche_result(Request $request)
    {
        return $this->render('categorie/success.html.twig', array(
            'categorie' => 'La catégorie a bien été enregistrée',
        ));
    }

    #[Route('/categorie/{id}', name: 'categorie_show')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $categorie = $doctrine->getRepository(Categorie::class)->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        return new Response('Check out this great product: ' . $categorie->getLibelle());

        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }
}
