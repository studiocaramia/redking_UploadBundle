<?php
/**
 * Namer déduit de l'objet
 */
namespace Redking\Bundle\UploadBundle\Naming;

use Vich\UploaderBundle\Naming\UniqidNamer;
use Vich\UploaderBundle\Mapping\PropertyMapping;


class ObjectNamer extends UniqidNamer
{
    /**
     * Construit le nom du fichier à partir de l'objet
     * @param  [type]          $object  [description]
     * @param  PropertyMapping $mapping [description]
     * @return [type]                   [description]
     */
    public function name($object, PropertyMapping $mapping)
    {
        $file = $mapping->getFile($object);
        
        $extension = $this->getExtension($file);

        $name = uniqid();
        
        if (is_null($object->getId())) {
            $name = parent::name($object, $mapping);
        } elseif(!is_null($extension)) {
            $name = $object->getId().'.'.$extension;
        }
        return $name;
    }

    /**
     * Retourne le nom du fichier tel qu'il devrait être
     * @param  [type] $object [description]
     * @param  string $filename   un nom de fichier
     * @return [type]         [description]
     */
    public function getNormalizedName($object, $filename)
    {
        if (is_null($object->getId())) {
            throw new \Exception("Object id should not be null");
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        return $object->getId().'.'.$extension;
    }
}
