# EVOLUA - Avaliacao e Evolucao de Atletas (MVP)

SaaS web-first para avaliacao de atletas, com Laravel 11 + Filament v3, multi-organizacao simples, geracao de relatorios PDF e base para planos.

## Stack
- Laravel 11
- Filament v3
- MySQL
- DomPDF

## Setup rapido
1) Instale dependencias PHP
```
composer install
```

2) Configure o ambiente
```
cp .env.example .env
php artisan key:generate
```

3) Ajuste o .env para MySQL
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=evolua
DB_USERNAME=root
DB_PASSWORD=
```

4) Rode as migrations + seed
```
php artisan migrate --seed
```

5) (Opcional) Storage link para uploads de logo
```
php artisan storage:link
```

6) Suba o servidor
```
php artisan serve
```

## Acessar o Filament
- URL: http://127.0.0.1:8000/admin
- Usuario owner seed:
  - Email: admin@evolua.test
  - Senha: password

## Geração de PDF
- Clique em "Gerar PDF" na avaliacao.
- Relatorios ficam em `storage/app/reports/{organization_id}/{report_id}.pdf`.
- Para relatórios com visibilidade "shareable", o link publico e `/r/{token}`.

## Comandos solicitados
1) Criar projeto
```
composer create-project laravel/laravel . "11.*"
```

2) Instalar dependencias
```
composer require filament/filament:"^3.0" barryvdh/laravel-dompdf
composer install
```

3) Rodar migrations e seed
```
php artisan migrate --seed
```

4) Subir servidor local
```
php artisan serve
```

5) Acessar o painel Filament
```
http://127.0.0.1:8000/admin
```

6) Credenciais do usuario owner
- Email: admin@evolua.test
- Senha: password
