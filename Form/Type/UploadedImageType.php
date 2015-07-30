<?php
namespace Redking\Bundle\UploadBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Vich\UploaderBundle\Form\Type\VichImageType;

class UploadedImageType extends VichImageType
{
    protected $resized = [];

    /**
     * Defini les options de resized
     * 
     * @param array $resized
     */
    public function setResizedConf($resized = [])
    {
        $this->resized = $resized;
    }

    /**
     * Récupère les dimensions de thumb
     * @return [type] [description]
     */
    protected function getThumbDimensions()
    {
        if (isset($this->resized['thumb'])) {
            $width = isset($this->resized['thumb']['width']) ? $this->resized['thumb']['width'] : null;
            $height = isset($this->resized['thumb']['height']) ? $this->resized['thumb']['height'] : null;
        } else {
            $height = 50;
            $width = null;
        }
        return ["width" => $width, "height" => $height];
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['object'] = $form->getParent()->getData();
        $view->vars['show_download_link'] = $options['download_link'];

        if ($view->vars['object']) {
            $view->vars['download_uri'] = $this->storage->resolveUri($form->getParent()->getData(), $form->getName());
        }
        $view->vars['dimensions'] = $this->getThumbDimensions();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'redking_image';
    }
}
