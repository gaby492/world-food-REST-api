<?php
	class Dish {
		// DB realted variiables
		private $conn;
		private $table = 'dish';

		public function __construct($db) {
			$this->conn = $db;
		}

		public function getBasicQuery() {
			$query = '
					SELECT 
								d.dish_id,
								d.name, 
								c.name as country_name,
								d.spicy,
								t.name as dish_type,
								d.image,
								group_concat(i.name) AS ingredients
					FROM ' . $this->table . ' d 
					LEFT JOIN
							dishIngredient di 
							on d.dish_id = di.dish_id 
					LEFT JOIN
							ingredient i 
							on di.ingredient_id = i.ingredient_id
					LEFT JOIN 
							country c ON d.country_id = c.country_id
					LEFT JOIN
							type t ON d.type_id = t.type_id';

					return $query;
		}

		// Get dishes
		public function findAll() {
			$query = $this->getBasicQuery() . ' GROUP BY d.dish_id';
			
			// Prepare statement and Excecute query
			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			$num = $stmt->rowCount();

			// Dish array
			$dishes_arr = [];
			$dishes_arr['data'] = [];

			if ($num > 0) {
		
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					extract($row);
					
					$dish_item = [
						'name' => $name,
						'country_name' => $country_name,
						'type' => $dish_type,
						'spicy' => $spicy ? true : false,
						'ingredients' => explode (",", $ingredients) 
					];
					
					array_push($dishes_arr['data'], $dish_item);
				}
			} 
			
			return $dishes_arr;
		}

		public function get_single($id) {
			$query = $this->getBasicQuery() . ' WHERE d.dish_id = ? GROUP BY d.dish_id  LIMIT 0,1';
			
			// Prepare statement
			$stmt = $this->conn->prepare($query);

			// Bind ID
			$stmt->bindParam(1, $id); 

			// Excecute query
			$stmt->execute();

			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			$dish_arr = [
				'id' => $id,
				'name' => $row['name'],
				'country' => $row['country_name'],
				'image' => $row['image'],
				'type' => $dish->type,
				'spicy' => $row['spicy'] ? true : false,
				'ingredients' => explode (",", $row['ingredients'])
			];

			return $dish_arr;
		}

		public function create($data) {
			$query = 'INSERT INTO ' . $this->table . '
				SET
					name = :name,
					country_id = :country_id,
					image = :image,
					type_id = :type_id,
					spicy = :spicy';
			
			// Prepare statement
			$stmt = $this->conn->prepare($query);

			// Clean data
			$name = htmlspecialchars(strip_tags($data['name']));
			$country_id = htmlspecialchars(strip_tags($data['country_id']));
			$image = htmlspecialchars(strip_tags($data['image']));
			$type_id = htmlspecialchars(strip_tags($data['type_id']));
			$spicy = htmlspecialchars(strip_tags($data['spicy']));

			// Bind data
			$stmt->bindParam(':name', $name); 
			$stmt->bindParam(':country_id', $country_id); 
			$stmt->bindParam(':image', $image); 
			$stmt->bindParam(':type_id', $type_id); 
			$stmt->bindParam(':spicy', $spicy); 

			return $this->executeQuery($stmt);
		}

		// Update dish
		public function update($id, $input) {
			$query = 'UPDATE ' . $this->table . '
				SET
					name = :name,
					country_id = :country_id,
					image = :image,
					type_id = :type_id,
					spicy = :spicy
				WHERE 
					dish_id = :dish_id';
			
			// Prepare statement
			$stmt = $this->conn->prepare($query);

			// Clean data
			$id = htmlspecialchars(strip_tags($id));
			$name = htmlspecialchars(strip_tags($input['name']));
			$country_id = htmlspecialchars(strip_tags($input['country_id']));
			$image = htmlspecialchars(strip_tags($input['image']));
			$type_id = htmlspecialchars(strip_tags($input['type_id']));
			$spicy = htmlspecialchars(strip_tags($input['spicy']));

			// Bind data
			$stmt->bindParam(':dish_id', $id);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':country_id', $country_id);
			$stmt->bindParam(':image', $image);
			$stmt->bindParam(':type_id', $type_id);
			$stmt->bindParam(':spicy', $spicy);

			return $this->executeQuery($stmt);
		}

		// Delete post
		public function delete($id) {
			// Create query
			$query = 'DELETE FROM ' . $this->table . ' WHERE dish_id = :dish_id ';

			// Prepare statement 
			$stmt = $this->conn->prepare($query);

			// Clean data
			$id = htmlspecialchars(strip_tags($id));

			// Bind data
			$stmt->bindParam(':dish_id', $id);

			return $this->executeQuery($stmt);
		}

		private function executeQuery($stmt) {
			if ($stmt->execute()) {
				return true;
			}

			return false;
		}
	}