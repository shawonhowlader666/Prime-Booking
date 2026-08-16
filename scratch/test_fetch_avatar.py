import urllib.request
import ssl

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

url = "https://primebooking.com.bd/storage/uploads/avatars/2oYnFAxmZJUgKzsYUYlhaF0RyUpjiW8K8jyihn9N.jpg"
try:
    req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'})
    with urllib.request.urlopen(req, context=ctx) as response:
        print(f"Status code: {response.getcode()}")
        print(f"Content-Type: {response.headers.get('Content-Type')}")
        data = response.read()
        print(f"Length: {len(data)} bytes")
        print(f"First 10 bytes: {list(data[:10])}")
except Exception as e:
    print(f"Error fetching URL: {e}")
