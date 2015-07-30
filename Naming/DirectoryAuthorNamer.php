<?php
/**
 * Namer déduit de l'objet
 */
namespace Redking\Bundle\UploadBundle\Naming;

use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;

class DirectoryAuthorNamer implements DirectoryNamerInterface
{
    /**
     * Construit le nom du répertoire à partir de l'auteur de l'objet
     * @param  [type]          $object  [description]
     * @param  PropertyMapping $mapping [description]
     * @return [type]                   [description]
     */
    public function directoryName($object, PropertyMapping $mapping)
    {
        return $object->getAuthor()->getId();
    }

}
