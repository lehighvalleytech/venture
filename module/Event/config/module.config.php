<?php
return [
    'router' => [
        'routes' => [
            'main' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => 'Event\Controller',
                        'action' => 'lvtech',
                    ]
                ],
                'may_terminate' => true,
            ],
            'event' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route' => '/:event[/:action]',
                    'defaults' => [
                        'controller' => 'Event\Controller',
                        'action' => 'index',
                    ]
                ],
                'may_terminate' => true,
            ]
        ]
    ],
    'controllers' => [
        'factories' => [
            'Event\Controller' => 'Event\Factory\Controller',
        ]
    ],

    'view_manager' => [
        'template_map' => [
            'error'          => __DIR__ . '/../view/error.phtml',
            'layout/venture' => __DIR__ . '/../view/venture/layout.phtml',
            'venture/index'  => __DIR__ . '/../view/venture/index.phtml',

            'layout/lvhack' => __DIR__ . '/../view/lvhack/layout.phtml',
            'lvhack/index'  => __DIR__ . '/../view/lvhack/index.phtml',

            'layout/layout'  => __DIR__ . '/../view/layout.phtml',
            'event/controller/register' => __DIR__ . '/../view/register.phtml',
            'event/controller/payment' => __DIR__ . '/../view/payment.phtml',
            'event/controller/ticket' => __DIR__ . '/../view/ticket.phtml',
            'event/controller/lvtech' => __DIR__ . '/../view/lvtech.phtml',
        ]
    ],
    'service_manager' => [
        'factories' => [
            'Service' => 'Event\Factory\Service',
            'Prismic' => 'Event\Factory\Prismic',
            'Orchestrate' => 'Event\Factory\Orchestrate',
            'Sendgrid' => 'Event\Factory\Sendgrid',
        ],
    ],

];