@echo off
curl http://127.0.0.1:11434/api/generate ^
-H "Content-Type: application/json" ^
-d "{\"model\":\"mistral\",\"prompt\":\"Hola desde Laravel demo\"}"
pause