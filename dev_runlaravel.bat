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
	echo dienstplan container is already running.
	echo Application should be reachable via Docker on http://localhost/
	goto end
)

echo Starting Laravel via Docker Compose...
docker compose up dienstplan
goto end

:local
echo Docker Compose not available. Starting local Laravel server...
php artisan serve

:end
endlocal