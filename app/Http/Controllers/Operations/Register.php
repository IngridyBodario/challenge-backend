<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Repository\UsersRepository;
use App\Services\DataValidator;

class Register extends Controller
{
    public $requiredParams = [
        "name",
        "document",
        "email",
        "password"
    ];

    public function __construct() {
        $this->validator = new DataValidator();
        $this->repo_users = new UsersRepository();
    }

    public function run($request) {
        try {
            $infos = $this->DataValidate($request);
            if($infos['error'] == true) {
                throw new \Exception($infos['message']); 
            }

            $id = $this->repo_users->insert($infos['result']);
            $return = [
                "Message:" => "Usuario inserido",
                "User Name" => $infos['result']['name'],
                "ID" => $id
            ];

            return array(
                "error" => false,
                "result" => $return
            );
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }
    }

    public function DataValidate($request) {
        try {
            $infos = $request->all();
            $validate = $this->validator->validateParams($infos, $this->requiredParams);
            if($validate['error'] == true) {
                throw new \Exception("Parametro ".$validate['return']." obrigatorio");
            }

            $validate = $this->validator->validateDocument($infos['document'], $request);
            if($validate['error'] == true) {
                throw new \Exception($validate['message']);
            }
            
            $infos['type_user'] = $validate['type_user'];
            $infos['document'] = $validate['document'];
            
            $return = $this->repo_users->verify($infos);
            if(count($return) > 0) {
                throw new \Exception("Ja existe usuario com o mesmo Email e CPF/CNPJ"); 
            }

            return array(
                "error" => false,
                "result" => $infos
            );
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return array(
                "error" => true,
                "message" =>$e->getMessage()
            );
        }
    }
}
