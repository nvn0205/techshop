@echo off
setlocal enabledelayedexpansion

REM Set start date
set "year=2025"
set "month=03"
set "day=01"

REM Loop through each line in commit-data.txt
set i=0
for /f "usebackq tokens=1,2 delims=|" %%F in ("commit-data.txt") do (
    set /a i+=1
    
    REM Create commit date
    set /a d=!day!+!i!-1
    if !d! lss 10 (set "dstr=0!d!") else (set "dstr=!d!")
    
    REM Stop if exceed 31 days
    if !d! gtr 31 goto :done
    
    set "commit_date=%year%-%month%-!dstr! 10:00:00"
    set "file_path=%%F"
    set "commit_message=%%G"
    
    REM Check if file exists
    if exist "!file_path!" (
        REM Add comment line to file
        echo REM Update !i! - !commit_message! >> "!file_path!"
        
        REM Add and commit
        git add "!file_path!"
        git commit -m "!commit_message!" --date="!commit_date!"
        
        echo Commit !i!: !commit_message! - !file_path!
    ) else (
        echo File !file_path! does not exist, skipping...
    )
)

:done
echo All commits created successfully!
pause