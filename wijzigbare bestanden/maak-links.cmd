@echo off
setlocal

set "SCRIPT_DIR=%~dp0"
for %%I in ("%SCRIPT_DIR%..") do set "PROJECT_ROOT=%%~fI\"
set "SOURCE_ROOT=%PROJECT_ROOT%wp-content\themes\astra-child"
set "TARGET_ROOT=%SCRIPT_DIR%astra-child"

if not exist "%SOURCE_ROOT%" (
	echo Bronmap niet gevonden:
	echo %SOURCE_ROOT%
	pause
	exit /b 1
)

if not exist "%TARGET_ROOT%" mkdir "%TARGET_ROOT%"

call :make_hardlink "style.css"
call :make_hardlink "functions.php"
call :make_hardlink "footer.php"
call :make_hardlink "single-floru_client.php"

call :make_junction "assets"
call :make_junction "inc"
call :make_junction "template-parts"
call :make_junction "templates"

echo.
echo Klaar. Gesynchroniseerde links zijn aangemaakt in:
echo %TARGET_ROOT%
pause
exit /b 0

:make_hardlink
set "NAME=%~1"
if exist "%TARGET_ROOT%\%NAME%" del /f /q "%TARGET_ROOT%\%NAME%"
mklink /H "%TARGET_ROOT%\%NAME%" "%SOURCE_ROOT%\%NAME%"
exit /b %errorlevel%

:make_junction
set "NAME=%~1"
if exist "%TARGET_ROOT%\%NAME%" rmdir "%TARGET_ROOT%\%NAME%"
mklink /J "%TARGET_ROOT%\%NAME%" "%SOURCE_ROOT%\%NAME%"
exit /b %errorlevel%
