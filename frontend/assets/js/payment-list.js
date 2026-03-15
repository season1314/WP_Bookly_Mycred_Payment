jQuery(document).ready(function($) {
    const $container = $(".bnp-payment-table-container");
    const $msg = $("#mycred-msg");
    $(document).on("click", ".next-page, .prev-page", function(e) {
        e.preventDefault();
        const page = $(this).data("page");
        bnpPagination(page);
    });

    window.bnpPagination = function(page) {
        $.ajax({
            url: bnp_vars.ajax_url,
            type: "GET",
            data: {
                action: "mycred_payment_list",
                page: page
            },
            beforeSend: function() {
                $container.css('opacity', 0.5);
            },
            success: function(response) {
                if (response.success) {
                    if ($container.length) {
                        $container.html(response.data.html);
                    }
                } else {
                    console.error("Server returned error:", response);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            },
            complete: function() {
                $container.css('opacity', 1);
            }
        });
    };
});