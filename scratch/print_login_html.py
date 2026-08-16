import urllib.request
import ssl

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

url = "https://primebooking.com.bd/login"
req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'})
with urllib.request.urlopen(req, context=ctx) as res:
    html = res.read().decode('utf-8')
    print(f"Status: {res.getcode()}")
    print("Length: ", len(html))
    print(html[:2000])
