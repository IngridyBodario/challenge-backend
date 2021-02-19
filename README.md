## Challenge Backend
Desafio back, cadastro de usuários e fluxo de transferências

## Dependências e Bibliotecas

- PHP 7.4
- PostgreSQL 10.15
- geekcom/validator-docs => Validador de cpf/cnpj (escolhi usa-lo para otimizar o tempo).

**Executar**
```
git clone https://github.com/IngridyBodario/challenge-backend.git
sudo -u postgres psql -c 'create database challengedb;'
php artisan migrate
php artisan serve
```

## Aplicação
**/api/ChallengeBackend/Register**
- Rota para salvar os dados do usuário
```
entrada
{
    'name': 'Ingridy',
    'document': '83351423942',
    'email': 'teste@gmail.com',
    'password': 'teste'
}
```
```
retorno
{
   "error":false,
   "result":{
      "Message:":"Usuario inserido",
      "User Name":"Ingridy",
      "ID":12
   }
}
```
**/api/ChallengeBackend/Transaction**
- Rota para realizar transferência

```
entrada
{
    'payer': 12,
    'payee': 11,
    'value': 100
}
```
```
retorno
{
   "error":false,
   "result": {
        "Mensagem:": "Transferencia realizada com sucesso",
        "Saldo Atualizado": 10
    }
}
```

