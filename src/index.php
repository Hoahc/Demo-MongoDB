<?php
require '../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$client = new Client("mongodb://localhost:27017");
$database = $client->selectDatabase('testdb');
$usersCollection = $database->users;
$companiesCollection = $database->companies;

header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['collection']) && $input['collection'] === 'companies') {
            $result = $companiesCollection->insertOne($input['data']);
        } else {
            $result = $usersCollection->insertOne($input['data']);
        }
        echo json_encode(['insertedId' => $result->getInsertedId()]);
        break;

    case 'GET':
        if (isset($_GET['id'])) {
            $id = new ObjectId($_GET['id']);
            $user = $usersCollection->findOne(['_id' => $id]);
            echo json_encode($user);
        } else {
            $users = $usersCollection->find()->toArray();
            echo json_encode($users);
        }
        break;

    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = new ObjectId($input['id']);
        $updateResult = $usersCollection->updateOne(
            ['_id' => $id],
            ['$set' => $input['data']]
        );
        echo json_encode(['matchedCount' => $updateResult->getMatchedCount(), 'modifiedCount' => $updateResult->getModifiedCount()]);
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = new ObjectId($input['id']);
        $deleteResult = $usersCollection->deleteOne(['_id' => $id]);
        echo json_encode(['deletedCount' => $deleteResult->getDeletedCount()]);
        break;

    default:
        echo json_encode(['error' => 'Phương thức không hợp lệ']);
        break;
}
?>
