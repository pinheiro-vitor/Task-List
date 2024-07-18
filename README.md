# Projeto Laravel + Vue.js

## Descrição
Este projeto é uma aplicação web construída com Laravel no backend e Vue.js no frontend. Ele permite gerenciar uma lista de tarefas, com diversas funcionalidades.

## Tecnologias Utilizadas
- [Laravel](https://laravel.com/)
- [Vue](https://vuejs.org/)
- [Inertia.js](https://inertiajs.com/)
- [Breeze](https://github.com/laravel/breeze)
- [Tailwind](https://tailwindcss.com/)

## Funcionalidades
- Exibir lista de tarefas
- Adicionar novas tarefas
- Editar tarefas existentes
- Visualizar detalhes de uma tarefa
- Paginação de tarefas
- Apagar tarefas
- Pesquisar por palavras-chaves
- Exportar em arquivo CSV
- Sistema de login e registro com autenticação

## Instalação e Configuração

### Pré-requisitos
- PHP >= 7.4
- Composer
- Node.js
- NPM ou Yarn
- SQLite (mas pode usar qualquer banco de dados, contato que configure)

### Passos para instalação

1. Clone o repositório:
    ```sh
    git clone https://github.com/pinheiro-vitor/Task-List
    cd seu-repositorio
    ```

2. Instale as dependências do Laravel:
    ```sh
    composer install
    ```

3. Instale as dependências do Node.js:
    ```sh
    npm install
    # ou se você estiver usando Yarn
    yarn install
    ```

4. Crie um arquivo `.env` a partir do `.env.example`:
    ```sh
    cp .env.example .env
    ```

5. Gere a chave da aplicação:
    ```sh
    php artisan key:generate
    ```

6. Configure as informações de banco de dados no arquivo `.env`:
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nome_do_banco
    DB_USERNAME=seu_usuario
    DB_PASSWORD=sua_senha
    ```

7. Execute as migrações do banco de dados:
    ```sh
    php artisan migrate
    ```

8. Compile os assets do frontend:
    ```sh
    npm run dev
    # ou se você estiver usando Yarn
    yarn dev
    ```

9. Inicie o servidor de desenvolvimento do Laravel:
    ```sh
    php artisan serve
    ```

10. Acesse a aplicação no navegador:
    ```
    http://localhost:8000
    ```
    
11. Preencher banco de dados (opcional):
    ```
    php artisan db:seed
    ```

    

## Estrutura do Projeto

### Backend (Laravel)
- **Controllers**: Controladores responsáveis pelo gerenciamento das tarefas (`app/Http/Controllers/TaskController.php`).
- **Models**: Modelos que representam as tarefas no banco de dados (`app/Models/Task.php`).
- **Migrations**: Migrações para criação das tabelas no banco de dados (`database/migrations/`).

### Frontend (Vue.js)
- **Componentes**: Componentes Vue.js para exibição e manipulação das tarefas (`resources/js/Components/`).
    - `TaskList.vue`: Componente para listar as tarefas.

## Rotas

### Web Routes (Laravel)
As rotas web estão definidas no arquivo `routes/web.php`.

```php
Route::get('/tasks)
Route::post('/tasks)
Route::put('/tasks/{task})
Route::delete('/tasks/{task})
Route::get('/tasks/export')
```

## Contribuição
Sinta-se à vontade para contribuir com este projeto. Para contribuir, siga os passos abaixo:
1. Faça um fork do repositório
2. Crie uma branch para sua feature (git checkout -b feature/nome-da-feature)
3. Commit suas mudanças (git commit -am 'Adicionar nova feature')
4. Faça o push para a branch (git push origin feature/nome-da-feature)
5. Crie um novo Pull Request
   
## Licença
Este projeto está licenciado sob a licença MIT. Veja o arquivo [LICENSE](https://github.com/git/git-scm.com/blob/main/MIT-LICENSE.txt) para mais detalhes.


## Contato
Para mais informações ou sugestões, entre em contato:

* Nome: Vitor Pinheiro
* Email: vitorpinheir.sp97@gmail.com
* GitHub: [pinheiro-vitor](https://github.com/pinheiro-vitor)
