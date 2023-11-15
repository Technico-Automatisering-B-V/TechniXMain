$(function () {

    var modal = $("#modal");

    var resetTimer = $.timer(function() {
        modal.modal("hide");
    });

    if (modal.length > 0) {
        modal.modal("show");
    }

    modal.on("click", function () {
        modal.modal("hide");
    }).on("hidden", function () {
        reset();
    }).on("hide", function () {
        focusCode();
    }).on("show", function () {
        focusCode();
    }).on("shown", function () {
        $(document).off('focusin.modal');
        focusCode();
        resetTimer.set({ time : 2000, autostart : true });
    });

    $("*").on("click", function () {
        focusCode();
    });

    function reset () {
        window.location.href = "deposit_employees.php";
    }

    function focusCode () {
        $("#code").blur();
        $("#code").focus();
    }
});