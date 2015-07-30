<?php
/**
 * Namer de base des répertoires portant comme nom l'id d'un objet
 */
namespace Redking\Bundle\UploadBundle\Naming;

use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;

abstract class BaseDirectoryIdNamer
{
    /**
     * Retourne le mauvais chemin du fichier
     * @param  [type]          $object  [description]
     * @param  PropertyMapping $mapping [description]
     * @return [type]                   [description]
     */
    public function directoryNameError($object, PropertyMapping $mapping)
    {
        return '';
    }

}
