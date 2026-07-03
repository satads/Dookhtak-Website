// ============================================
// مرکز آموزش دوختک — منبع واحد حقیقت
// افزودن آموزش جدید = یک آیتم اینجا + یک فایل فرگمنت در content/ — همین.
// ⚠ همه آموزش‌ها نمونه/placeholder هستند — با اپ واقعی تطبیق داده شود.
// Phase 1: static client-side data; Phase 4 rewires this to the API.
// ============================================
export const CATEGORIES = [
  { id: 'start',     title: 'شروع کار',                icon: 'M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z M12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0 M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5' },
  { id: 'customers', title: 'مشتری‌ها',                icon: 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2 M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z' },
  { id: 'orders',    title: 'سفارش‌ها',                icon: 'M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2 M9 2h6v4H9z M12 11h4 M8 11h.01 M12 16h4 M8 16h.01' },
  { id: 'gallery',   title: 'گالری نمونه‌کار',         icon: 'M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z M9 11a2 2 0 1 0 0-4 2 2 0 0 0 0 4z M21 15l-3.09-3.09a2 2 0 0 0-2.82 0L6 21' },
  { id: 'sms',       title: 'پیامک و باشگاه مشتریان',  icon: 'M7.9 20A9 9 0 1 0 4 16.1L2 22Z' },
  { id: 'payment',   title: 'پرداخت و درگاه',          icon: 'M2 7h20v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2z M2 7a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2 M2 11h20' },
  { id: 'account',   title: 'حسابداری',                icon: 'M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8 M12 17.5v-11' },
  { id: 'settings',  title: 'تنظیمات و امکانات بیشتر', icon: 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.01a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.01a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z' }
];

export const TUTORIALS = [
  { id: 'install-android',       category: 'start',     title: 'چطور دوختک را روی گوشی اندروید نصب کنم؟',   duration: 3, hasVideo: true,  updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/install-android.html' },
  { id: 'install-iphone',        category: 'start',     title: 'چطور دوختک را روی آیفون نصب کنم؟',           duration: 3, hasVideo: true,  updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/install-iphone.html' },
  { id: 'signup-login',          category: 'start',     title: 'چطور ثبت‌نام و وارد شوم؟',                    duration: 4, hasVideo: true,  updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/signup-login.html' },
  { id: 'app-tour',              category: 'start',     title: 'آشنایی با صفحه اصلی اپ',                      duration: 5, hasVideo: false, updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/app-tour.html' },
  { id: 'add-customer',          category: 'customers', title: 'چطور مشتری جدید ثبت کنم؟',                    duration: 3, hasVideo: true,  updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/add-customer.html' },
  { id: 'add-measurements',      category: 'customers', title: 'چطور اندازه‌های اندام مشتری را ثبت کنم؟',      duration: 4, hasVideo: true,  updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/add-measurements.html' },
  { id: 'find-edit-customer',    category: 'customers', title: 'چطور مشتری را پیدا و ویرایش کنم؟',            duration: 2, hasVideo: false, updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/find-edit-customer.html' },
  { id: 'first-order',           category: 'orders',    title: 'چطور اولین سفارشم را ثبت کنم؟',               duration: 4, hasVideo: true,  updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/first-order.html' },
  { id: 'order-status',          category: 'orders',    title: 'مراحل و وضعیت سفارش چطور کار می‌کند؟',        duration: 3, hasVideo: false, updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/order-status.html' },
  { id: 'order-deadline',        category: 'orders',    title: 'چطور سررسید تحویل تنظیم کنم؟',                duration: 2, hasVideo: false, updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/order-deadline.html' },
  { id: 'refer-order',           category: 'orders',    title: 'چطور سفارش را به همکار ارجاع بدهم؟',          duration: 4, hasVideo: true,  updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/refer-order.html' },
  { id: 'gallery-upload',        category: 'gallery',   title: 'چطور عکس و فیلم نمونه‌کار آپلود کنم؟',         duration: 4, hasVideo: true,  updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/gallery-upload.html' },
  { id: 'gallery-categories',    category: 'gallery',   title: 'چطور گالری را دسته‌بندی کنم؟',                duration: 2, hasVideo: false, updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/gallery-categories.html' },
  { id: 'gallery-share',         category: 'gallery',   title: 'چطور لینک گالری را برای مشتری بفرستم؟',       duration: 3, hasVideo: true,  updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/gallery-share.html' },
  { id: 'sms-activate',          category: 'sms',       title: 'چطور سامانه پیامکی را فعال کنم؟',             duration: 4, hasVideo: true,  updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/sms-activate.html' },
  { id: 'sms-bulk',              category: 'sms',       title: 'چطور پیامک انبوه بفرستم؟',                    duration: 3, hasVideo: false, updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/sms-bulk.html' },
  { id: 'sms-birthday',          category: 'sms',       title: 'چطور تبریک تولد خودکار را روشن کنم؟',         duration: 2, hasVideo: false, updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/sms-birthday.html' },
  { id: 'sms-dedicated-line',    category: 'sms',       title: 'چطور خط اختصاصی بگیرم؟',                      duration: 3, hasVideo: false, updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/sms-dedicated-line.html' },
  { id: 'payment-activate',      category: 'payment',   title: 'چطور درگاه پرداخت را فعال کنم؟',              duration: 5, hasVideo: true,  updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/payment-activate.html' },
  { id: 'payment-link',          category: 'payment',   title: 'چطور لینک پرداخت بسازم و بفرستم؟',            duration: 3, hasVideo: true,  updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/payment-link.html' },
  { id: 'payment-track',         category: 'payment',   title: 'چطور پرداخت‌ها را پیگیری کنم؟',               duration: 2, hasVideo: false, updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/payment-track.html' },
  { id: 'account-income-expense',category: 'account',   title: 'چطور درآمد و هزینه ثبت کنم؟',                 duration: 4, hasVideo: true,  updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/account-income-expense.html' },
  { id: 'account-cheques',       category: 'account',   title: 'چطور چک ثبت کنم و یادآوری بگیرم؟',            duration: 3, hasVideo: false, updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/account-cheques.html' },
  { id: 'account-reports',       category: 'account',   title: 'گزارش‌های مالی را کجا ببینم؟',                duration: 3, hasVideo: false, updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/account-reports.html' },
  { id: 'custom-domain',         category: 'settings',  title: 'چطور دامنه اختصاصی وصل کنم؟',                 duration: 6, hasVideo: true,  updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/custom-domain.html' },
  { id: 'staff-access',          category: 'settings',  title: 'چطور پرسنل و دسترسی‌ها را مدیریت کنم؟',       duration: 4, hasVideo: false, updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/staff-access.html' },
  { id: 'support-ticket',        category: 'settings',  title: 'چطور تیکت پشتیبانی بفرستم؟',                  duration: 2, hasVideo: false, updated: '[۳۰ خرداد ۱۴۰۵]', file: '/content/support-ticket.html' }
];

// مسیر شروع سریع «از صفر تا اولین سفارش»
export const QUICK_START = ['install-android', 'add-customer', 'first-order', 'sms-activate', 'gallery-upload'];
