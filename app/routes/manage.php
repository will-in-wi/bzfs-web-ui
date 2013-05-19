<?php

// GET route
$app->get('/', function () use ($app) {

    $procs = get_bzfs_procs();

    $app->render('index.html.twig', array(
    	'procs' => $procs
    ));
});

// POST route
// $app->post('/post', function () {
//     echo 'This is a POST route';
// });

// PUT route
// $app->put('/put', function () {
//     echo 'This is a PUT route';
// });

// DELETE route
// $app->delete('/delete', function () {
//     echo 'This is a DELETE route';
// });

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */