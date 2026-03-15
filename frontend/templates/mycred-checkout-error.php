<?php
/**
 * MyCred Checkout Panel Error Template
 */
?>
<div class="mycred-custom-panel" style="min-width: 400px; max-width:600px;margin: 20px 0; padding: 20px; border: 1px solid #eee; border-radius: 8px; text-align: right;">
    <div style="font-weight: bold; color: #333;">MyCred Checkout</div>
            <div class="error-text" style="margin-top:30px;"><?php echo esc_html($error); ?></div>
    <button id="mycred-confirm-btn"
    class="mycred-button" 
    style="height: 43px; background: #ff8c00; color: #fff; border: none; padding: 0 30px; border-radius: 4px; cursor: pointer; font-weight: bold; margin-top: 15px;"data-redirect="<?php echo esc_attr($redirect_url); ?>">
    Back
</button>
</div>
<script>
    (function() {
        const btn = document.getElementById('mycred-confirm-btn');
        btn.addEventListener('click', function() {
            const redirectUrl = btn.getAttribute('data-redirect');
            window.location.href = redirectUrl;
            
        });
    })();
</script>
<style>
    .error-text {
        color: #d32f2f !important; 
        text-transform: uppercase;
        font-weight: bold;
        letter-spacing: 1px;
        font-size: 28px;
    }

</style>