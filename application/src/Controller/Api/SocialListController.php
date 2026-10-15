<?php

namespace App\Controller\Api;

use App\Entity\Dto\ApiErrorResponseDto;
use App\Entity\Dto\ApiSuccessResponseDto;
use App\Services\RknSocialListApiService;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class SocialListController extends AbstractController
{
    /**
     * @OA\Get(
     *     tags={"SocialList"},
     *     description="Список социально значимых ресурсов",
     *
     *     @OA\Parameter(name="token", required=true, in="query", @OA\Schema(type="string")),
     *
     *     @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(
     *              @OA\Property (property="status", type="integer", example=200),
     *              @OA\Property (property="error", type="string", example=""),
     *              @OA\Property (property="data", type="array", @OA\Items(
     *                  @OA\Property (property="update_time", type="string", example="2022-02-17T13:10:00+00:00"),
     *                  @OA\Property (property="social_list", type="array", @OA\Items(
     *                      @OA\Property (property="name", type="string", example="Социальная сеть ВКонтакте"),
     *                      @OA\Property (property="domains", type="array", @OA\Items(
     *                          @OA\Property (property="0", type="string", example="vk.com"),
     *                      )),
     *                  )),
     *              )),
     *          ),
     *      )),
     *
     *     @OA\Response(
     *      response=404,
     *      description="Error (Ответ всё равно будет с кодом 200)",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(
     *              @OA\Property (property="status", type="integer", example=404),
     *              @OA\Property (property="error", type="string", example="Не найден список социально значимых ресурсов"),
     *              @OA\Property (property="data", type="string", example="[]")
     *          ),
     *      )),
     * )
     */
    #[Route('/api/social-list', name: 'api-social_list', methods: 'get')]
    public function index(Request $request, RknSocialListApiService $rknSocialListApiService): Response
    {
        $token = $this->getParameter('api.token');
        if ($request->query->get('token') !== $token) {
            $responseArray = (new ApiErrorResponseDto(
                'Access denied',
                403
            ))->toArray();

            return $this->json($responseArray);
        }

        try {
            $data = $rknSocialListApiService->getSocialListFormatted();

            return $this->json(
                (new ApiSuccessResponseDto($data))->toArray()
            );
        } catch (DomainException $exception) {
            $responseArray = (new ApiErrorResponseDto(
                $exception->getMessage(),
                404
            ))->toArray();

            return $this->json($responseArray);
        }
    }
}
