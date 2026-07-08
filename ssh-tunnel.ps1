while ($true) {
    & "C:\Windows\System32\OpenSSH\ssh.exe" -N -o BatchMode=yes -o ExitOnForwardFailure=yes -o ServerAliveInterval=30 -o ServerAliveCountMax=3 -o StrictHostKeyChecking=accept-new -i "$env:USERPROFILE\.ssh\id_ed25519" -L 3306:127.0.0.1:3306 root@185.98.137.219
    Start-Sleep -Seconds 5
}
