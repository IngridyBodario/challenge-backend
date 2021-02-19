## Challenge Backend
Desafio back, cadastro de usuários e fluxo de transferências

## Dependências e Bibliotecas

- PHP 7.4
- PostgreSQL 10.15
- geekcom/validator-docs => Validador de cpf/cnpj (escolhi usa-lo para otimizar o tempo).

Rodar
```
sudo -u postgres psql -c 'create database desafiodb;'
php artisan migrate
php artisan serve
```

## Aplicação
**/Register**
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
**/Transaction**
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
   "result":"ok"
}
```

