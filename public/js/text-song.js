$(document).ready(function () {
    $(".btn-song-text").button().click(function () {
        let audioId = $(this).parent().closest('.audioplayer').children(":first").attr('id');
        console.log(audioId);
        $.ajax({
            type: 'GET',
            url: "/song/text/" + audioId,
            success: success
        });


        let toast = $("#liveToast");
        toast.find(".close").click(function () {
            toast.toast('hide');
        })

        function success(data) {
            console.log(data);
            $(document).ready(function () {
                data = JSON.parse(data);
                if (data.name !== null) {
                    toast.find(".me-auto").text(data.name);
                    toast.find(".toast-body").html(data.text.replaceAll('\n', "<br>"));
                    toast.toast('show');
                } else {
                    toast.find(".me-auto").text("Not found");
                    toast.find(".toast-body").html("Text not found. Do not worry.");
                    toast.toast('show');
                }
            });
        }
    });
});
