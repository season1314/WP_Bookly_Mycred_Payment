# Bookly myCRED Payment Gateway

A lightweight bridge plugin that seamlessly integrates the **myCRED** reward system into the **Bookly** appointment booking ecosystem. Allow your customers to settle their appointments using accumulated points as a flexible payment alternative.

---

## 🌟 Technical Highlights

* **Zero Database Bloat:** This plugin **does not create any new database tables**. It operates entirely within the existing Bookly and myCRED architectures, ensuring your site remains lean and fast.
* **Non-Intrusive Integration:** Acts as a transparent bridge. It **will not interfere** with the native features, settings, or core workflows of either plugin.
* **Plug-and-Play Compatibility:** Built to respect the original logic of both platforms. Your existing reward rules and booking schedules remain completely intact.

## 🚀 Key Features

* **Points-as-Currency:** Enable a "Pay with myCRED" option at the Bookly checkout.
* **Real-time Balance Verification:** Automatically checks user point balances before confirming the booking.
* **Automated Transaction Logs:** Every payment is recorded within the myCRED history for transparent auditing.

## 🛠 Installation

1.  **Requirement:** Ensure both [Bookly](https://wordpress.org/plugins/bookly-responsive-appointment-booking-tool/) and [myCRED](https://wordpress.org/plugins/mycred/) are installed and activated.
2.  **Upload:** Upload the plugin files to your `/wp-content/plugins/` directory.
3.  **Activate:** Activate the plugin via the WordPress 'Plugins' dashboard.

## ⚙️ Setup & Configuration

To enable point-based payments, please follow these steps to configure your Bookly workflow:

### 1. Enable Pay Locally
Navigate to **Bookly > Settings > Payments** and ensure that the **Pay Locally** option is enabled. This allows the booking to proceed to the final step without an immediate gateway transaction.

### 2. Set Auto-Approval
Under **Bookly > Settings > General**, set the **Default appointment status** to **Approved**. This ensures the booking is confirmed before the user is redirected to the points checkout.

### 3. Configure Redirect URL
In the **Bookly > Appearance** settings, locate the "Done" step (the final step of the booking form). 
* Enable the **"Final Page URL"** or add a custom button.
* Set the target URL to your specialized points-checkout page (where your plugin's shortcode is placed).
* **Crucial:** Append the booking number parameter to the URL as follows:  
  `https://yourdomain.com/checkout-page/?bookNo={booking_number}`  
  *(Note: Replace `{book_id}` with the appropriate Bookly placeholder if different).*

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
