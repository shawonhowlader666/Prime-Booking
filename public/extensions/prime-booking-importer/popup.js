document.getElementById('btnExtractCopy').addEventListener('click', async () => {
  const statusMsg = document.getElementById('statusMsg');
  statusMsg.style.display = 'block';
  statusMsg.className = 'status-success';
  statusMsg.innerText = 'Extracting hotel data from page...';

  const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });

  chrome.scripting.executeScript({
    target: { tabId: tab.id },
    func: extractPageHotels
  }, (results) => {
    if (results && results[0] && results[0].result) {
      const data = results[0].result;
      navigator.clipboard.writeText(JSON.stringify(data, null, 2));
      statusMsg.className = 'status-success';
      statusMsg.innerText = '✅ Extracted ' + (Array.isArray(data) ? data.length : 1) + ' hotels! Copied to clipboard. Paste into Importer!';
    } else {
      statusMsg.className = 'status-error';
      statusMsg.innerText = '⚠️ Please open an Agoda or Booking.com hotel search page.';
    }
  });
});

document.getElementById('btnOpenImporter').addEventListener('click', () => {
  chrome.tabs.create({ url: 'https://primebooking.com.bd/admin/import-hotels' });
});

function extractPageHotels() {
  let hotels = [];
  
  // Agoda Scraper Logic
  const agodaCards = document.querySelectorAll('[data-selenium="hotel-item"], [data-element-name="property-card"]');
  if (agodaCards.length > 0) {
    agodaCards.forEach(card => {
      const name = card.querySelector('[data-selenium="hotel-name"], h3')?.innerText?.trim();
      const price = card.querySelector('[data-selenium="price-box"] span, .PropertyCardPrice__Value')?.innerText?.replace(/[^0-9]/g, '');
      const rating = card.querySelector('[data-selenium="badge-rating-value"]')?.innerText?.trim();
      const img = card.querySelector('img')?.src;

      if (name) {
        hotels.push({
          name: name,
          starRating: 4,
          ratingScore: rating ? parseFloat(rating) : 4.5,
          price: price ? parseInt(price) : 5500,
          currency: 'BDT',
          images: img ? [img] : []
        });
      }
    });
  }

  // Booking.com Scraper Logic
  const bookingCards = document.querySelectorAll('[data-testid="property-card"]');
  if (bookingCards.length > 0) {
    bookingCards.forEach(card => {
      const name = card.querySelector('[data-testid="title"]')?.innerText?.trim();
      const price = card.querySelector('[data-testid="price-and-discounted-price"]')?.innerText?.replace(/[^0-9]/g, '');
      const rating = card.querySelector('[data-testid="review-score"] div')?.innerText?.trim();
      const img = card.querySelector('img')?.src;

      if (name) {
        hotels.push({
          name: name,
          starRating: 4,
          ratingScore: rating ? parseFloat(rating) : 4.5,
          price: price ? parseInt(price) : 6000,
          currency: 'BDT',
          images: img ? [img] : []
        });
      }
    });
  }

  // Fallback generic extraction
  if (hotels.length === 0) {
    const titles = document.querySelectorAll('h1, h2, h3');
    titles.forEach(t => {
      if (t.innerText && t.innerText.length > 5 && t.innerText.length < 80) {
        hotels.push({
          name: t.innerText.trim(),
          starRating: 4,
          price: 5000,
          images: []
        });
      }
    });
  }

  return hotels.slice(0, 50);
}
