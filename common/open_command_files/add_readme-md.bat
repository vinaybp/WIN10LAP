@echo off
echo running %0
set THISPLACE=%1
rem substring from /doc
set THISPLACE=%THISPLACE:~16,512%
rem change backslash characters into -
set THISPLACE=%THISPLACE:\=/%
echo THISPLACE=%THISPLACE%

for %%a in (.) do set currentfolder=%%~na
echo current directory name: %currentfolder%

for %%a in (..) do set parentfolder=%%~na
echo parent directory name: %parentfolder%

echo currentfolder : %currentfolder%>readme.md
echo.>>readme.md
echo parentfolder : %parentfolder%>>readme.md
echo.>>readme.md
echo HOST : %HOST%>>readme.md
echo.>>readme.md
echo URLDIR : %URLDIR%>>readme.md
echo.>>readme.md
echo TARGETDIR : %TARGETDIR%>>readme.md
echo ___>>readme.md
:test
rem echo [%currentfolder% - %HOST%]^(http://%HOST%%URLDIR%open-command-prompt-here.html^)>>readme.md
set MACHINESERVING=win7-pc
echo [%currentfolder% - %MACHINESERVING%]^(http://%MACHINESERVING%%URLDIR%open-command-prompt-here.html^)>>readme.md
set MACHINESERVING=celine-pc
echo [%currentfolder% - %MACHINESERVING%]^(http://%MACHINESERVING%%URLDIR%open-command-prompt-here.html^)>>readme.md
set MACHINESERVING=xsjmikhaell30
echo [%currentfolder% - %MACHINESERVING%]^(http://%MACHINESERVING%%URLDIR%open-command-prompt-here.html^)>>readme.md
set MACHINESERVING=laptop-7kqrmtc0
echo [%currentfolder% - %MACHINESERVING%]^(http://%MACHINESERVING%%URLDIR%open-command-prompt-here.html^)>>readme.md

rem pause