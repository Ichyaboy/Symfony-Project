<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('book/home.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    /**
     * @Route("/books", name="books")
     */
    public function books(BookRepository $repo): Response
    {
        $books= $repo->findAll();
        return $this->render('user/browse_books.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @Route("/books/{user_id}/{id}", name="add_book_user")
     */
    public function add_to_list(BookRepository $repo,$id,$user_id): Response
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
        $user = $this->getDoctrine()->getRepository(User::class)->find($user_id);
        $user->addBook($book);
        dd($book);
        dd($user);
        return $this->redirectToRoute('books');
    }

}
