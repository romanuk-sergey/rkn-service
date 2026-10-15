<?php

namespace App\Controller\Admin;

use App\Entity\PrivateKey;
use App\Form\PrivateKeyType;
use App\Repository\PrivateKeyRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PrivateKeyController extends AbstractCrudController
{

    public function __construct(
        private PrivateKeyRepository   $privateKeyRepository,
        private EntityManagerInterface $entityManager,
        private AdminUrlGenerator      $adminUrlGenerator,
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return PrivateKey::class;
    }

    public function privateKey()
    {
        $entity = $this->privateKeyRepository->findOneBy([]);

        $form = $this->createForm(PrivateKeyType::class, null, ['action' => $this->generateUrl('private-key-upload')]);

        return $this->render('admin/private-key.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/private-key/upload', name: 'private-key-upload', methods: 'post')]
    public function uploadPrivateKey(Request $request, SluggerInterface $slugger)
    {
        $form = $this->createForm(PrivateKeyType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            /** @var UploadedFile $personalDataFile */
            $personalDataFile = $form->get('path')->getData();

            if ($personalDataFile) {
                $originalFilename = pathinfo($personalDataFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $personalDataSafeFilename = $slugger->slug($originalFilename);
                $personalDataFilename = $personalDataSafeFilename . '.' . $personalDataFile->getClientOriginalExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $password = $request->request->all()['private_key']['password'];
                    $personalDataFile->move(
                        $this->getParameter('secret.directory'),
                        $personalDataFilename
                    );

                    $privateKeyFilePath = $this->getParameter('secret.directory') . '/KVS.private.key.pem';

                    $this->extractPrivateKey($personalDataFilename, $privateKeyFilePath, $password);

                    $privateKey = new PrivateKey();
                    $privateKey->setPath($privateKeyFilePath);
                    $privateKey->setPassword($password);
                    $this->entityManager->persist($privateKey);
                    $this->entityManager->flush();

                    $this->addFlash('info', 'Электронная подпись успешно загружена');
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Ошибка загрузки электронной подписи');
                }
            }
        }

        return $this->redirect($this->adminUrlGenerator
            ->setController(PrivateKeyController::class)
            ->setAction('privateKey')
            ->generateUrl());
    }

    #[Route('/private-key/delete', name: 'private-key-delete', methods: 'get')]
    public function deletePrivateKey()
    {
        $entity = $this->privateKeyRepository->findOneBy([]);
        if ($entity) {
            unlink($entity->getPath());
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            $this->addFlash('info', 'Электронная подпись удалена');
        } else {
            $this->addFlash('warning', 'Электронная подпись не найдена');
        }

        return $this->redirect($this->adminUrlGenerator
            ->setController(PrivateKeyController::class)
            ->setAction('privateKey')
            ->generateUrl());
    }

    /**
     * @param $personalDataFilename
     * @param $privateKeyFilePath
     * @param $password
     */
    private function extractPrivateKey($personalDataFilename, $privateKeyFilePath, $password): void
    {
        $personalDataFilePath = $this->getParameter('secret.directory') . '/' . $personalDataFilename;
        exec('openssl pkcs12 -in ' . $personalDataFilePath . ' -out ' . $privateKeyFilePath . ' -name "alias" -passin pass:' . $password . ' -passout pass:' . $password);
        unlink($personalDataFilePath);
    }
}
