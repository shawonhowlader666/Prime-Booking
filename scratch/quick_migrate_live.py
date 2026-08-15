import paramiko

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect("168.144.27.74", port=22, username="master_gxpvmujegu", password="76uG9CVVcKa22", timeout=10)

sftp = ssh.open_sftp()
# Upload missing migrations directly
sftp.put(r"c:\Users\Shawon\Desktop\Prime Booking\database\migrations\2026_08_16_004500_add_realtime_location_and_policy_fields_to_properties_table.php", "applications/yayxamnaue/public_html/laravel/database/migrations/2026_08_16_004500_add_realtime_location_and_policy_fields_to_properties_table.php")
sftp.put(r"c:\Users\Shawon\Desktop\Prime Booking\database\migrations\2026_08_16_000004_make_property_fields_nullable_safe.php", "applications/yayxamnaue/public_html/laravel/database/migrations/2026_08_16_000004_make_property_fields_nullable_safe.php")
sftp.close()

# Run migrate
cmd = "cd applications/yayxamnaue/public_html/laravel && php artisan migrate --force && php artisan optimize:clear"
stdin, stdout, stderr = ssh.exec_command(cmd)
print("[MIGRATE OUT]:\n" + stdout.read().decode())
print("[MIGRATE ERR]:\n" + stderr.read().decode())

# Test live search with curl
stdin, stdout, stderr = ssh.exec_command("curl -s -o /dev/null -w '%{http_code}' 'https://primebooking.com.bd/search?destination=Sundarbans&q=Gulshan'")
status = stdout.read().decode().strip()
print(f"\n[LIVE SEARCH HTTP CODE]: {status}")

ssh.close()
