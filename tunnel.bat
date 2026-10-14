@echo off
title Cloudflare Tunnel - KSP KOPIN
echo ========================================================
echo   KSP CORE ENTERPRISE SYSTEM - CLOUDFLARE TUNNEL
echo ========================================================
echo.
echo Menghubungkan port lokal 8001 ke domain publik HTTPS Cloudflare...
echo.

"C:\Program Files (x86)\cloudflared\cloudflared.exe" tunnel --url http://127.0.0.1:8001

pause
