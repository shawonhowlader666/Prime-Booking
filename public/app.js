/**
 * Agoda Booking Flow Interactive Logic
 * Supports 1:1 Agoda behavior:
 * - Real-time countdown timer
 * - Multi-step booking & payment flow
 * - Interactive form validation
 * - Special requests customization
 * - Payment gateway simulation (Card, bKash, PayPal, Pay at Property)
 * - Confirmation receipt & voucher generation
 */

document.addEventListener("DOMContentLoaded", () => {
  initCountdownTimer();
  initUserDropdown();
  initFormValidation();
  initSpecialRequests();
  initPaymentTabs();
  initStepNavigation();
  initCardFormatting();
});

// ==========================================================================
// 1. COUNTDOWN TIMER (19:50)
// ==========================================================================
function initCountdownTimer() {
  const timerElement = document.getElementById("timerCountdown");
  if (!timerElement) return;

  // 19 minutes 50 seconds in seconds
  let remainingSeconds = 19 * 60 + 50;

  function updateDisplay() {
    const hours = Math.floor(remainingSeconds / 3600);
    const minutes = Math.floor((remainingSeconds % 3600) / 60);
    const seconds = remainingSeconds % 60;

    const formatted = `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}:${String(seconds).padStart(2, "0")}`;
    timerElement.textContent = formatted;

    if (remainingSeconds > 0) {
      remainingSeconds--;
    } else {
      clearInterval(timerInterval);
      showToast("Notice: Room rates are subject to update due to high demand.");
    }
  }

  updateDisplay();
  const timerInterval = setInterval(updateDisplay, 1000);
}

// ==========================================================================
// 2. USER PROFILE DROPDOWN
// ==========================================================================
function initUserDropdown() {
  const badge = document.getElementById("userBadge");
  const dropdown = document.getElementById("userDropdown");

  if (!badge || !dropdown) return;

  badge.addEventListener("click", (e) => {
    e.stopPropagation();
    badge.classList.toggle("open");
    dropdown.classList.toggle("show");
  });

  document.addEventListener("click", () => {
    badge.classList.remove("open");
    dropdown.classList.remove("show");
  });

  dropdown.addEventListener("click", (e) => {
    e.stopPropagation();
  });
}

// ==========================================================================
// 3. FORM VALIDATION & INTERACTIVE INPUTS
// ==========================================================================
function initFormValidation() {
  const fields = [
    { input: document.getElementById("firstNameInput"), container: document.getElementById("firstNameField"), validator: (val) => val.trim().length > 0 },
    { input: document.getElementById("lastNameInput"), container: document.getElementById("lastNameField"), validator: (val) => val.trim().length > 0 },
    { input: document.getElementById("emailInput"), container: document.getElementById("emailField"), validator: (val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val.trim()) },
    { input: document.getElementById("phoneInput"), container: document.getElementById("phoneField"), validator: (val) => val.replace(/\D/g, "").length >= 7 }
  ];

  fields.forEach(({ input, container, validator }) => {
    if (!input || !container) return;

    // Set initial validation
    if (validator(input.value)) {
      container.classList.add("valid-field");
    }

    input.addEventListener("focus", () => {
      container.classList.add("focused");
    });

    input.addEventListener("blur", () => {
      container.classList.remove("focused");
      if (validator(input.value)) {
        container.classList.add("valid-field");
        container.classList.remove("error");
      } else {
        container.classList.remove("valid-field");
        container.classList.add("error");
      }
    });

    input.addEventListener("input", () => {
      if (validator(input.value)) {
        container.classList.add("valid-field");
        container.classList.remove("error");
      } else {
        container.classList.remove("valid-field");
      }
    });
  });

  // Residence country change
  const residenceSelect = document.getElementById("residenceSelect");
  const residenceField = document.getElementById("residenceField");
  if (residenceSelect && residenceField) {
    residenceSelect.addEventListener("change", () => {
      if (residenceSelect.value) {
        residenceField.classList.add("valid-field");
      }
    });
  }
}

// ==========================================================================
// 4. SPECIAL REQUESTS TOGGLE & PILLS
// ==========================================================================
function initSpecialRequests() {
  const toggleHeader = document.getElementById("specialRequestsToggle");
  const content = document.getElementById("specialRequestsContent");
  const chevron = document.getElementById("specialRequestsChevron");

  if (toggleHeader && content) {
    toggleHeader.addEventListener("click", () => {
      const isExpanded = content.classList.toggle("expanded");
      if (chevron) {
        chevron.style.transform = isExpanded ? "rotate(180deg)" : "rotate(0deg)";
      }
    });
  }

  // Request pills selection
  const pills = document.querySelectorAll(".request-pill");
  pills.forEach((pill) => {
    pill.addEventListener("click", () => {
      pill.classList.toggle("selected");
    });
  });
}

// ==========================================================================
// 5. PAYMENT TABS
// ==========================================================================
function initPaymentTabs() {
  const tabs = document.querySelectorAll(".payment-tab");
  const panels = document.querySelectorAll(".payment-panel");

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      tabs.forEach((t) => t.classList.remove("active"));
      panels.forEach((p) => p.classList.remove("active"));

      tab.classList.add("active");
      const targetPanel = document.getElementById(tab.dataset.panel);
      if (targetPanel) {
        targetPanel.classList.add("active");
      }
    });
  });
}

// ==========================================================================
// 6. CARD INPUT FORMATTING
// ==========================================================================
function initCardFormatting() {
  const cardNum = document.getElementById("cardNumberInput");
  const cardExpiry = document.getElementById("cardExpiryInput");
  const cardCvv = document.getElementById("cardCvvInput");

  if (cardNum) {
    cardNum.addEventListener("input", (e) => {
      let value = e.target.value.replace(/\D/g, "").substring(0, 16);
      let formatted = value.match(/.{1,4}/g)?.join(" ") || value;
      e.target.value = formatted;
    });
  }

  if (cardExpiry) {
    cardExpiry.addEventListener("input", (e) => {
      let value = e.target.value.replace(/\D/g, "").substring(0, 4);
      if (value.length >= 2) {
        e.target.value = value.substring(0, 2) + "/" + value.substring(2);
      } else {
        e.target.value = value;
      }
    });
  }

  if (cardCvv) {
    cardCvv.addEventListener("input", (e) => {
      e.target.value = e.target.value.replace(/\D/g, "").substring(0, 4);
    });
  }
}

// ==========================================================================
// 7. STEP NAVIGATION & BOOKING SUBMISSION
// ==========================================================================
function initStepNavigation() {
  const step1Container = document.getElementById("step1Container");
  const step2Container = document.getElementById("step2Container");
  const step3Container = document.getElementById("step3Container");

  const stepItem1 = document.getElementById("stepItem1");
  const stepItem2 = document.getElementById("stepItem2");
  const stepItem3 = document.getElementById("stepItem3");

  const divider1 = document.getElementById("divider1");
  const divider2 = document.getElementById("divider2");

  const btnProceedToPayment = document.getElementById("btnProceedToPayment");
  const btnBackToStep1 = document.getElementById("btnBackToStep1");
  const btnSubmitPayment = document.getElementById("btnSubmitPayment");
  const btnBkashPay = document.getElementById("btnBkashPay");
  const btnDownloadVoucher = document.getElementById("btnDownloadVoucher");
  const btnPrintReceipt = document.getElementById("btnPrintReceipt");
  const btnBookAnother = document.getElementById("btnBookAnother");

  // Go to Payment Step
  if (btnProceedToPayment) {
    btnProceedToPayment.addEventListener("click", () => {
      const firstName = document.getElementById("firstNameInput").value.trim();
      const lastName = document.getElementById("lastNameInput").value.trim();
      const email = document.getElementById("emailInput").value.trim();
      const phone = document.getElementById("phoneInput").value.trim();

      if (!firstName || !lastName || !email) {
        showToast("Please fill in all required guest fields marked with *");
        return;
      }

      // Populate summary names in confirmation
      const guestFullName = `${firstName} ${lastName}`;
      document.querySelectorAll(".summary-guest-name").forEach((el) => {
        el.textContent = guestFullName;
      });
      document.querySelectorAll(".summary-guest-email").forEach((el) => {
        el.textContent = email;
      });
      document.querySelectorAll(".summary-guest-phone").forEach((el) => {
        el.textContent = `+880 ${phone || "1712345678"}`;
      });

      // Switch view
      step1Container.classList.remove("active-step");
      step2Container.classList.add("active-step");

      // Stepper updates
      stepItem1.classList.remove("active");
      stepItem1.classList.add("completed");
      stepItem2.classList.add("active");
      if (divider1) divider1.classList.add("filled");

      window.scrollTo({ top: 0, behavior: "smooth" });
      showToast("Proceeding to secure payment");
    });
  }

  // Back to Step 1
  if (btnBackToStep1) {
    btnBackToStep1.addEventListener("click", () => {
      step2Container.classList.remove("active-step");
      step1Container.classList.add("active-step");

      stepItem1.classList.remove("completed");
      stepItem1.classList.add("active");
      stepItem2.classList.remove("active");
      if (divider1) divider1.classList.remove("filled");

      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  // Submit Payment (Simulate 1.2s loading)
  function processPaymentSimulation(button) {
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = `
      <svg style="animation: spin 1s linear infinite; width:18px; height:18px; margin-right:8px;" viewBox="0 0 24 24" fill="none">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"></circle>
        <path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
      </svg> Processing Payment...
    `;

    setTimeout(() => {
      button.disabled = false;
      button.innerHTML = originalText;

      // Switch to confirmation
      step2Container.classList.remove("active-step");
      step3Container.classList.add("active-step");

      stepItem2.classList.remove("active");
      stepItem2.classList.add("completed");
      stepItem3.classList.add("active");
      stepItem3.classList.add("completed");
      if (divider2) divider2.classList.add("filled");

      // Generate random booking ID
      const randomRef = "PRM-2026-" + Math.floor(1000000 + Math.random() * 9000000);
      const bookingRefEl = document.getElementById("bookingRefId");
      if (bookingRefEl) bookingRefEl.textContent = randomRef;

      window.scrollTo({ top: 0, behavior: "smooth" });
      showToast("Payment Successful! Booking confirmed.");
    }, 1200);
  }

  if (btnSubmitPayment) {
    btnSubmitPayment.addEventListener("click", () => {
      processPaymentSimulation(btnSubmitPayment);
    });
  }

  if (btnBkashPay) {
    btnBkashPay.addEventListener("click", () => {
      processPaymentSimulation(btnBkashPay);
    });
  }

  // Download voucher
  if (btnDownloadVoucher) {
    btnDownloadVoucher.addEventListener("click", () => {
      window.open("voucher.html", "_blank");
    });
  }

  // Print receipt
  if (btnPrintReceipt) {
    btnPrintReceipt.addEventListener("click", () => {
      window.print();
    });
  }

  // Book another
  if (btnBookAnother) {
    btnBookAnother.addEventListener("click", () => {
      step3Container.classList.remove("active-step");
      step1Container.classList.add("active-step");

      stepItem1.className = "step-item active";
      stepItem2.className = "step-item";
      stepItem3.className = "step-item";
      if (divider1) divider1.className = "step-divider";
      if (divider2) divider2.className = "step-divider";

      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }
}

// ==========================================================================
// 8. TOAST NOTIFICATIONS
// ==========================================================================
function showToast(message) {
  let toast = document.getElementById("globalToast");
  if (!toast) {
    toast = document.createElement("div");
    toast.id = "globalToast";
    toast.className = "toast-notice";
    document.body.appendChild(toast);
  }

  toast.innerHTML = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="12" cy="12" r="10"></circle>
      <line x1="12" y1="8" x2="12" y2="12"></line>
      <line x1="12" y1="16" x2="12.01" y2="16"></line>
    </svg>
    <span>${message}</span>
  `;

  toast.classList.add("show");
  setTimeout(() => {
    toast.classList.remove("show");
  }, 3500);
}

// Add spinning animation style for processing
const style = document.createElement("style");
style.textContent = `
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
`;
document.head.appendChild(style);
