import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

sftp = ssh.open_sftp()
sftp.put(r"c:\Users\Shawon\Desktop\Prime Booking\scratch\test_vendor_manual_form_submission.php", "applications/yayxamnaue/public_html/laravel/test_vendor_form.php")
sftp.close()

cmd = "cd applications/yayxamnaue/public_html/laravel && php test_vendor_form.php"
stdin, stdout, stderr = ssh.exec_command(cmd)
out = stdout.read().decode('utf-8', errors='ignore')
err = stderr.read().decode('utf-8', errors='ignore')
print("[LIVE VENDOR FORM CREATION OUTPUT]:\n" + out.encode('ascii', errors='ignore').decode())
if err:
    print("[LIVE STDERR]:\n" + err.encode('ascii', errors='ignore').decode())

ssh.exec_command("rm -f applications/yayxamnaue/public_html/laravel/test_vendor_form.php")

# Check live URL
stdin, stdout, stderr = ssh.exec_command("curl -s -o /dev/null -w '%{http_code}' 'https://primebooking.com.bd/search?destination=Cox%27s+Bazar'")
status = stdout.read().decode().strip()
print(f"\n[LIVE SEARCH HTTP CODE]: {status}")

ssh.close()
