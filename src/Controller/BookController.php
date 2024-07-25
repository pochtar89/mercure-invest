<?php

namespace App\Controller;

use App\Entity\Book;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    #[Route('/book/form', name: 'form', methods: ['GET'])]
    public function form()
    {
        return $this->render('book/form.html.twig');
    }

    #[Route('/book/create', name: 'create', methods: ['GET'])]
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $book = new Book();
        $book->name = $request->get('name');
        $manager->persist($book);
        $manager->flush();
        return $this->redirectToRoute('form');
    }

    #[Route('/book/subscribe', name: 'book_subscribe', methods: ['GET'])]
    public function subscribe()
    {
        return $this->render('book/subscribe.html.twig', ['topic' => 'https://sibers-mercure.com/books']);
    }
}
