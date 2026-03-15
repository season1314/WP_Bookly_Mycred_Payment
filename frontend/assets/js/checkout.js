jQuery(document).ready(function($) {
    const $btn = $('#mycred-confirm-btn');
    const $msg = $('#mycred-msg');
    function showNotification(text, isSuccess) {
        $msg.text(text);
        $msg.removeClass('msg-success msg-error').addClass('show ' + (isSuccess ? 'msg-success' : 'msg-error'));
        
        setTimeout(() => {
            $msg.removeClass('show');
        }, 3000);
    }

    $btn.on('click', function() {
        const paymentId = $(this).data('pid');
        const key = $(this).data('key');
        $btn.prop('disabled', true).text('Processing');
        const formData = new FormData();
        formData.append('action', 'mycred_process_payment');
        formData.append('payment_id', paymentId);
        formData.append('key', key);

        $.ajax({
            url: bnp_vars.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {},
            success: function(data) {
                if (data.success) {
                    showNotification(data.data, true);
                    $btn.text('Paid');
                    const redirectUrl = $btn.data('redirect');
                    setTimeout(() => { window.location.href = redirectUrl; }, 2000);
                } else {
                    showNotification(data.data, false);
                    $btn.prop('disabled', false).text('Confirm');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                showNotification('Network Error, please try again.', false);
                $btn.prop('disabled', false).text('Confirm');
            }
        });
    });
});