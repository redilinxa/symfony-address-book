<?php


namespace AppBundle\Application\Listeners;

use AppBundle\Application\Services\FileUploader;
use AppBundle\Domain\Entity\Person;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadListener
{
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    private function uploadFile($entity)
    {
        if (!$entity instanceof Person) {
            return;
        }

        $file = $entity->getImage();
        if ($file instanceof UploadedFile) {
            $fileName = $this->uploader->upload($file);
            $entity->setImage($fileName);
        } elseif ($file instanceof File) {
            $entity->setImage($file->getFilename());
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Person) {
            return;
        }

        if ($fileName = $entity->getImage()) {
            $entity->setImage(new File($this->uploader->getTargetDirectory().'/'.$fileName));
        }
    }
}