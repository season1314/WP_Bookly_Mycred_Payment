<?php
/**
 * MyCred Checkout Panel Template
 */
?>
<div class="mycred-custom-panel" style="min-width: 400px; max-width:600px;margin: 20px 0; padding: 20px; border: 1px solid #eee; border-radius: 8px; text-align: right;">
    <div style="font-weight: bold; color: #333;">MyCred Checkout</div>
    <div id="mycred-msg" class="mycred-msg"></div>
    <div style="margin-top:20px; font-size:14px; color: #666;">
        Bookly Appointment Payment Id: <strong><?php echo esc_html($payment_id); ?></strong>
    </div>
    
    <div style="margin-top:10px; font-size:20px; font-weight:bold;">Amount to Pay:</div>
    <div style="margin-top:5px; font-size:30px; font-weight:bold; color: #ff8c00;">
        <?php echo esc_html($total); ?>
    </div>
    
    <div style="font-size:14px; margin-top:10px; color: #888;">
        Your balance: <?php echo esc_html($balance); ?>
    </div>

    <button id="mycred-confirm-btn" 
            class="mycred-button" 
            data-pid="<?php echo esc_attr($payment_id); ?>" 
            data-redirect="<?php echo esc_attr($redirect_url); ?>"
            data-key="<?php echo esc_attr($key); ?>"
            style="height: 43px; background: #ff8c00; color: #fff; border: none; padding: 0 30px; border-radius: 4px; cursor: pointer; font-weight: bold; margin-top: 15px;">
        Confirm
    </button>
</div>