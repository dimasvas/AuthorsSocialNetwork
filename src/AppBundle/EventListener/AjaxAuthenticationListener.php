<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Description of AjaxAuthenticationListener
 *
 * @author dimas
 * 
 * http://intelligentbee.com/blog/2015/08/25/how-to-fix-symfony2-ajax-login-redirect/
 */
class AjaxAuthenticationListener 
{
     /**
     * Handles security related exceptions.
     *
     * @param GetResponseForExceptionEvent $event An GetResponseForExceptionEvent instance
     */
    public function onCoreException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();
 
        if ($request->isXmlHttpRequest()) {
           //  if ($exception instanceof AuthenticationException || $exception instanceof AccessDeniedException) {
            if ($exception instanceof AuthenticationException) {
                $event->setResponse(new Response('not_authentificated', 403));
            }
        }
    }
}
