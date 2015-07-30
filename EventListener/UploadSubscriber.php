<?php
/**
 * Subscriber d'events sur vichuploaded
 */

namespace Redking\Bundle\UploadBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Events as VichEvents;
use Vich\UploaderBundle\Event\Event;
use Knp\Bundle\GaufretteBundle\FilesystemMap;

use Gregwar\ImageBundle\Services\ImageHandling;

use Redking\Bundle\UploadBundle\Naming\ResizedNamer;

class UploadSubscriber implements EventSubscriberInterface
{
    /**
     * @var FilesystemMap
     */
    protected $filesystem_map;
    
    /**
     * @var ImageHandling
     */
    protected $image_handling;

    /**
     * Tableau contenant les redimensionnement à faire
     * @var array
     */
    protected $resizes;

    public function __construct(FilesystemMap $filesystem_map, ImageHandling $image_handling, $resizes)
    {
        $this->filesystem_map = $filesystem_map;
        $this->image_handling = $image_handling;
        $this->resizes        = $resizes;
    }

    public static function getSubscribedEvents()
    {
        // Liste des évènements écoutés et méthodes à appeler
        return array(
            VichEvents::POST_UPLOAD => 'onPostUpload',
            VichEvents::PRE_REMOVE  => 'onPreRemove'
        );
    }

    /**
     * Au post upload, je vais générer les redimentionnements définis en conf
     * @param  UploadEvent $event [description]
     * @return [type]             [description]
     */
    public function onPostUpload(Event $event)
    {
        $object = $event->getObject();
        $mapping = $event->getMapping();

        $file = $mapping->getFile($object);

        $mimetype = $file->getMimeType();
        $mimetype_parts = explode('/', $mimetype);

        if (is_array($mimetype_parts) && count($mimetype_parts) == 2 && $mimetype_parts[0] == 'image') {

            $adapter = $this->filesystem_map
                ->get($mapping->getUploadDestination())
                ->getAdapter();

            $base_directory = ($mapping->hasDirectoryNamer()) ? $mapping->getDirectoryNamer()->directoryName($object, $mapping).'/' : '';
            
            foreach($this->resizes as $suffix => $resize_conf) {

                $resize_file = $base_directory . ResizedNamer::getName($mapping->getFileName($object), $suffix);

                // Génération du resize
                $image_content = $this->image_handling->open($file->getPathname())
                    ->resize($resize_conf['width'], $resize_conf['height'])
                    ->get()
                ;

                // copie du resize
                $adapter->setMetadata($resize_file, array('contentType' => $mimetype));
                $adapter->write($resize_file, $image_content);

            }
        }
    }

    /**
     * A la suppression de l'objet uploadé, je supprime également les redimensionnements
     * @param  Event  $event [description]
     * @return [type]        [description]
     */
    public function onPreRemove(Event $event)
    {
        $object = $event->getObject();
        $mapping = $event->getMapping();

        $adapter = $this->filesystem_map
                ->get($mapping->getUploadDestination())
                ->getAdapter();

        $base_directory = ($mapping->hasDirectoryNamer()) ? $mapping->getDirectoryNamer()->directoryName($object, $mapping).'/' : '';

        foreach($this->resizes as $suffix => $resize_conf) {
            $resize_file = $base_directory . ResizedNamer::getName($mapping->getFileName($object), $suffix);

            if (!is_null($resize_file) && $adapter->exists($resize_file)) {
                $adapter->delete($resize_file);
            }
        }
    }

    

}
