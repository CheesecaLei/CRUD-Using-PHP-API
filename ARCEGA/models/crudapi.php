<?php

interface CrudInterface{
    public function getAll();
    public function getOne();
    public function insert();
    public function update();
    public function delete();
}
ss
class crudapi {

    protected $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function getAll(){
        $sql = "SELECT * FROM users";
        try{
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute()){
                $data =  $stmt->fetchAll();
                if ($stmt->rowCount() > 0){
                    return $data;
                }else{
                    http_response_code(404);
                    return 'There are no data present';
                }
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    } 

    public function getOne($data){
        $sql = "SELECT * FROM users WHERE user_id = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->user_id])){
                $data =  $stmt->fetchAll();
                if ($stmt->rowCount() > 0){
                    return $data;
                }else{
                    http_response_code(404);
                    return 'User does not exist';
                }
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function insert($data){
        $sql = 'INSERT INTO users(firstname, lastname, is_admin) VALUES(?, ?, Default)';

        if (!isset($data->firstname) || !isset($data->lastname)) {
            return "firstname and lastname are required fields. PLEASE TRY AGAIN!";
        }

        if (empty($data->firstname) || empty($data->lastname)) {
            return "REQUIRED FIELDS CANNOT BE EMPTY!";
        }

        try{
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->firstname, $data->lastname])){
                $lastID = $this->pdo->lastInsertId();
                echo json_encode(["msg"=>"Finished INSERTING Data!"]);
                return $this->getOne((object)['user_id'=>$lastID]);
            }else{
                echo json_encode(["msg"=>"Try INSERT Data again!"]);
            }
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function update($data){
        $sql = "UPDATE users SET is_admin = CASE WHEN is_admin = 0 THEN 1 WHEN is_admin = 1 THEN 0 END WHERE user_id = ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->user_id])) {
                if ($stmt->rowCount() > 0){
                    echo json_encode(["message"=>"Finished UPDATING Data!"]);
                    return $this->getOne((object)['user_id' => $data->user_id]);
                } else {
                    http_response_code(404);
                    echo json_encode(["message"=>"Cannot find Id or User."]);
                }
            }
        } catch (PDOException $e) {
            return $e->getMessage();  
        }
    } 

    public function delete($data){
        $sql = "DELETE FROM users WHERE user_id = ?";
    
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->user_id])) {
                if ($stmt->rowCount() > 0){
                echo json_encode(["message"=>"Successfully DELETED User!"]);
                } else {
                    http_response_code(404);
                    echo json_encode(["message"=>"Sorry, the User does not exist or was deleted earlier."]);
                }
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}