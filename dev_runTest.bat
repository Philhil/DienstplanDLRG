@echo off
setlocal

where docker >nul 2>nul
if errorlevel 1 goto local

docker compose version >nul 2>nul
if errorlevel 1 goto local

docker compose ps dienstplan --format json >nul 2>nul
if errorlevel 1 goto local

for /f "usebackq delims=" %%i in (`docker compose ps dienstplan --status running --format json 2^>nul`) do set "DIENSTPLAN_RUNNING=1"

if defined DIENSTPLAN_RUNNING (
	echo Running PHPUnit in the dienstplan container...
	docker compose exec dienstplan php vendor/bin/phpunit
	goto end
)

echo Starting a temporary dienstplan container for PHPUnit...
docker compose run --rm dienstplan php vendor/bin/phpunit
goto end

:local
echo Docker Compose not available. Running local PHPUnit...
php .\vendor\bin\phpunit

:end
endlocal