<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Table is used for PDO Session handler. No direct access to Entity must be given.
 *
 * @ORM\Table(name="sessions")
 * @ORM\Entity
 */
class Sessions {
   
    /**
     * @var VARCHAR(128)
     *
     * @ORM\Column(name="sess_id", type="string", length=128)
     * @ORM\Id
     */
    private $sess_id;
    
    /**
     * @var bytea
     *
     * @ORM\Column(name="sess_data", type="blob", nullable=false)
     */
    private $sess_data;
    
    /**
     * @var bytea
     *
     * @ORM\Column(name="sess_time", type="integer", nullable=false)
     */
    private $sess_time;
    
    /**
     * @var bytea
     *
     * @ORM\Column(name="sess_lifetime", type="integer", nullable=false)
     */
    private $sess_lifetime;
}
