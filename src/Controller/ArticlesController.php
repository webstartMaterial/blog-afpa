<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\ArticlesType;
use App\Form\CommentType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends AbstractController
{

    // Model REST
    // je récupère tous les articles
    #[Route('/articles', name: 'app_articles')]
    public function index(): Response
    {

        return $this->render('articles/index.html.twig', [
            'controller_name' => 'ArticlesController',
        ]);
    }

    #[Route('/article/{id}/delete', name: 'delete_article', requirements: ['id' => '\d+'])]
    public function deleteArticle(EntityManagerInterface $entityManager, string $id, Request $request): Response {

        // si j'ai un post
        // je récupère le paramètre POST ID
        $id = $request->get('id');
        $article = $entityManager->getRepository(Articles::class)->find($id);

        if($picture = $article->getPicture()) { // si j'ai une image, je la supprimer lors de la suppression de l'article
            @unlink('./images/articles/' . $picture);
        }

        $entityManager->remove($article);
        $entityManager->flush();

        // rediriger vers la page d'accueil avec un msg de confirmation

        $this->addFlash('confirmation', 'L\'article a bien été supprimé !');
        return $this->redirectToRoute('app_home');

    }   

    // route multiple
    // je récupère un article
    #[Route('/article/{id}', name: 'show_article_by_id', requirements: ['id' => '\d+'])]
    public function showArticle(EntityManagerInterface $entityManager, string $id, Request $request): Response
    {

        // récupérer l'article en bdd avec l'id de mon article
        // comment récupérer l'id (qui est param dans l'url)
        // je récupère le paramètre id via l'argument $id

        $article = $entityManager->getRepository(Articles::class)->findBy([ "id" => $id ])[0];

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $comment->setArticle($article);
            $comment->setDate(new \DateTime);
            $comment->setUser($this->getUser());
            $comment->setIsValid(false);

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('confirmation', 'Votre commentaire sera examiné sous 24h00');

        }

        // récupérer tous les commentaires
        $comments = $entityManager->getRepository(Comment::class)->findAllValidComments($id);


        // récupères moi en BDD
        // Les trois articles les plus récents liés à cette catégory
        // différent de l'article en cours
        // => on va devoir coder la requête dans ArticlesRepository
        $relatedArticles = $entityManager->getRepository(Articles::class)->findLastThreeRelatedArticles($article->getCategory(), $id);

        return $this->render('articles/article.html.twig', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'form_comment' => $form,
            'comments' => $comments
        ]);
    }

    /**
     * CETTE MÉTHODE PERMET DE MODIFIER UN ARTICLE
     */
    #[Route('/article/{id}/modify', name: 'modify_article', requirements: ['id' => '\d+'])]
    public function modifyArticle(EntityManagerInterface $entityManager, string $id, Request $request) {

        // faut récupérer l'article en BDD qui à l'id $id
        // ensuite créer le formulaire via ArticleType
        // render la page articles/article-modify.html.twig
        // faut render le formulaire dans cette page

        $article = $entityManager->getRepository(Articles::class)->find($id); // récupère l'article en BDD
        $form = $this->createForm(ArticlesType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            if($file = $article->getPosterFile()) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension(); // je formate le nom
                $file->move('./images/articles/', $fileName); // je le copie sur le serveur

                $article->setPicture($fileName);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('confirmation', 'Votre article a bien été modifié en BDD');
            return $this->redirectToRoute('app_home');


        }

        return $this->render('articles/modify.html.twig', [
            'articles_form' => $form->createView(),
            'article' => $article
        ]);

    }

    /**
     * Cette méthode permet d'afficher tous les articles liés à une catégorie
     */
    #[Route('/articles/{id}', name: 'show_articles_by_category', requirements: ['id' => '\d+'])]
    public function showArticleByCategory(EntityManagerInterface $entityManager, string $id): Response
    {

        // récupérer tous les articles liés à l'id catégorie récupéré sur la root
        // comment récupérer l'id de la catégorie

        $articles = $entityManager->getRepository(Articles::class)->findBy(["category" => $id ]);
        $category = $entityManager->getRepository(Category::class)->find($id);

        return $this->render('articles/index.html.twig', [
            'listArticles' => $articles,
            'category' => $category->getName()
        ]);
    }

    /**
     * Cette méthode permet de créer un article
     */
    #[Route('/article/new', name: 'create_article')]
    public function createArticle(EntityManagerInterface $entityManager, Request $request): Response
    {

        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            // dd($form->getData());

            // si j'ai une catégorie de rentrée dans le formulaire
                // je la créer
                // et je set cette catégorie créée en BDD dans mon article
            
            if(!is_null($form->get('newCategory')->getData()) && !empty($form->get('newCategory')->getData())) {

                $category = new Category();
                $category->setName($form->get('newCategory')->getData());
                $category->setDescription("");

                $entityManager->persist($category);
                $article->setCategory($category);

            }
            
            // sinon je fais ce qu'il y a en dessous

            if($file = $article->getPosterFile()) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension(); // je formate le nom
                $file->move('./images/articles/', $fileName); // je le copie sur le serveur

                $article->setPicture($fileName);
            }

            // 1ere solution
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('confirmation', 'L\'article a bien été ajouté !');
            return $this->redirectToRoute('app_home');


        }

        return $this->render('articles/new.html.twig', [
            'add_article_form' => $form->createView()
        ]);

    }


    #[Route('/delete', name: 'delete')]
    public function delete(EntityManagerInterface $entityManager, Request $request): Response
    {

        // si j'ai un post
        // je récupère le paramètre POST ID
        $id = $request->get('id');
        $article = $entityManager->getRepository(Articles::class)->find($id);

        if($picture = $article->getPicture()) { // si j'ai une image, je la supprimer lors de la suppression de l'article
            @unlink('./images/articles/' . $picture);
        }

        $entityManager->remove($article);
        $entityManager->flush();

        return new Response(Response::HTTP_OK);

    }

}
