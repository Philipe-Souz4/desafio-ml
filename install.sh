#!/bin/bash

echo "=========================================="
echo "  Desafio Mercado Livre — Instalacao"
echo "=========================================="

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

ok()    { echo -e "${GREEN}[OK]${NC} $1"; }
erro()  { echo -e "${RED}[ERRO]${NC} $1"; exit 1; }
aviso() { echo -e "${YELLOW}[AVISO]${NC} $1"; }

echo ""
echo "1. Verificando PHP..."
if ! command -v php &> /dev/null; then
    erro "PHP nao encontrado. Instale o PHP 7.4+:\n   Ubuntu: sudo apt install php php-pgsql php-mbstring php-xml php-curl\n   Mac:    brew install php"
fi
php -v | head -n 1
ok "PHP encontrado."

echo ""
echo "2. Verificando Composer..."
if ! command -v composer &> /dev/null; then
    erro "Composer nao encontrado. Instale em https://getcomposer.org\n   Ou execute: curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer"
fi
ok "Composer encontrado."

echo ""
echo "3. Instalando dependencias via Composer..."
composer install --no-interaction --prefer-dist
if [ $? -ne 0 ]; then
    erro "Falha ao instalar dependencias."
fi
ok "Dependencias instaladas."

echo ""
echo "4. Copiando arquivo de configuracao..."
if [ ! -f config/db.php ]; then
    cp config/db.php.example config/db.php
    ok "config/db.php criado. Edite com suas credenciais do PostgreSQL."
else
    aviso "config/db.php ja existe — mantido sem alteracoes."
fi

echo ""
echo "5. Ajustando permissoes..."
chmod -R 777 runtime/ web/assets/ 2>/dev/null
chmod 755 yii 2>/dev/null
ok "Permissoes ajustadas."

echo ""
echo "6. Rodando migrations do banco de dados..."
php yii migrate --interactive=0
if [ $? -ne 0 ]; then
    erro "Falha nas migrations. Verifique as configuracoes em config/db.php"
fi
ok "Migrations executadas."

echo ""
echo "7. Limpando cache..."
php yii cache/flush-all
ok "Cache limpo."

echo ""
echo "=========================================="
echo "  Instalacao concluida com sucesso!"
echo "=========================================="
echo ""
echo "Proximos passos:"
echo "  1. Edite config/db.php com seus dados do PostgreSQL (se ainda nao fez)"
echo "  2. Edite config/params.php com seu meliClientId e meliClientSecret"
echo "  3. Acesse http://localhost/web/ no navegador"
echo "  4. Va em Tokens para configurar o access token do Mercado Livre"
echo ""
