# Sistema de Controle de DOs e DNVs

Sistema desenvolvido para gerenciar a distribuição e controle de Declarações de Óbito (DOs) e Declarações de Nascidos Vivos (DNVs) em instituições de saúde.

## 📋 Funcionalidades

- **Gestão de Instituições**
  - Cadastro e gerenciamento de instituições de saúde
  - Controle de informações de contato
  - Histórico de distribuições por instituição

- **Controle de Distribuições**
  - Distribuição de formulários (DOs e DNVs)
  - Controle de numeração sequencial
  - Rastreamento de distribuições por data
  - Gestão de quantidades e status

- **Gerenciamento de Baixas**
  - Registro de baixas individuais e em lote
  - Controle de status (utilizada, cancelada, não utilizada)
  - Histórico de movimentações
  - Validação de numeração

- **Relatórios**
  - Relatório de distribuições
  - Relatório de utilização
  - Relatório de disponibilidade
  - Relatório de pendências

## 🚀 Tecnologias

- [PHP 8.1+](https://php.net)
- [Laravel 10.x](https://laravel.com)
- [Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Tailwind CSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)
- [SQLite](https://www.sqlite.org)

## 📦 Requisitos

- PHP >= 8.1
- Composer
- Node.js >= 16
- NPM ou Yarn
- SQLite

## ⚙️ Instalação

1. Clone o repositório
```bash
git clone https://github.com/sua-empresa/versa.git
cd versa
```

2. Instale as dependências do PHP
```bash
composer install
```

3. Instale as dependências do Node.js
```bash
npm install
```

4. Configure o ambiente
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure o banco de dados no arquivo .env
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

6. Crie o arquivo do banco de dados SQLite
```bash
touch database/database.sqlite
```

7. Execute as migrações e seeds
```bash
php artisan migrate --seed
```

8. Compile os assets
```bash
npm run dev
```

9. Inicie o servidor
```bash
php artisan serve
```

O sistema estará disponível em `http://localhost:8000`

## 👥 Usuário Padrão

Após executar as seeds, você terá acesso com as seguintes credenciais:

- **Email:** admin@example.com
- **Senha:** password

## 🔒 Permissões

O sistema utiliza o pacote Laravel Permission para controle de acesso. As principais permissões são:

- **Instituições:** instituicao_listar, instituicao_criar, instituicao_editar, instituicao_excluir
- **Distribuições:** distribuicao_listar, distribuicao_criar, distribuicao_editar, distribuicao_excluir
- **Baixas:** baixa_listar, baixa_criar, baixa_editar, baixa_excluir
- **Relatórios:** relatorio_gerar

## 🔍 Testes

Para executar os testes:

```bash
php artisan test
```
