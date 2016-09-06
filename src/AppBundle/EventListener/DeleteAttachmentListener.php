<?php
namespace AppBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use AppBundle\Entity\Attachment;

/**
 * Description of DeleteAttachmentListener
 *
 * @author dimas
 */
class DeleteAttachmentListener 
{
    private $attDir;
    
    public function __construct($attachmentDir)
    {
        $this->attDir = $attachmentDir['full_attachment_dir'];
    }
    
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Attachment) {
            return;
        }
        
        $file = $this->attDir. $entity->getFile();
        
        unlink($file);
        
        //write to log files
        if(file_exists($file)) {
            throw new \Exception("Cannot delete file  {$file}");
        }
    }
    
}
