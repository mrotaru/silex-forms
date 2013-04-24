<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

use Silex\Provider\FormServiceProvider;
$app->register(new FormServiceProvider());

use Silex\Provider\TranslationServiceProvider;
$app->register(new TranslationServiceProvider(), array(
    'translator.messages' => array(),
));

use Silex\Provider\TwigServiceProvider;
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/'
));

use Symfony\Component\HttpFoundation\Request;
$app->match('/form', function (Request $request) use ($app) {
    // some default data for when the form is displayed the first time
    $data = array(
        'name' => 'Your name',
        'email' => 'Your email',
    );

    $form = $app['form.factory']->createBuilder('form', $data)
        ->add('name')
        ->add('email')
        ->add('gender', 'choice', array(
            'choices' => array(1 => 'male', 2 => 'female'),
            'expanded' => true,
        ))
        ->getForm();

    if ('POST' == $request->getMethod()) {
        $form->bind($request);

        if ($form->isValid()) {
            $data = $form->getData();

            // do something with the data

            // redirect somewhere
//            return $app->redirect('...');
        }
    }

    // display the form
    return $app['twig']->render('index.twig', array('form' => $form->createView()));
});

$app->run();
