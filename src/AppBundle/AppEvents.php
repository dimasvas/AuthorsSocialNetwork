<?php
namespace AppBundle;

/**
 * Description of CompositionUpdateEvent
 *
 * @author dimas
 */
final class AppEvents {
    
    /**
     * The event is thrown each time an udate message is sent
     * to subscribed users.
     *
     * @var string
     */
    const COMPOSITION_UPDATE = 'composition.update';
}
