import urllib.request
import ssl

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

url = "https://primebooking.com.bd/storage/uploads/avatars/9FheO7F0vHe1Yt0bWMyZGodfjLwWwbXt2GeYGFD4.jpg"
try:
    req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
    with urllib.request.urlopen(req, context=ctx) as response:
        print(f"Status code: {response.getcode()}")
        print(f"Content-Type: {response.headers.get('Content-Type')}")
        print(f"Length: {len(response.read())} bytes")
except Exception as e:
    print(f"Error fetching URL: {e}")
