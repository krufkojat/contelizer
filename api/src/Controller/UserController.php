<?php

namespace App\Controller;

use App\Service\GoRestApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class UserController extends AbstractController
{
    public function __construct(private readonly GoRestApiService $apiService, private readonly ValidatorInterface $validator)
    {
    }

    #[Route('/', name: 'user_index')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/api/users', name: 'api_find_users', methods: ['GET'])]
    public function findUsers(Request $request): JsonResponse
    {
        $params = $request->query->all();
        $result = $this->apiService->findUsers($params);

        if (!$result || isset($result['success']) && $result['success'] === false) {
            $status = $result['code'] ?? Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->json([
                'success' => false,
                'error' => $result['error'] ?? 'Nie udało się pobrać użytkowników',
                'code' => $status
            ], $status);
        }

        return $this->json([
            'success' => true,
            'data' => $result['data'] ?? [],
            'pagination' => $result['pagination'] ?? []
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/user/edit/{id}', name: 'user_edit', methods: ['GET'])]
    public function editUser(int $id): Response
    {
        $result = $this->apiService->findUser($id);

        if (!$result || isset($result['success']) && $result['success'] === false) {
            $this->addFlash('error', 'Nie znaleziono użytkownika lub wystąpił błąd.');
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $result
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/api/users/{id}', name: 'api_update_user', methods: ['PUT'])]
    public function updateUser(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $csrfToken = $data['_csrf_token'] ?? '';

        if (!$this->isCsrfTokenValid('edit_user', $csrfToken)) {
            return new JsonResponse(['success' => false, 'error' => 'Błędny token CSRF'], Response::HTTP_FORBIDDEN);
        }

        $errors = $this->validateUserData($data);

        if (!empty($errors)) {
            return new JsonResponse([
                'success' => false,
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->apiService->updateUser($id, $data);

        if (!$result || isset($result['success']) && $result['success'] === false) {
            $status = $result['code'] ?? Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->json([
                'success' => false,
                'error' => $result['error'] ?? 'Nie udało się zaktualizować danych użytkownika',
                'code' => $status,
                'errors' => $result['errors'] ?? []
            ], $status);
        }

        return $this->json([
            'success' => true,
            'data' => $result
        ]);
    }

    private function validateUserData(array $data): array
    {
        $constraints = new Assert\Collection([
            'name' => [
                new Assert\NotBlank(['message' => 'Imię i nazwisko nie może być puste']),
                new Assert\Length([
                    'min' => 3,
                    'max' => 50,
                    'minMessage' => 'Imię i nazwisko musi zawierać co najmniej {{ limit }} znaki',
                    'maxMessage' => 'Imię i nazwisko może zawierać maksymalnie {{ limit }} znaków'
                ]),
            ],
            'email' => [
                new Assert\NotBlank(['message' => 'Email nie może być pusty']),
                new Assert\Email(['message' => 'Podaj poprawny adres email']),
            ],
            'gender' => [
                new Assert\NotBlank(['message' => 'Płeć jest wymagana']),
                new Assert\Choice([
                    'choices' => ['male', 'female'],
                    'message' => 'Płeć musi być "male" lub "female"'
                ]),
            ],
            'status' => [
                new Assert\NotBlank(['message' => 'Status jest wymagany']),
                new Assert\Choice([
                    'choices' => ['active', 'inactive'],
                    'message' => 'Status musi być "active" lub "inactive"'
                ]),
            ],
            '_csrf_token' => new Assert\Optional()
        ]);

        $violations = $this->validator->validate($data, $constraints);
        $errors = [];

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
        }

        return $errors;
    }
}
