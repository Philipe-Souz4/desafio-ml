# 🛒 Consulta de Produtos — Mercado Livre API - Yii2

Aplicação web para consulta de produtos do Mercado Livre desenvolvida com **Yii2 (Basic)** e **PostgreSQL** consumindo **API Mercado Livre**.

## 📌 Links Úteis

- **Documentação ML:** [Items e Buscas](https://developers.mercadolivre.com.br/pt_br/publicacao-de-produtos)
- **Dev Center ML:** [Gerenciar App](https://developers.mercadolivre.com.br/devcenter)

---

## 🚀 Funcionalidades Principais

* **Consulta de Produto:** Busca por ID do Mercado Livre (ex: `MLB59773991`) com exibição de dados relevantes.
* **Renovação Automática de Token:** O `access_token` é renovado automaticamente via `refresh_token` ao expirar.
* **Gerenciamento de Tokens:** Tela dedicada para inserir tokens manualmente ou renovar via fluxo OAuth completo.

---

## 🛠️ Tecnologias

* **PHP 7.4+** + **Yii2 Framework**
* **PostgreSQL**
* **Bootstrap 5**
* **Mercado Livre API**

---

## 📦 Como Instalar e Rodar

### 1. Clonar o repositório

```bash
git clone https://github.com/Philipe-Souz4/desafio-ml.git
cd desafio-ml
```

### 2. Configurar o Banco de Dados

Edite o arquivo `config/db.php` com suas credenciais do PostgreSQL:

```php
return [
    'class'    => 'yii\db\Connection',
    'dsn'      => 'pgsql:host=localhost;port=5432;dbname=desafio_ml',
    'username' => 'postgres',
    'password' => 'sua_senha',
    'charset'  => 'utf8',
];
```

### 3. Configurar as Credenciais do App ML

Edite o arquivo `config/params.php`:

```php
return [
    'meliClientId'     => 'SEU_CLIENT_ID',
    'meliClientSecret' => 'SEU_CLIENT_SECRET',
    'meliRedirectUri'  => 'https://example.com',
];
```

### 4. Instalação Automática

Para facilitar a instalação das dependências (Composer) e a criação das tabelas (Migrations), utilize os instaladores na raiz:

- No **Windows:** Execute o arquivo `install.bat`
- No **Linux/Mac:** Execute o arquivo `install.sh`

### 5. Iniciar o servidor

```bash
php yii serve
```

Acesse em: `http://localhost:8080`

---

## 🔑 Configurando os Tokens de Acesso

Após instalar, acesse `/token` para configurar o acesso à API.

### Opção 1 — Inserir tokens manualmente
Cole o `access_token` e `refresh_token` gerados via Postman ou curl diretamente pela interface.

### Opção 2 — Fluxo OAuth completo
1. Clique em **"Autorizar no Mercado Livre"**
2. Faça login e autorize o app
3. Copie o `code` da URL de redirect (`https://example.com?code=TG-...`)
4. Cole o code no campo e clique em **"Trocar"**

> ⚠️ O `access_token` expira em **6 horas**. A renovação é automática. Caso o `refresh_token` também expire, repita o fluxo OAuth.

---

## 📊 Dados Exibidos do Produto

| Campo | Descrição |
|---|---|
| `id` | Identificador do produto no ML |
| `title` | Título do anúncio |
| `category_id` | ID da categoria |
| `price` | Preço atual |
| `available_quantity` | Quantidade disponível em estoque |
| `thumbnail` | Imagem do produto |
| `permalink` | Link direto para o anúncio no ML |

---

## 📁 Estrutura do Projeto

```
desafio-ml/
├── config/
│   ├── db.php              # Credenciais do banco (não versionado)
│   ├── db.php.example      # Template de configuração do banco
│   ├── params.php          # Credenciais do app ML
│   └── web.php             # Configuração da aplicação
├── controllers/
│   ├── ProdutoController.php   # Busca e exibe dados do produto
│   ├── TokenController.php     # Gerenciamento de tokens OAuth
│   └── SiteController.php      # Página inicial
├── models/
│   └── MeliTokens.php          # Model de tokens (ActiveRecord)
├── migrations/
│   └── m..._create_meli_tokens_table.php
├── views/
│   ├── layouts/main.php
│   ├── produto/detalhes.php
│   ├── site/index.php
│   └── token/index.php
├── install.bat             # Instalador Windows
├── install.sh              # Instalador Linux/Mac
└── README.md
```

---

### 👨‍💻 Desenvolvido por

Philipe Souza — Desafio Técnico Mercado Livre
