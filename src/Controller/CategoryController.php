<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/new', name: 'create_category')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $category = $form->getData();
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('confirmation', 'Votre catégorie a bien été insérée');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'category_form' => $form
        ]);

    }
}
