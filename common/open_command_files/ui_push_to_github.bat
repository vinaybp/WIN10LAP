@echo off
set REPONAME=WIN10LAP
set THISDIR=%CD%

set THISPLACE=%cd%
set THISPLACE=%THISPLACE:~27,512%
set THISPLACESLASH=%THISPLACE%
set THISPLACEBACKSLASH=%THISPLACE%
set THISPLACE=%THISPLACE:\=-%

set THISPLACESLASH=%THISPLACESLASH:\=/%
rem echo THISPLACE=%THISPLACE%
rem echo THISPLACESLASH=%THISPLACESLASH%
rem echo THISPLACEBACKSLASH=%THISPLACEBACKSLASH%
rem echo REPONAME=%REPONAME%
for %%a in (.) do set currentfolder=%%~na
rem echo current directory name: %currentfolder%

cd c:\UniServer\www\doc\files\

rem type %THISPLACEBACKSLASH%\.public 2>&1

find /c "checked" %THISPLACEBACKSLASH%\.public >nul
if %errorlevel% equ 1 goto notpublic

rem en fait it faut tester par rapport au network pas le nom de la machine
if "%COMPUTERNAME%" == "LAPTOP-7KQRMTC0" ( rem echo this is LAPTOP-7KQRMTC0 WIN10LAP
  rem echo user is mlerman
  git config --global --unset http.proxy
  goto set_proxy
  rem goto done_proxy

)

if "%COMPUTERNAME%" == "WIN7-PC" ( rem echo this is WIN7-PC 
  rem echo user is mlerman
  git config --global --unset http.proxy
  goto done_proxy
)

if "%COMPUTERNAME%" == "DESKTOP-MCQS4FT" ( rem echo this is WIN7-PC 
  rem echo user is mlerman
  git config --global --unset http.proxy
  goto done_proxy

)

if "%COMPUTERNAME%" == "XSJMIKHAELL30" ( rem echo this is XSJMIKHAELL30 
  rem echo user is mikhaell
  git config --global http.proxy proxy:8080
  goto done_proxy
)

:set_proxy
call C:\UniServer\www\doc\files\common\global_settings\HTTP_PROXY.sh.bat
call C:\UniServer\www\doc\files\common\global_settings\PROXY_PORT.sh.bat
git config --global http.proxy %HTTP_PROXY%:%PROXY_PORT%


:done_proxy



git config --global user.email "michael_lerman@yahoo.com"
git config --global user.name "Mikhael Lerman"
git config --global core.safecrlf false
git config --global core.autocrlf false
git config --global status.showUntrackedFiles no

rem git config --global credential.helper "store --file c:\UniServer\www\local\.git-credentials"

rem remove previous add
rem ceci efface les fichier dans le repo github
rem git rm -r --cached c:\UniServer\www\doc\files\ >nul

rem focus only on the current directory
rem untrack all files except this directory
git update-index --assume-unchanged c:\UniServer\www\doc\files\*
git update-index --no-assume-unchanged c:\UniServer\www\doc\files\%THISPLACEBACKSLASH%\*

rem add only this project and subdir

rem git add -A %THISPLACEBACKSLASH%\ 2>&1
git add -A %THISPLACEBACKSLASH%\

rem call c:\UniServer\www\local\set_git_usep.bat 2>&1
call c:\UniServer\www\local\set_git_usep.bat

rem @git remote set-url origin https://%GITUSEP%@github.com/mlerman/%REPONAME%.git 2>&1
@git remote set-url origin https://%GITUSEP%@github.com/mlerman/%REPONAME%.git


rem git status
rem commit only this directory

rem git commit -m "commit for %currentfolder% project from %COMPUTERNAME%" -- %THISPLACEBACKSLASH%\ 2>&1
git commit -m "commit for %currentfolder% project from %COMPUTERNAME%" -- %THISPLACEBACKSLASH%\

rem git push origin master 2>&1
git push origin master

rem returning to the directory
:test
cd %THISDIR%

goto end
:notpublic
echo Not allowed

:end
rem pause
