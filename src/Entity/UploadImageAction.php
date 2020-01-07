<?php

namespace App\Entity;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class UploadImageAction
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UploadImageAction constructor.
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $entityManager)
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request)
    {
        // Create new image instance
        $image = new Image();
        // Validate the form
        $form = $this->formFactory->create(null, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the new Image entity
            $this->entityManager->persist($image);
            $this->entityManager->flush();

            // due to binary of file we do not want for the moment
            $image->setFile(null);

            return $image;
        }

        // Uploading done for us in background with VichUploader

        // throw on validation exception, that means something went wrong during form validation
        throw new ValidationException(
            $this->validator->validate($image)
        );
    }
}