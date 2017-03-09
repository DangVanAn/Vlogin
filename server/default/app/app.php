<?php
/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */

/**
 * Add your routes here
 */

use Phalcon\Http\Response;

$app->get('/abc', function () {
    echo $this['view']->render('index');
});
$app->get('/logout', function () use ($app) {
    $app["session"]->destroy();
    echo "Logout success ! ";
});
$app->before(function () use ($app, $acl) {
    $auth = $app["session"]->get("auth");
    if (!$auth) {
        $role = 'Guests';
    } else {
        $role = 'Users';
    }
    //get element of uri
    $element = explode("/", $app['router']->getRewriteUri());

    $allowed = $acl->isAllowed($role, (!empty($element[1]) ? $element[1] : ""), (!empty($element[2]) ? $element[2] : ""));
    if ($allowed === false) {
        $app["flashSession"]->error("The user isn't authenticated");
        $app["response"]->redirect("error");
        $app->response->sendHeaders();

        return false;
    }
    return true;
});

$app->post('/login', function () use ($app) {

    $user = $app->request->getJsonRawBody();
    $pass = $user->password;
    $user = Users::findFirst(
        [
            "(email = :email:)",
            "bind" => [
                "email" => $user->email,
                "password" => $user->password,
            ]
        ]
    );
    $response = new Response();

    if ($user !== false) {

        if ($this->security->checkHash($pass, $user->password)) {
            $app["session"]->set(
                "auth",
                [
                    "id" => $user->id,
                    "email" => $user->email,
                ]);

            $response->setStatusCode(200, "OK");
            $response->setJsonContent(
                [
                    "status" => "OK",
                    "messages" => "Login success! " . $user->id
                ]
            );
        } else {
            // Change the HTTP status
            $response->setStatusCode(200, "OK");
            $response->setJsonContent(
                [
                    "status" => "Fail",
                    "messages" => "wrong password!"
                ]
            );

        }
    } else {
        // Change the HTTP status
        $response->setStatusCode(200, "OK");
        $response->setJsonContent(
            [
                "status" => "Fail",
                "messages" => "Account does not exist!"
            ]
        );
    }

    return $response;
});

$app->post('/signup', function () use ($app) {
    try {
        $post = $app->request->getJsonRawBody();

        $user = new Users();

        //$user->username = $post->username;
        $user->email = $post->email;
        $user->password = $this->security->hash($post->password);

        // Create a response
        $response = new Response();

        // Check if the insertion was successful
        if ($user->save()) {
            // Change the HTTP status
            $response->setStatusCode(201, "Created");

            $response->setJsonContent(
                [
                    "status" => "OK",
                    "data" => "Signup success!"
                ]
            );
        } else {
            // Change the HTTP status
            $response->setStatusCode(200, "OK");
            // Send errors to the client
            $response->setJsonContent(
                [
                    "status" => "ERROR",
                    "messages" => "Signup fail!"
                ]
            );
        }
        $app->response = $response;
    } catch (Exception $exception) {
        $app->response->setStatusCode(404, "Not Found")->sendHeaders();
        echo $app['view']->render('404');
    }

    return $response;
});
/**
 * Not found handler
 */
$app->get("/error", function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo $app['view']->render('404');
});
$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo $app['view']->render('404');
});
