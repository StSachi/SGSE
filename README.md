# SGSE — Sistema de Gestão e Marcação de Eventos (MVP)

Projeto MVP desenvolvido em Laravel (Blade + Tailwind). Este README descreve como configurar e executar localmente no Laragon (MySQL) para desenvolvimento.

Idioma: Português (PT-PT)
Moeda: Kz

Pré-requisitos
- PHP 8.x compatível com a versão do Laravel do projecto
- Composer
- Node.js + npm
- MySQL (Laragon)
- Git

Configuração rápida (Laragon)

1. Clonar o repositório

```powershell
cd C:\laragon\www
git clone <repo-url> sgse
cd sgse
```

2. Instalar dependências PHP e JS

```powershell
composer install
npm install
npm run dev
```

3. Configurar `.env`

Copie o ficheiro de exemplo e ajuste as credenciais MySQL do Laragon (por defeito: user `root`, sem password ou conforme a sua configuração):

```powershell
cp .env.example .env
php artisan key:generate

# Exemplo de variáveis DB (ajuste conforme Laragon):
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=sgse
# DB_USERNAME=root
# DB_PASSWORD=
```

Crie a base de dados `sgse` no MySQL (via HeidiSQL/PhpMyAdmin/Laragon DB manager).

4. Migrations e Seeders

```powershell
php artisan migrate
php artisan db:seed
```

Isto executa as migrations e insere seeds essenciais (configurações e utilizadores de exemplo).

5. Storage link (para imagens)

```powershell
php artisan storage:link
```

6. PDF (opcional)

Para gerar relatórios em PDF recomenda-se instalar `barryvdh/laravel-dompdf`:

```powershell
composer require barryvdh/laravel-dompdf
```

7. Scheduler (cancelamento automático)

O job `CheckPendingDeposits` é agendado no kernel para correr diariamente. Em desenvolvimento pode executar:

```powershell
php artisan schedule:work
```

8. Contas de teste (seeders criam):

- admin@sgse.test / password (role ADMIN)
- func@sgse.test / password (role FUNCIONARIO)
- owner@sgse.test / password (role PROPRIETARIO)
- client@sgse.test / password (role CLIENTE)

9. Comandos úteis

```powershell
# Rodar o servidor local
php artisan serve

# Executar testes
php artisan test
vendor\bin\phpunit

# Executar compilação assets
npm run dev
npm run build
```

10. Notas de desenvolvimento
- Regras de negócio e comentários importantes estão documentados nos Services (`app/Services`) e nos FormRequests.
- Pagamentos são simulados — não existe integração com gateway real.
- Apenas `ADMIN` acede a logs/auditoria.

Commit final sugerido

```powershell
git add .
git commit -m "MVP: Implementação SGSE (PARTE 1-8, UI/UX, testes e documentação)"
```

Problemas conhecidos / próximas melhorias
- Melhorar validações e adicionar testes de integração para fluxos de pagamento.
- UI/UX: tornar páginas mais ricas e responsivas (cards, imagens, galerias).
- Adicionar fila para envios de e-mail e tarefas de longo tempo.

Contacto
- Projeto entregue como código no repositório local; contacte-me para ajustar fluxos, adicionar integração de pagamento real ou deploy.
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
