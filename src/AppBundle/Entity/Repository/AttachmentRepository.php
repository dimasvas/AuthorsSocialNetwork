<?php
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Composition;

/**
 * Description of AttachmentRepository
 *
 * @author dimas
 */
class AttachmentRepository extends EntityRepository 
{    
    public function getCompositionAttachments(Composition $composition, $order = 'ASC')
    {
        
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('a')
            ->where('a.composition = :composition')
            ->from('AppBundle:Attachment', 'a')
            ->setParameter('composition', $composition->getId())
            ->orderBy('a.created', $order);
    }
}
