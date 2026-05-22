# Distinção Empresarial — Pesquisa de Lembrança de Marca

Sistema web em **Laravel** para aplicar a pesquisa **Distinção Empresarial** (ACIBA / Bagé-RS): formulários por segmento econômico, coleta de respostas em campo, validação e relatórios.

Desenvolvido em parceria com a consultoria da URCAMP (Inov@).

---

## Funcionalidades

- Cadastro e parametrização de formulários (passos, perguntas, opções)
- **Fatores de satisfação** em dois blocos por segmento:
  - *Caso não lembre o nome* (ex.: não conheço, conheço mas não lembro)
  - *Por que lembra dessa empresa?* (qualidade, preços, etc.)
- Resposta online com etapas, barra de progresso e geolocalização
- Validação de envios e tratamento de respostas
- Relatórios e dashboard administrativo
- Controle de disponibilidade por data (início/fim do formulário)

---

## Requisitos

- PHP 8.2+
- Composer 2.x
- Node.js 18+ e npm (assets front-end)
- MySQL 8+ (produção) ou SQLite (desenvolvimento local)

---

## Instalação local

```bash
git clone https://github.com/SEU_USUARIO/distincao-empresarial.git
cd distincao-empresarial

composer install
cp .env.example .env
php artisan key:generate

# SQLite (padrão no .env.example)
touch database/database.sqlite
php artisan migrate

npm install
npm run build
```

Criar usuário administrador:

```bash
php artisan db:seed --class=UserSeeder
# Login: SEED_ADMIN_EMAIL / SEED_ADMIN_PASSWORD do .env
```

Carregar os 5 questionários oficiais (126 segmentos):

```bash
php artisan db:seed --class=DistincaoEmpresarialSeeder --force
```

O seeder **apaga** formulários, respostas e envios existentes antes de recriar (`$limparAntes = true`).

Servidor de desenvolvimento:

```bash
php artisan serve
```

---

## Estrutura dos questionários

Os segmentos ficam em `database/data/distincao_questionarios.php`:

| Questionário | Segmentos |
|--------------|-----------|
| 1 | 26 |
| 2 | 24 |
| 3 | 23 |
| 4 | 24 |
| 5 | 29 |
| **Total** | **126** |

Cada questionário vira um formulário separado no sistema, com passo demográfico + um passo por segmento.

---

## Variáveis de ambiente importantes

| Variável | Descrição |
|----------|-----------|
| `DB_CONNECTION` | `mysql` em produção (obrigatório na KingHost) |
| `SESSION_DRIVER` | `database` ou `file` |
| `SEED_ADMIN_*` | Credenciais do admin apenas para seed local |

Nunca commite o arquivo `.env`.

---

## Deploy (resumo)

1. Envie o código sem `vendor/`, `node_modules/`, `.env`
2. No servidor: `composer install --no-dev --optimize-autoloader`
3. Configure `.env` com MySQL e `DB_CONNECTION=mysql`
4. `php artisan migrate --force`
5. `php artisan config:cache` e `php artisan route:cache`
6. Permissões em `storage/` e `bootstrap/cache/`
7. Document root apontando para `public/` (na KingHost: pasta `www`)

```bash
php artisan config:clear
php artisan db:seed --class=DistincaoEmpresarialSeeder --force
```

---

## O que não vai para o Git

- `.env` (senhas, `APP_KEY`, banco)
- `/vendor`, `/node_modules`, `/public/build`
- `storage/logs`, sessões e cache
- `composer.phar`, `vendor.zip`, `index.htm` (página padrão da hospedagem)

---

## Licença

Projeto acadêmico / institucional. Código da aplicação sob licença MIT (Laravel). Uso dos dados da pesquisa sujeito à política da ACIBA.
