<?php

namespace App\Controller;

use App\Entity\User;
use App\RequestServices\ElectronicSignatureCreator;
use App\RequestServices\RequestContentCreator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use ZipArchive;

#[IsGranted(User::ROLE_ADMIN)]
class RequestController extends AbstractController
{

    #[Route('/download', name: 'download_request')]
    public function download(RequestContentCreator $contentCreator, ElectronicSignatureCreator $creator, ParameterBagInterface $parameterBag): BinaryFileResponse|RedirectResponse
    {
        $pathContent = $contentCreator->create();
        $pathSign = $creator->create($pathContent);
        $zip_name = $parameterBag->get('kernel.project_dir') . '/public/request/' . time() . '.zip';
        $zip = new ZipArchive;
        $result = $zip->open($zip_name, ZipArchive::CREATE);
        if ($result === TRUE) {
            $zip->addFile($pathContent, 'request.xml');
            $zip->addFile($pathSign, 'sign.sig');
            $zip->close();
            return new BinaryFileResponse($zip_name, contentDisposition: ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        } else {
            $this->addFlash('notice', 'Не удалось сформировать файл для скачивания. ' . $result);
        }
        return $this->redirectToRoute('admin');
    }
}