<?php
/**
 * Génération UUID d'un nom
 */
namespace Redking\Bundle\UploadBundle\Naming;

use Vich\UploaderBundle\Naming\UniqidNamer;
use Vich\UploaderBundle\Mapping\PropertyMapping;


class UuidNamer extends UniqidNamer
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

        if (class_exists('\Doctrine\ODM\MongoDB\Id\UuidGenerator')) {
            $generator = new \Doctrine\ODM\MongoDB\Id\UuidGenerator();
            $name = $generator->generateV5($generator->generateV4(), php_uname('n'));
        } else {
            $name = uniqid();
        }

        if ($extension = $this->getExtension($file)) {
            $name = sprintf('%s.%s', $name, $extension);
        }
        return $name;
    }

}
