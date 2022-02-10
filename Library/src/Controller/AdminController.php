<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Form\BookType;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\RegistrationType;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function users(UserRepository $repo): Response
    {
        $users= $repo->findAll();
        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }
    /**
     * @Route("/admin/users/{id}", name="delete_user")
     */
    public function delete_user(Request $request,$id){
        $user= $this->getDoctrine()->getRepository(User::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
        $response = new Response();
        $response->send();
        return $this->redirectToRoute('admin_users');

    }

    /**
     * @Route("/admin/users/edit/{id}" , name="edit_user" )
     * Method({"GET","POST"})
     */

     public function edit_user(Request $request, $id,UserPasswordEncoderInterface $encoder){
        $user = new User();
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
  
        $form = $this->createForm(RegistrationType::class,$user);
  
        $form->handleRequest($request);
  
        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash); 
          $user->setConfirmPassword($user->getPassword());
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->flush();
  
          return $this->redirectToRoute('admin_users');
        }
  
        return $this->render('admin/edit.html.twig', [
          'form_user' => $form->createView()]
        );
     }

     /**
     * @Route("/admin/books", name="admin_books")
     */
    public function books(BookRepository $repo): Response
    {
        $books= $repo->findAll();
        return $this->render('admin/books/books.html.twig', [
            'books' => $books,
        ]);
    }
   
    /**
     * @Route("/admin/books/edit/{id}" , name="edit_book" )
     * Method({"GET","POST"})
     */

     public function edit_book(Request $request, $id){
        $book = new Book();
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
  
        $form = $this->createForm(BookType::class,$book);
  
        $users= $book->getUsers();
        foreach($users as $user){
            $book->removeUser($user);
        }
        $book->__construct();
        $form->handleRequest($request);
  
        if($form->isSubmitted() && $form->isValid()) {
             
          
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->flush();
  
          return $this->redirectToRoute('admin_books');
        }
  
        return $this->render('admin/books/edit.html.twig', [
          'form_book' => $form->createView()]
        );
     }


     /**
      * @Route("admin/books/add", name="books_add")
      */
      public function add_book(Request $request, EntityManagerInterface $manager){
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
         $book->__construct();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $manager->persist($book);
            $manager->flush();

            return $this->redirectToRoute('admin_books');
        }

        return $this->render('admin/books/new_book.html.twig', [
            'form_book' => $form->createView()
        ]);
    }
     /**
     * @Route("/admin/books/{id}", name="delete_book")
     */
    public function delete_book(Request $request,$id){
        $book= $this->getDoctrine()->getRepository(Book::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($book);
        $entityManager->flush();
        $response = new Response();
        $response->send();
        return $this->redirectToRoute('admin_books');

    }
    /**
     * @Route("/admin/books/category/add" , name="add_category" )
     */
    public function add_category(Request $request, EntityManagerInterface $manager){
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute('admin_books');
        }

        return $this->render('admin/books/new_category.html.twig', [
            'form_category' => $form->createView()
        ]);
    }
}
