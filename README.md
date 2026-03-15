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

  
## 🔄 Booking & Payment Logic

The plugin implements a "Reserve-and-Pay" workflow to ensure schedule integrity while preventing unpaid "ghost" bookings:

1.  **Appointment Creation:** When a user initiates a booking, the **Appointment Status** is immediately set to `Approved` to temporarily lock the time slot.
2.  **Pending Payment:** Simultaneously, the **Payment Status** is marked as `Pending`.
3.  **15-Minute Grace Period:** * **Success:** If the user completes the myCRED points payment within **15 minutes**, the Payment Status is updated to `Completed`.
    * **Expiration/Failure:** If the payment is not completed within the 15-minute window, the system automatically triggers a cleanup:
        * **Appointment Status** is changed to `Rejected` (releasing the time slot).
        * **Payment Status** is changed to `Rejected`.
4.  **Non-Intrusive:** This logic is isolated to the myCRED payment flow and **does not interfere** with other Bookly payment methods or standard workflows.

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


Got it! My apologies—let's get that back into professional English. Here is the final, comprehensive README.md incorporating all the logic, shortcodes, and refund rules we discussed.

Markdown
# Bookly myCRED Payment Gateway

A professional, lightweight bridge plugin that seamlessly integrates the **myCRED** reward system into the **Bookly** appointment booking ecosystem. Allow your customers to pay for their appointments using points while maintaining automated status management and refund logic.

---

## 🌟 Technical Highlights

* **Zero Database Bloat:** This plugin **does not create any new database tables**. It fully utilizes existing Bookly and myCRED meta structures.
* **Non-Intrusive Integration:** Acts as a transparent bridge. It **will not interfere** with the native features, settings, or core workflows of either plugin.
* **Smart Automation:** Handles appointment locking, payment timeouts, and conditional refunds automatically.

---

## 🧱 Shortcodes Reference

### 1. Appointment Management List
`[bnp_bookly_appointment_list status="completed" key="mycred_default" advance="3600"]`

* **`status`**: Filter appointments by type (e.g., `completed`, `cancelled`, `approved`, `done`).
* **`key`**: The specific myCRED point type used for refunds/reversals.
* **`advance`**: **Refund Grace Period (in seconds)**. Default is `3600` (1 hour). Users can only receive a point refund if they cancel the appointment more than X seconds before the start time.

### 2. Pending Payment List
`[bnp_bookly_payment_list redirect="https://yourdomain.com/checkout"]`

* **`redirect`**: The URL of your "Checkout/Cashier" page. This list displays all appointments with a `Pending` payment status and provides a link to pay.

### 3. myCRED Payment Desk (Cashier)
`[bnp_mycred_desk key="mycred_default" redirect="https://yourdomain.com/success"]`

* **`key`**: The myCRED point type key to be deducted for the payment.
* **`redirect`**: The destination URL after a successful transaction.



---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

