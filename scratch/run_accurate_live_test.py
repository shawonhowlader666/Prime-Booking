import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

sftp = ssh.open_sftp()
sftp.put(r"c:\Users\Shawon\Desktop\Prime Booking\scratch\test_exact_search_error.php", "applications/yayxamnaue/public_html/laravel/test_exact_search_error.php")
sftp.close()

cmd = "cd applications/yayxamnaue/public_html/laravel && php test_exact_search_error.php"
stdin, stdout, stderr = ssh.exec_command(cmd)
print("[LIVE TEST OUTPUT]:\n" + stdout.read().decode())
print("[LIVE TEST STDERR]:\n" + stderr.read().decode())

ssh.exec_command("rm -f applications/yayxamnaue/public_html/laravel/test_exact_search_error.php")
ssh.close()
