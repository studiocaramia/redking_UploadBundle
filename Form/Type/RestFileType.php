<?php
namespace Redking\Bundle\UploadBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class RestFileType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                // Récupération de la donnée qui doit être en base64
                $data = $event->getData();
                
                // Décodage de $data pour en faire un fichier upload
                if (!is_null($data) && $data != '') {
                    $content = base64_decode($data);
                    
                    $path = tempnam("/tmp", "upload_redking");

                    file_put_contents($path, $content);
                    // Création du file upload
                    $uploaded_file = new UploadedFile($path, basename($path), null, strlen($content), null, true);

                    $event->setData($uploaded_file);
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'redking_file_rest';
    }

    public function getParent()
    {
        return 'file';
    }
}
