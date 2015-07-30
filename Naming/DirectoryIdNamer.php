<?php
/**
 * Namer déduit de l'objet
 */
namespace Redking\Bundle\UploadBundle\Naming;

use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;

class DirectoryIdNamer extends BaseDirectoryIdNamer implements DirectoryNamerInterface
{
    /**
     * Construit le nom du répertoire à partir de l'id de l'objet
     * @param  [type]          $object  [description]
     * @param  PropertyMapping $mapping [description]
     * @return [type]                   [description]
     */
    public function directoryName($object, PropertyMapping $mapping)
    {
        return $object->getId();
    }

}
