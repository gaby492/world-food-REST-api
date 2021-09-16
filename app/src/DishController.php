<?php
	include_once '/var/www/html/config/Database.php';
	include_once '/var/www/html/models/Dish.php';

  class DishController {

    private $database;
    private $db;
    private $dishId;
    private $requestMethod;

    public function __construct($requestMethod, $dishId) {
      $this->database = new Database();
      $this->db = $this->database->connect();
      $this->requestMethod = $requestMethod;
      $this->dishId = $dishId;
    }

    public function processRequest() {
      switch ($this->requestMethod) {
        case 'GET':
          if ($this->dishId) {
            $response = $this->getDish($this->dishId);
          } else {
            $response = $this->getAllDishes();
          }
          break;
        case 'POST':
          $response = $this->createDish();
          break;
        case 'PUT':
          $response = $this->updateDish($this->dishId);
          break;
        case 'DELETE':
          $response = $this->deleteDish($this->dishId);
          break;
        default:
          $response = $this->notFoundResponse();
          break;
      }
      header($response['status_code_header']);
      if ($response['body']) {
        print_r($response);
      }
    }

    private function getDish($id) {
      $dish = new Dish($this->db);
      $result = $dish->get_single($id);
      if (!$result) {
        return $this->notFoundResponse();
      }
      $response['status_code_header'] = 'HTTP/1.1 200 OK';
      $response['body'] = json_encode($result);
      return $response;
    }

    private function getAllDishes() {
      $dish = new Dish($this->db);
      $result = $dish->findAll();
      $response['status_code_header'] = 'HTTP/1.1 200 OK';
      $response['body'] = json_encode($result);
      return $response;
    }

    private function createDish() {
      $dish = new Dish($this->db);
      
      $input = (array) json_decode(file_get_contents('php://input'), TRUE);
      if (! $this->validateDish($input)) {
        return $this->invalidInputResponse();
      }
      
      $result = $dish->create($input);
      $response['status_code_header'] = 'HTTP/1.1 201 Created';
      $response['body'] = $result;
      return $response; 
    }

    private function updateDish($id) {
      $dish = new Dish($this->db);
      $result = $dish->get_single($id);
      if (! $result) {
          return $this->notFoundResponse();
      }
      $input = (array) json_decode(file_get_contents('php://input'), TRUE);
      if (! $this->validateDish($input)) {
          return $this->invalidInputResponse();
      }

      $result = $dish->update($id, $input);
      $response['status_code_header'] = 'HTTP/1.1 200 Updated';
      $response['body'] = $result;
      return $response;
    }

    private function deleteDish($id) {
      $dish = new Dish($this->db);
      $result = $dish->get_single($id);
      if (! $result) {
          return $this->notFoundResponse();
      }
      $result = $dish->delete($id);
      $response['status_code_header'] = 'HTTP/1.1 200 OK';
      $response['body'] = $result;
      return $response;
    }

    private function validateDish($input)
    {
        if (!isset($input['name']) ||
            !isset($input['type_id']) ||
            !isset($input['country_id']) ||
            !isset($input['spicy']) ||
            !isset($input['image'])) {
            return false;
        }
        
        return true;
    }

    private function invalidInputResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse() {
      $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
      $response['body'] = null;
      return $response;
    }
  }