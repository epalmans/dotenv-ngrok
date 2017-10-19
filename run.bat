@echo off

start "ngrok" /MIN "ngrok" http -host-header=myapp.url 80	& :: start ngrok

timeout 5 > NUL							& :: allow ngrok a little time to setup its tunnels

php -f ngrok-dotenv-update.php "C:\path\to\myapp\.env"		& :: update .env
