if not exist *.exe goto NOEXE

for %%a in (*.exe) do (
  start %%a
)
goto :EOF

:NOEXE

for %%a in (*.msi) do (
  start %%a
)
