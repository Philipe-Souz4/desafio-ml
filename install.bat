@echo off
echo ==========================================
echo Instalando Dependencias e Banco de Dados
echo ==========================================

echo.
echo 1. Instalando pacotes via Composer...
call composer install

echo.
echo 2. Rodando Migrations do Banco de Dados...
php yii migrate --interactive=0

echo.
echo 3. Limpando o Cache...
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