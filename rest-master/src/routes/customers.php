<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
});

// Get All Customers
$app->get('/api/customers', function(Request $request, Response $response){
    $sql = "SELECT * FROM usuarios";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($customers);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Get Single Customer
$app->get('/api/customers/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');

    $sql = "SELECT * FROM usuarios WHERE id_usuarios = $id";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $customer = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($customer);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Add Customer
$app->post('/api/customers/add', function(Request $request, Response $response){
    $id_user = $request->getParam('id_usuarios');
    $cedula = $request->getParam('cedula');
    $first_name = $request->getParam('nombre');
    $last_name = $request->getParam('apellido');
    $phone = $request->getParam('telefono');
    $address = $request->getParam('direccion');
    $city = $request->getParam('ciudad');

    $sql = "INSERT INTO usuarios (id_usuarios,cedula,nombre,apellido,telefono,direccion,ciudad') VALUES
    (:id_usuarios,:cedula,:nombre,:apellido,:telefono,:email,:direccion,:ciudad)";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id_usuarios', $id_user);
        $stmt->bindParam(':cedula', $cedula);
        $stmt->bindParam(':nombre', $first_name);
        $stmt->bindParam(':apellido',  $last_name);
        $stmt->bindParam(':telefono',      $phone);
        $stmt->bindParam(':direccion',    $address);
        $stmt->bindParam(':ciudad',       $city);

        $stmt->execute();

        echo '{"notice": {"text": "Usuario ingresado"}';

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Delete Customer
$app->delete('/api/customers/delete/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');

    $sql = "DELETE FROM usuarios WHERE id_usuarios = $id";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Usuario eliminado"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});