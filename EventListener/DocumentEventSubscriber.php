<?php
/**
 * Subscriber pour le cycle de vie des documents uploadable
 */
namespace Redking\Bundle\UploadBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Events as MongoDBEvents;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;

use Vich\UploaderBundle\Metadata\MetadataReader;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Util\ClassUtils;

use Symfony\Component\HttpFoundation\File\File;

use Knp\Bundle\GaufretteBundle\FilesystemMap;

use Redking\Bundle\UploadBundle\Naming\ResizedNamer;
use Redking\Bundle\UploadBundle\Naming\BaseDirectoryIdNamer;

class DocumentEventSubscriber implements EventSubscriber
{
    /**
     * @var MetadataReader
     */
    protected $metadata_reader;
    
    /**
     * @var PropertyMappingFactory
     */
    protected $mapping_factory;
    
    /**
     * @var FilesystemMap
     */
    protected $filesystem_map;

    /**
     * Tableau contenant les redimensionnement à faire
     * @var array
     */
    protected $resizes;

    public function __construct(MetadataReader $metadata_reader, PropertyMappingFactory $mapping_factory, FilesystemMap $filesystem_map, $resizes)
    {
        $this->metadata_reader = $metadata_reader;
        $this->mapping_factory = $mapping_factory;
        $this->filesystem_map  = $filesystem_map;
        $this->resizes         = $resizes;
    }

    public function getSubscribedEvents()
    {
        return array(
            MongoDBEvents::postPersist,
            );
    }


    /**
     * Dans le cas d'un document nouvellement enregistré, je vais renommer le nom du fichier uploadé si besoin
     * @param  LifecycleEventArgs $eventArgs [description]
     * @return [type]                        [description]
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        $dm = $eventArgs->getDocumentManager();


        $is_uploadable = $this->metadata_reader->isUploadable(ClassUtils::getClass($document));

        if ($is_uploadable) {
            // Récupération des champs uploadable
            $fields = $this->metadata_reader->getUploadableFields(ClassUtils::getClass($document));

            // Pour chacun de ces champs, je récupère le mapping associé pour vérifier le namer et le nom du champ
            foreach($fields as $field) {
                $mapping = $this->mapping_factory->fromField($document, $field['propertyName']);
                
                if ($mapping->getNamer() instanceof \Redking\Bundle\UploadBundle\Naming\ObjectNamer) {

                    $filename = $mapping->getFileName($document);
                    $file = $mapping->getFile($document);

                    // Si il y a bien un fichier, je vérifie son nom
                    if (!is_null($filename) && $file instanceof File) {
                        $filename_normalized = $mapping->getNamer()->getNormalizedName($document, $filename);

                        // Si les deux noms ne correspondent pas, je renomme et réassigne
                        if (strcmp($filename, $filename_normalized) !== 0) {
                            
                            $base_directory = ($mapping->hasDirectoryNamer()) ? $mapping->getDirectoryNamer()->directoryName($document, $mapping).'/' : '';

                            $adapter = $this->filesystem_map
                                ->get($mapping->getUploadDestination())
                                ->getAdapter();
                            
                            $adapter->rename($base_directory. $filename, $base_directory. $filename_normalized);
                            if ($adapter->exists($base_directory. $filename)) {
                                $adapter->delete($base_directory. $filename);
                            }

                            // On vérifie si il y a des fichiers resized à renommer
                            foreach ($this->resizes as $suffix => $resize_conf) {
                                $resize_filename            = $base_directory . ResizedNamer::getName($filename, $suffix);
                                $resize_filename_normalized = $base_directory . ResizedNamer::getName($filename_normalized, $suffix);

                                if ($adapter->exists($resize_filename)) {
                                    $adapter->rename($resize_filename, $resize_filename_normalized);
                                    if ($adapter->exists($resize_filename)) {
                                        $adapter->delete($resize_filename);
                                    }
                                }
                            }

                            $mapping->setFileName($document, $filename_normalized);

                            // Ré-enregistrement
                            $class = $dm->getClassMetadata(get_class($document));
                            $dm->getUnitOfWork()->recomputeSingleDocumentChangeSet($class, $document);
                        }
                    }
                }

                // Traitement du répertoire basé sur l'id pour voir si le fichier est bien dedans
                $directory_namer = $mapping->getDirectoryNamer();
                if (!is_null($directory_namer) && $directory_namer instanceof BaseDirectoryIdNamer) {
                    $adapter = $this->filesystem_map
                                ->get($mapping->getUploadDestination())
                                ->getAdapter();

                    $filename  = $mapping->getFileName($document);
                    $good_path = ltrim($directory_namer->directoryName($document, $mapping).'/'.$filename, '/');
                    $bad_path  = ltrim($directory_namer->directoryNameError($document, $mapping).'/'.$filename, '/');

                    if (!$adapter->exists($good_path) && $adapter->exists($bad_path)) {
                        $success = $adapter->rename($bad_path, $good_path);
                    }
                }
            }
        }
    }

}
