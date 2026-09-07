/**
 * HotelAir PMS Enterprise Multi-Language & i18n Translation Engine
 * Architecture: Word-Boundary Safe Regex Compiler (Zero-Corruption Guaranteed)
 */

const BN_DICTIONARY = {
    // Specific Long Sentences & Phrases
    'Manage all active staff, assign enterprise roles, and monitor user privileges.': 'সকল সক্রিয় স্টাফ পরিচালনা করুন, রোল বরাদ্দ করুন এবং সুযোগ-সুবিধা নিয়ন্ত্রণ করুন।',
    'Permitted to login': 'লগইন অনুমোদিত',
    'Sanitization team': 'পরিচ্ছন্নতাকর্মী দল',
    'Reservation operators': 'রিজার্ভেশন অপারেটর',
    'System Wide': 'সিস্টেম জুড়ে',
    'All active roles': 'সকল সক্রিয় পদবী',
    'System Operators & Access List': 'সিস্টেম অপারেটর ও এক্সেস তালিকা',
    'System Operators': 'সিস্টেম অপারেটরবৃন্দ',
    'Registered Users': 'নিবন্ধিত ইউজার',
    'Active & Online Staff': 'সক্রিয় ও অনলাইন স্টাফ',
    'Front Desk & Reception': 'ফ্রন্ট ডেস্ক ও রিসেপশন',
    'Housekeeping Staff': 'হাউসকিপিং স্টাফ',
    'Add New Staff': 'নতুন স্টাফ যুক্ত করুন',
    'Staff Member': 'স্টাফ সদস্য',
    'Designation / Department': 'পদবী / বিভাগ',
    'Registered On': 'নিবন্ধনের তারিখ',
    'General Manager': 'সাধারণ ম্যানেজার',
    'Housekeeper': 'হাউসকিপার',
    'Receptionist': 'রিসেপশনিস্ট',
    'Accountant': 'হিসাবরক্ষক',
    'Owner': 'মালিক',
    'Suspend': 'স্থগিত করুন',
    'Suspended': 'স্থগিত',
    'Actions': 'অ্যাকশন',
    'Action': 'অ্যাকশন',
    'Center': 'সেন্টার',
    'Operators': 'অপারেটরবৃন্দ',
    'Operator': 'অপারেটর',
    'System': 'সিস্টেম',
    'Monitor': 'তদারকি',
    'Privileges': 'সুযোগ-সুবিধা',
    'Enterprise': 'এন্টারপ্রাইজ',

    // Top Navigation, Header & Breadcrumbs
    'Home': 'হোম',
    'English': 'English',
    'Bengali': 'Bengali',
    'Hindi': 'Hindi',
    'Spanish': 'Spanish',
    'Arabic': 'Arabic',
    'Search...': 'অনুসন্ধান করুন...',
    'Search': 'অনুসন্ধান',
    'Recent Search': 'সাম্প্রতিক অনুসন্ধান',
    'Help & Docs': 'সহায়তা ও ডকুমেন্টস',
    'Team Members': 'টিম সদস্যবৃন্দ',
    'New around here? Sign up': 'নতুন সদস্য? সাইন আপ করুন',
    'Prime Booking OTA Live Sync Hub': 'প্রাইম বুকিং ওটিএ লাইভ সিঙ্ক হাব',
    'Light': 'লাইট মোড',
    'Dark': 'ডার্ক মোড',
    'Auto': 'অটো মোড',
    'Template Setting': 'টেমপ্লেট সেটিংস',
    'Set Theme Color': 'থিম কালার নির্বাচন',
    'Border Radius none': 'বর্ডার রেডিয়াস বন্ধ',
    'Sidebar Icon color': 'সাইডবার আইকন কালার',
    'Card box shadow active': 'কার্ড শ্যাডো সক্রিয়',
    'Reset Defaults': 'রিসেট ডিফল্ট',
    'Notifications': 'বিজ্ঞপ্তি',
    'Mark all as read': 'সব পঠিত হিসেবে চিহ্নিত করুন',
    'View all notifications': 'সকল বিজ্ঞপ্তি দেখুন',
    'Signed in as': 'লগইন আছেন',
    'Set status': 'স্ট্যাটাস নির্ধারণ',
    'Profile': 'প্রোফাইল',
    'My Profile': 'আমার প্রোফাইল',
    'Account Setting': 'অ্যাকাউন্ট সেটিংস',
    'Settings': 'সেটিংস',
    'Activity': 'অ্যাক্টিভিটি',
    'Billing': 'বিলিং',
    'Upgrade': 'আপগ্রেড',
    'Lockscreen': 'লকস্ক্রিন',
    'Sign Out': 'লগআউট',
    'New folder': 'নতুন ফোল্ডার',
    'File upload': 'ফাইল আপলোড',
    'Google docs': 'গুগল ডক্স',
    'Google sheets': 'গুগল শিটস',
    '+ Invite': '+ ইনভাইট করুন',
    'Generate report': 'রিপোর্ট তৈরি করুন',
    'New Reports': 'নতুন রিপোর্ট',
    'Filter Options': 'ফিল্টার অপশন',
    'Sorted by:': 'সর্ট ক্রম:',
    'Choose...': 'নির্বাচন করুন...',
    'Enabled': 'চালু',
    'Reset': 'রিসেট',
    'Apply': 'প্রয়োগ করুন',

    // Navigation Sections & Modules
    'HOTELS': 'হোটেল ও বুকিং',
    'HRMS': 'এইচআর ও পেরোল',
    'TUNING': 'সিস্টেম টিউনিং',
    'Overview': 'একনজরে',
    'Dashboard': 'ড্যাশবোর্ড',
    'Hotel Activity': 'হোটেল অ্যাক্টিভিটি',
    'Hotels': 'হোটেলসমূহ',
    'Hotel': 'হোটেল',
    'Transaction': 'লেনদেন তালিকা',
    'Room Book': 'রুম বুকিং',
    'Booking List': 'বুকিং তালিকা',
    'Room Booking': 'রুম বুকিং',
    'Room Checkout': 'রুম চেক-আউট',
    'Room Status': 'রুমের অবস্থা',
    'Room Matrix Grid': 'রুম ক্যালেন্ডার গ্রিড',
    'Room Facilites': 'রুম সুবিধাসমূহ',
    'Facilites List': 'সুবিধার তালিকা',
    'Facilites Details': 'সুবিধার বিবরণ',
    'Room Size': 'রুমের আয়তন',
    'House keeping': 'হাউসকিপিং',
    'Assign Room': 'রুম বরাদ্দ',
    'Room Cleaning': 'রুম পরিষ্কারকরণ',
    'Checklist': 'ক্লিনিং চেকলিস্ট',
    'QR List': 'কিউআর তালিকা',
    'Cleaning Report': 'পরিষ্কারকরণ রিপোর্ট',
    'Cab Facility': 'গাড়ি সুবিধা',
    'Cab List': 'গাড়ির তালিকা',
    'Cab Booking': 'গাড়ি বুকিং',
    'Items Manage': 'মালামাল পরিচালনা',
    'Items Unit List': 'আইটেম ইউনিট তালিকা',
    'Items List': 'আইটেম তালিকা',
    'Items Destroyed': 'নষ্ট মালামাল',
    'Items Category': 'আইটেম ক্যাটাগরি',
    'Items Suppliers': 'সরবরাহকারী তালিকা',
    'Personalised': 'ব্যক্তিগত সেবা',
    'Jacuzzi': 'জাকুজি বাথ',
    'Swimming pool': 'সুইমিং পুল',
    'Spa': 'স্পা ও ওয়েলনেস',
    'Pedicure': 'পেডিকিউর',
    'Theater': 'সিনেমা থিয়েটার',
    'Banquet': 'ব্যাংকুয়েট হল',
    'All Settings': 'সকল সেটিংস',
    'Booking Reports': 'বুকিং রিপোর্ট',
    'Purchase Reports': 'ক্রয় রিপোর্ট',
    'Stock Reports': 'স্টক রিপোর্ট',
    'Bed List': 'বেড তালিকা',
    'Booking Type List': 'বুকিংয়ের ধরন',
    'Floor Plan List': 'ফ্লোর প্ল্যান',
    'HR Dashboard': 'এইচআর ড্যাশবোর্ড',
    'All Employees': 'সকল কর্মচারী',
    'Leave Tracking': 'ছুটি ট্র্যাকিং',
    'Attendance': 'উপস্থিতি খাতা',
    'Departments': 'বিভাগসমূহ',
    'Employees Loan': 'কর্মচারী ঋণ',
    'Appraisal': 'পারফরম্যান্স মূল্যায়ন',
    'Add Employees': 'নতুন কর্মচারী যুক্ত করুন',
    'Employee Salary': 'কর্মচারী বেতন',
    'Payslip': 'পে-স্লিপ',
    'User & Access Control': 'ইউজার ও এক্সেস কন্ট্রোল',
    'Prime OTA Sync': 'প্রাইম ওটিএ লাইভ সিঙ্ক',
    'Common': 'সাধারণ সেটিংস',
    'Payment Set': 'পেমেন্ট গেটওয়ে',
    'Accounts': 'হিসাবরক্ষণ',
    'Role Managment': 'ভূমিকা ও অনুমতি',
    'Role Add': 'রোল তৈরি',
    'Role List': 'রোল তালিকা',
    'Role Permission': 'রোল পারমিশন',
    'Role Create': 'নতুন পারমিশন',
    'Role Assign': 'রোল প্রদান',
    'Role Access': 'এক্সেস লেভেল',
    'Front Pages': 'পাবলিক পেজ',
    'About Us': 'আমাদের সম্পর্কে',
    'Contact Us': 'যোগাযোগ',
    'Privacy Policy': 'গোপনীয়তা নীতি',
    'Terms Conditions': 'শর্তাবলী',
    'Application': 'অ্যাপস',
    'My Calendar': 'আমার ক্যালেন্ডার',
    'Inbox': 'ইনবক্স',
    'Chat': 'সরাসরি চ্যাট',
    'Blog Article': 'ব্লগ আর্টিকেল',
    'File Manager': 'ফাইল ম্যানেজার',

    // Dashboard KPIs, Cards & Charts
    'Today Booking List': 'আজকের বুকিং তালিকা',
    'Today Bookings': 'আজকের বুকিং',
    'Today Booking': 'আজকের বুকিং',
    'Total Amount': 'মোট টাকার পরিমাণ',
    'Total Customer': 'মোট কাস্টমার / গেস্ট',
    'Total Revenue': 'মোট আয়',
    'From previous period': 'পূর্ববর্তী সময়ের চেয়ে',
    'New income': 'নতুন আয়',
    'Reservations': 'রিজার্ভেশন চার্ট',
    'Occupancy Rate': 'রুম বুকিং হার (অকুপেন্সি)',
    'Available Rooms': 'বর্তমান খালি রুম',
    'Today Check In / Out': 'আজকের চেক-ইন ও চেক-আউট',
    'Month Revenue': 'চলতি মাসের মোট আয়',
    'Total Bookings': 'সর্বমোট বুকিং',
    'Total Rooms': 'মোট রুম সংখ্যা',
    'Occupied Rooms': 'বুকড রুম',
    'Dirty / Cleaning': 'ক্লিনিং অপেক্ষমাণ',
    'Maintenance': 'মেরামতাধীন রুম',

    // Table Headers & Columns
    'Name': 'গেস্টের নাম',
    'Room Type': 'রুমের ধরন',
    'Check In': 'চেক-ইন',
    'Check Out': 'চেক-আউট',
    'Paid Amount': 'পরিশোধিত টাকা',
    'Due Amount': 'বকেয়া টাকা',
    'Payment Status': 'পেমেন্ট স্ট্যাটাস',
    'Status': 'অবস্থা',
    'Room No': 'রুম নম্বর',
    'Guest Name': 'অতিথির নাম',
    'Phone': 'মোবাইল নম্বর',
    'Staff Code': 'স্টাফ কোড',
    'Role': 'পদবী',
    'Contact': 'যোগাযোগ',
    'Joined Date': 'যোগদানের তারিখ',
    'Shift': 'শিফট',
    'Total': 'মোট',
    'Subtotal': 'সাবটোটাল',
    'Discount': 'ছাড়',
    'Tax': 'ট্যাক্স / ভ্যাট',
    'Grand Total': 'সর্বমোট',
    'Price': 'মূল্য',
    'Quantity': 'পরিমাণ',
    'Rate': 'দর',
    'Amount': 'টাকা',
    'Date': 'তারিখ',
    'Time': 'সময়',

    // Room Types & Categories
    'Single': 'সিঙ্গেল রুম',
    'Deluxe': 'ডিলাক্স রুম',
    'Studio': 'স্টুডিও রুম',
    'Junior Suite': 'জুনিয়র স্যুট',
    'Queen': 'কুইন রুম',
    'King': 'কিং রুম',
    'Executive': 'এক্সিকিউটিভ স্যুট',
    'Standard': 'স্ট্যান্ডার্ড রুম',
    'Suite': 'লাক্সারি স্যুট',

    // Status Badges & States
    'Paid': 'পরিশোধিত',
    'Unpaid': 'অপরিশোধিত',
    'Pending': 'অপেক্ষমাণ',
    'Confirmed': 'নিশ্চিতকৃত',
    'Completed': 'সম্পন্ন',
    'Cancelled': 'বাতিলকৃত',
    'Available': 'খালি আছে',
    'Occupied': 'বুকড আছে',
    'Dirty': 'পরিষ্কার প্রয়োজন',
    'Cleaning': 'পরিষ্কার হচ্ছে',
    'Active': 'সক্রিয়',
    'Online': 'অনলাইন',
    'Offline': 'অফলাইন',
    'Front Desk': 'ফ্রন্ট ডেস্ক',
    'Housekeeping': 'হাউসকিপিং',
    'Manager': 'ম্যানেজার',
    'Admin': 'অ্যাডমিন',
    'Staff': 'স্টাফ',
    'Super Admin': 'সুপার অ্যাডমিন',
    'Morning': 'সকাল',
    'Evening': 'বিকাল',
    'Night': 'রাত',
    'General': 'সাধারণ',

    // Calendar & Timing
    'Today': 'আজ',
    'Yesterday': 'গতকাল',
    'Tomorrow': 'আগামীকাল',
    'January': 'জানুয়ারি',
    'February': 'ফেব্রুয়ারি',
    'March': 'মার্চ',
    'April': 'এপ্রিল',
    'May': 'মে',
    'June': 'জুন',
    'July': 'জুলাই',
    'August': 'আগস্ট',
    'September': 'সেপ্টেম্বর',
    'October': 'অক্টোবর',
    'November': 'নভেম্বর',
    'December': 'ডিসেম্বর',
    'Monday': 'সোমবার',
    'Tuesday': 'মঙ্গলবার',
    'Wednesday': 'বুধবার',
    'Thursday': 'বৃহস্পতিবার',
    'Friday': 'শুক্রবার',
    'Saturday': 'শনিবার',
    'Sunday': 'রবিবার',

    // Modals, Forms & Actions
    'Add Booking': 'নতুন বুকিং',
    'Quick Walk-in Booking': 'তাত্ক্ষণিক ওয়াক-ইন বুকিং',
    'New Reservation / Add Booking': 'নতুন রিজার্ভেশন / বুকিং যুক্ত করুন',
    'Guest First Name *': 'গেস্টের প্রথম নাম *',
    'Guest Last Name': 'গেস্টের শেষ নাম',
    'Phone Number *': 'মোবাইল নম্বর *',
    'NID / Passport No': 'এনআইডি / পাসপোর্ট নম্বর',
    'Room Type *': 'রুমের ধরন *',
    'Assign Room Unit': 'নির্দিষ্ট রুম বরাদ্দ',
    'Auto-Assign Room': 'স্বয়ংক্রিয় রুম বরাদ্দ',
    'Check-in Date *': 'চেক-ইন তারিখ *',
    'Check-out Date *': 'চেক-আউট তারিখ *',
    'Paid Advance (৳)': 'অগ্রিম পেমেন্ট (৳)',
    'Special Notes / Preferences': 'বিশেষ নির্দেশনা বা পছন্দ',
    'Confirm Reservation': 'রিজার্ভেশন নিশ্চিত করুন',
    'Add Staff Member': 'নতুন স্টাফ সদস্য যুক্ত করুন',
    'Staff Full Name': 'স্টাফের পূর্ণ নাম',
    'Email Address': 'ইমেইল অ্যাড্রেস',
    'Department': 'বিভাগ',
    'Account Password': 'অ্যাকাউন্ট পাসওয়ার্ড',
    'Assigned Shift': 'নির্ধারিত শিফট',
    'Create Staff Account': 'স্টাফ অ্যাকাউন্ট তৈরি করুন',
    'Save Changes': 'সংরক্ষণ করুন',
    'Close': 'বন্ধ করুন',
    'Cancel': 'বাতিল',
    'Submit': 'জমা দিন',
    'Filter': 'ফিল্টার',
    'Export': 'এক্সপোর্ট',
    'Print': 'প্রিন্ট',
    'Download': 'ডাউনলোড',
    'Copy': 'কপি',
    'Excel': 'এক্সেল',
    'PDF': 'পিডিএফ',
    'CSV': 'সিএসভি',
    'View All': 'সব দেখুন',
    'Details': 'বিস্তারিত',
    'Edit': 'সম্পাদনা',
    'Delete': 'মুছে ফেলুন',
    'View': 'দেখুন',

    // DataTable UI & Pagination
    'Show': 'দেখান',
    'entries': 'টি রেকর্ড',
    'Showing': 'দেখান হচ্ছে',
    'to': 'থেকে',
    'of': 'মোট',
    'Previous': 'পূর্ববর্তী',
    'Next': 'পরবর্তী',
    'First': 'প্রথম',
    'Last': 'শেষ',
    'Success': 'পরিশোধিত',
    'Booking Confirmed': 'নিশ্চিতকৃত বুকিং',
    'Booking Pending': 'অপেক্ষমাণ বুকিং',
    'No data available in table': 'টেবিলে কোনো তথ্য নেই',
    'Filtered from': 'ফিল্টারকৃত',
    'total entries': 'মোট রেকর্ড'
};

// Sort keys longest first to match full phrases before individual words
const SORTED_KEYS = Object.keys(BN_DICTIONARY).sort((a, b) => b.length - a.length);

// Pre-compile Regex with Word Boundaries (\b) so short words NEVER corrupt longer words
const COMPILED_RULES = SORTED_KEYS.map(key => {
    const escaped = key.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    // For alphanumeric words/phrases, use word boundaries \b to prevent sub-word corruption
    const isPureWords = /^[a-zA-Z0-9\s]+$/.test(key);
    const regex = isPureWords ? new RegExp('\\b' + escaped + '\\b', 'gi') : new RegExp(escaped, 'g');
    return {
        regex: regex,
        val: BN_DICTIONARY[key]
    };
});

function applyTranslation(isBangla) {
    function walk(node) {
        if (node.nodeType === Node.TEXT_NODE) {
            if (!node._originalEn) {
                node._originalEn = node.nodeValue;
            }
            if (isBangla) {
                let str = node._originalEn;
                for (let i = 0; i < COMPILED_RULES.length; i++) {
                    str = str.replace(COMPILED_RULES[i].regex, COMPILED_RULES[i].val);
                }
                node.nodeValue = str;
            } else {
                node.nodeValue = node._originalEn;
            }
        } else if (node.nodeType === Node.ELEMENT_NODE) {
            const tag = node.tagName.toLowerCase();
            if (tag === 'script' || tag === 'style' || tag === 'code') return;
            if (node.id && node.id.startsWith('lang-btn-')) return;

            if (node.hasAttribute('placeholder')) {
                if (!node._originalPl) node._originalPl = node.getAttribute('placeholder');
                if (isBangla) {
                    let pl = node._originalPl;
                    for (let i = 0; i < COMPILED_RULES.length; i++) {
                        pl = pl.replace(COMPILED_RULES[i].regex, COMPILED_RULES[i].val);
                    }
                    node.setAttribute('placeholder', pl);
                } else {
                    node.setAttribute('placeholder', node._originalPl);
                }
            }

            for (let i = 0; i < node.childNodes.length; i++) {
                walk(node.childNodes[i]);
            }
        }
    }
    walk(document.body);
}

function setAppLanguage(lang) {
    localStorage.setItem('hotelair_lang', lang);
    $('.dropdown-menu a[id^="lang-btn-"]').removeClass('active');
    $('#lang-btn-' + lang).addClass('active');

    const isBangla = (lang === 'bn');
    applyTranslation(isBangla);

    // Re-apply on dynamically loaded components
    setTimeout(() => { applyTranslation(isBangla); }, 200);
    setTimeout(() => { applyTranslation(isBangla); }, 600);
}

// Auto Initialize on DOM Ready
$(function() {
    const savedLang = localStorage.getItem('hotelair_lang') || 'en';
    if (savedLang === 'bn') {
        setAppLanguage('bn');
    } else {
        $('.dropdown-menu a[id^="lang-btn-"]').removeClass('active');
        $('#lang-btn-' + savedLang).addClass('active');
    }
});
