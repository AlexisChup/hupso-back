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

        return $this->json([
            'status' => 200,
            'message' => 'All books.',
            'data' => $books,
        ]);
    }

    #[Route('/filter', name: 'book_filter', methods: ['GET'])]
    public function filterBook(Request $request, BookRepository $bookRepository): JsonResponse
    {
        // Decode JSON data from request body
        $requestData = json_decode($request->getContent(), true);

        // Check if parameters exists in JSON data
        $title = $requestData['title'] ?? null;
        $category = $requestData['category'] ?? null;
        $publishedAt = $requestData['publishedAt'] ?? null;

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

        return $this->json([
            'status' => 200,
            'message' => 'Books fetched successfully.',
            'data' => $books,
        ]);
    }
}
