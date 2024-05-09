<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/book', name: 'app_book')]
class BookController extends AbstractController
{
    #[Route('/all', name: 'book_all', methods: 'GET')]
    public function fetchAll(BookRepository $bookRepository): JsonResponse
    {
        $books = $bookRepository->findAll();

        return $this->json($books);
    }

    #[Route('/filter', name: 'book_filter', methods: ['GET'])]
    public function filterBook(Request $request, BookRepository $bookRepository): JsonResponse
    {
        $title = $request->query->get('title');
        $category = $request->query->get('category');
        $publishedAt = $request->query->get('publishedAt');


        // Build query parameters array
        $queryParams = [];
        if ($title) {
            $queryParams['title'] = '%' . $title . '%'; // Partial match for title
        }
        if ($category) {
            $queryParams['category'] = '%' . $category . '%'; // Partial match for genre
        }
        if ($publishedAt) {
            $queryParams['publishedAt'] = '%' . $publishedAt . '%' ; // Partial match for publication year
        }

        // Fetch books based on query parameters
        $books = $bookRepository->findByPartial($queryParams);

        return $this->json($books);
    }
}
