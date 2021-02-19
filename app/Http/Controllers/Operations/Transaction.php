<?php

namespace App\Http\Controllers\Operations;

use App\Repository\TransactionRepository;
use App\Repository\UsersRepository;
use App\Services\CurlService;
use App\Services\DataValidator;

class Transaction
{
    public $requiredParams = [
        "value",
        "payer",
        "payee"
    ];

    public function __construct() {
        $this->repo_transaction = new TransactionRepository();
        $this->repo_user = new UsersRepository();
        $this->curl = new CurlService();
        $this->validator = new DataValidator();
        $this->infos = [];
    }

    public function run($request) {
        try {
            $this->infos = $request->all();
            $return = $this->validator->treatValue($this->infos['value']);
            if($return['error'] == true) {
                throw new \Exception($return_users['message']); 
            }
            $this->infos['value'] = $return['value'];
            
            $return_users = $this->UsersValidate();
            if($return_users['error'] == true) {
                throw new \Exception($return_users['message']); 
            }
            
            $id = $this->repo_transaction->insert($this->infos);

            $return = $this->SendMocky(env('MOCKY_TRANSACTION'), $id);
            if($return['error'] == true) {
                throw new \Exception($return['message']); 
            }
            
            $balance_payer = $return_users['wallet_payer'] - (float)$this->infos['value'];
            $this->repo_user->updateBalance($balance_payer, $this->infos['payer']);

            $balance_payee = $return_users['wallet_payee'] + (float)$this->infos['value'];
            $this->repo_user->updateBalance($balance_payee, $this->infos['payee']);

            $return = $this->SendMocky(env('MOCKY_NOTIFICATION'), $id);
            if($return['error'] == true) {
                $this->repo_user->updateBalance($return_users['wallet_payer'], $this->infos['payer']);
                $this->repo_user->updateBalance($return_users['wallet_payee'], $this->infos['payee']);
                throw new \Exception($return['message']); 
            }

            $this->repo_transaction->updateTransaction('success',$id);
            
            $return = [
                "Mensagem:" => "Transferencia realizada com sucesso",
                "Saldo Atualizado" => $balance_payer
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

    public function UsersValidate() {
        try {
            $return_payer = $this->repo_user->searchUser($this->infos['payer']);
            if(count($return_payer) == 0) {
                throw new \Exception('ID Pagador nao encontrado');
            }
            
            $return_payee = $this->repo_user->searchUser($this->infos['payee']);
            if(count($return_payee) == 0) {
                throw new \Exception('ID Beneficiario nao encontrado');
            }
    
            if($return_payer[0]->type_user == 2) {
                throw new \Exception('Logistas nao podem ser pagadores');
            }

            $wallet_payer = empty($return_payer[0]->balance) ? 0 : $return_payer[0]->balance;
            $wallet_payee = empty($return_payee[0]->balance)? 0 : $return_payee[0]->balance;
            
            return array(
                "error" => false,
                "wallet_payer" => (float)$wallet_payer,
                "wallet_payee" => (float)$wallet_payee
            );
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }
    }

    public function SendMocky($url, $id) {
        try {
            $return = $this->curl->send($url, 'POST', $this->infos);
            if($return['status'] == 500) {
                $this->repo_transaction->updateTransaction($return['message'],$id);
                throw new Exception($return['message']);
            }
            return array(
                "error" => false,
            );
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return array(
                "error" => true,
                "message" => $e->getMessage()
            );
        }
    }
}
