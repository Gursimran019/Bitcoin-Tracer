@echo off
echo Starting Bitcoin Transaction Viewer...
echo.
echo Make sure PostgreSQL is running with your bitcoin database!
echo.
echo Starting PHP development server on http://localhost:8000
echo.
echo Open your browser and go to: http://localhost:8000/project-with-database.html
echo.
echo Press Ctrl+C to stop the server
echo.
php -S localhost:8000
pause