<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Booking;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/booking', name: 'app_booking')]
class BookingController extends AbstractController
{
    #[Route('/all', name: 'booking_all', methods: 'GET')]
    public function fetchAll(BookingRepository $bookingRepository): JsonResponse
    {
        $bookings = $bookingRepository->findAll();

        return $this->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    #[Route('/create', name: 'booking_create', methods: 'POST')]
    public function createBooking(EntityManagerInterface $entityManager, Request $request, BookingRepository $bookingRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $isAvailable = $this->checkBookingAvailability($data['startDate'], $data['endDate']);

        if (!$isAvailable) {
            // Return error response if booking is not available
            return new Response('Booking not available for the requested period.', Response::HTTP_BAD_REQUEST);
        }

        $booking = new Booking();

        $booking->setEmail($data["email"]);

        $startDate = \DateTime::createFromFormat('Y-m-d', $data["startDate"]);
        $booking->setStartDate($startDate);
        $endDate = \DateTime::createFromFormat('Y-m-d', $data["endDate"]);
        $booking->setEndDate($endDate);

        $booking->setStatus("active");
        $booking->setBookId($data["bookId"]);

        $entityManager->persist($booking);

        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => $booking,
        ]);
    }
}
