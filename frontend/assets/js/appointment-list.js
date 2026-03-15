jQuery(document).ready(function ($) {
    const $container = $(".bnp-payment-table-container");
    let page = 1
    let status = ""

    $(document).on("click", ".next-page, .prev-page", function (e) {
        e.preventDefault();
        page = $(this).data("page");
        status = $(this).data("status")
        bnpPagination(page, status);
    });


    $(document).on("click", ".cancel-btn", function (e) {
        const $overlay = $("#bnp-confirm-overlay")
        const $msg = $("#mycred-msg");
        const $confirmBox = $("#bnp-confirm-box")
        const id = $(this).data("id");
        const type = $(this).data("type")
        const advance = $(this).data("advance");
        const key = $(this).data("key")
        const text = type == "cancel" ? "Are you sure cancel this appointment?" : "Are you sure you finished this conversation"
        e.preventDefault();
        $confirmBox.find('p').first().text(text);
        $overlay.css('display','flex');
        const yesBtn = $("#bnp-confirm-yes");
        const noBtn = $("#bnp-confirm-no");
        yesBtn.on("click", async function () {
            $("#bnp-confirm-yes").prop("disabled", true);
            $("#bnp-confirm-no").prop("disabled", true);
            let result;
            if(type == "cancel"){
                result = await window.cancel(id,advance,key)
            }else{
                result = await window.complete(id)
            }
            const $msg = $("#mycred-msg");
            $msg.text(result.data);
            if (result.code == "success") {
                $msg.removeClass("msg-success msg-error").addClass("show msg-success");
            } else {
                $msg.removeClass("msg-success msg-error").addClass("show msg-error");
            }
            $overlay.css('display', 'none');
            setTimeout(() => { $msg.removeClass("show"); }, 3000);
            if (result.code == "success") { bnpPagination(page, status) }
            return
        })
        noBtn.on("click", function () {
            $overlay.css('display', 'none')
        })
    })
    window.cancel = async function (id,advance,key) {
        try {
            const formData = new FormData();
            formData.append("action", "cancel_appointment_by_customer");
            formData.append("id", id);
            formData.append("advance", advance);
            formData.append("key", key);
            const result = await new Promise((resolve, reject) => {
                $.ajax({
                    url: bnp_vars.ajax_url,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.success) {
                            resolve({ code: "success", data: data.data });
                        } else {
                            resolve({ code: "error", data: data.data });
                        }
                    },
                    error: function (xhr, status, error) {
                        reject({ code: "error", message: error });
                    }
                });
            });

            return result;
        } catch (error) {
            console.error("Error:", error);
            return { code: "error", message: error.message };
        }
    };

    window.complete = async function (id) {
        try {
            const formData = new FormData();
            formData.append("action", "complete_appointment_by_customer");
            formData.append("id", id);
            const result = await new Promise((resolve, reject) => {
                $.ajax({
                    url: bnp_vars.ajax_url,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.success) {
                            resolve({ code: "success", data: data.data });
                        } else {
                            resolve({ code: "error", data: data.data });
                        }
                    },
                    error: function (xhr, status, error) {
                        reject({ code: "error", message: error });
                    }
                });
            });

            return result;
        } catch (error) {
            console.error("Error:", error);
            return { code: "error", message: error.message };
        }
    };

    window.bnpPagination = function (page, status) {
        $.ajax({
            url: bnp_vars.ajax_url,
            type: "GET",
            data: {
                action: "mycred_appointments_list",
                page: page,
                status: status
            },
            beforeSend: function () {
                $container.css('opacity', 0.5);
            },
            success: function (response) {
                if (response.success) {
                    if ($container.length) {
                        $container.html(response.data.html);
                    }
                } else {
                    console.error("Server returned error:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
            },
            complete: function () {
                $container.css('opacity', 1);
            }
        });
    };
});