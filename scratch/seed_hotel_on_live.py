import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

sftp = ssh.open_sftp()
sftp.put(r"c:\Users\Shawon\Desktop\Prime Booking\scratch\seed_complete_vendor_hotel_with_rooms.php", "applications/yayxamnaue/public_html/laravel/seed_hotel.php")
sftp.close()

cmd = "cd applications/yayxamnaue/public_html/laravel && php seed_hotel.php"
stdin, stdout, stderr = ssh.exec_command(cmd)
out = stdout.read().decode('utf-8', errors='ignore')
err = stderr.read().decode('utf-8', errors='ignore')
print("[LIVE HOTEL SEED OUTPUT]:\n" + out.encode('ascii', errors='ignore').decode())
if err:
    print("[LIVE HOTEL SEED STDERR]:\n" + err.encode('ascii', errors='ignore').decode())

ssh.exec_command("rm -f applications/yayxamnaue/public_html/laravel/seed_hotel.php")

# Now verify with curl
print("\n[VERIFYING LIVE HOTEL DETAIL PAGE]:")
stdin, stdout, stderr = ssh.exec_command("curl -s -o /dev/null -w '%{http_code}' 'https://primebooking.com.bd/property/the-grand-horizon-luxury-palace-water-villas'")
status = stdout.read().decode().strip()
print(f"HTTP Status for https://primebooking.com.bd/property/the-grand-horizon-luxury-palace-water-villas -> {status}")

ssh.close()
