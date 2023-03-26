<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookingController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly BookingRepository $bookingRepository
    ) {}

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route(
        path: '/bookings/create',
        name: 'app_bookings_create',
        methods: 'POST'
    )]
    public function index(Request $request): JsonResponse
    {
        $booking = $this->serializer->deserialize(
            $request->getContent(),
            Booking::class,
            JsonEncoder::FORMAT
        );

        $errors = $this->validator->validate($booking);

        if (count($errors) > 0) {
            return new JsonResponse((string)$errors, 422);
        }

        $bookingSuccess = $this->bookingRepository->book($booking, $this->getParameter('maxVacancies'));

        if (!$bookingSuccess) {
            return $this->json([
                'message' => 'Can not book on given dates. The vacancies are full',
            ], 403);
        }

        return $this->json([
            'message' => 'Booking successful',
        ]);
    }

    #[Route(
        path: '/bookings',
        name: 'app_bookings',
        methods: 'GET'
    )]
    public function list(): JsonResponse
    {
        return $this->json($this->bookingRepository->list());
    }
}
