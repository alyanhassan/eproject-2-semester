#!/bin/bash
echo ""
echo " =========================================="
echo "   VAXORA - E-Vaccination System"
echo " =========================================="
echo ""
echo " Starting server at: http://localhost:8000"
echo " Press Ctrl+C to stop."
echo ""
php -S localhost:8000 -t VAXORA VAXORA/router.php
