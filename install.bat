@echo off
chcp 65001 >nul
echo ==========================================
echo   Desafio Mercado Livre — Instalacao
echo ==========================================

echo.
echo 1. Verificando PHP...
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERRO] PHP nao encontrado. Instale o PHP 7.4+ e adicione ao PATH.
    pause
    exit /b 1
)
php -v | findstr /i "PHP"

echo.
echo 2. Verificando Composer...
composer -V >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERRO] Composer nao encontrado. Instale em https://getcomposer.org
    pause
    exit /b 1
)

echo.
echo 3. Instalando dependencias via Composer...
call composer install --no-interaction --prefer-dist
if %errorlevel% neq 0 (
    echo [ERRO] Falha ao instalar dependencias.
    pause
    exit /b 1
)

echo.
echo 4. Copiando arquivo de configuracao...
if not exist config\db.php (
    copy config\db.php.example config\db.php >nul
    echo [OK] config\db.php criado. Edite com suas credenciais do PostgreSQL.
) else (
    echo [OK] config\db.php ja existe.
)

echo.
echo 5. Rodando migrations do banco de dados...
php yii migrate --interactive=0
if %errorlevel% neq 0 (
    echo [ERRO] Falha nas migrations. Verifique as configuracoes em config\db.php
    pause
    exit /b 1
)

echo.
echo 6. Limpando cache...
php yii cache/flush-all

echo.
echo ==========================================
echo   Instalacao concluida com sucesso!
echo ==========================================
echo.
echo Proximos passos:
echo   1. Edite config\db.php com seus dados do PostgreSQL (se ainda nao fez)
echo   2. Edite config\params.php com seu meliClientId e meliClientSecret
echo   3. Acesse http://localhost/web/ no navegador
echo   4. Va em Tokens para configurar o access token do Mercado Livre
echo.
pause
