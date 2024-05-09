<?php

// src/EventListener/CorsListener.php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class CorsListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $method = $request->getRealMethod();

        // Allow requests from http://localhost:5173
        $origin = $request->headers->get('Origin');
        if ($origin === 'http://localhost:5173') {
            $response = new Response();
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');

            // Handle preflight requests
            if ($method === 'OPTIONS') {
                $response->setStatusCode(200);
                $event->setResponse($response);
                return;
            }

            // Attach CORS headers to the response
            $event->setResponse($response);
        }
    }
}
