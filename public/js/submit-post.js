$(document).ready(function () {
    $(".btn-submit-post").button().click(function () {
        let audioplayers = $(this).parent().parent().find('.audioplayer');
        let audioId;
        let songs = [];
        $.each(audioplayers, function (index, value) {
            audioId = value.firstChild.id;
            console.log(audioId);
            songs.push(audioId);
        });
        let textarea = $.trim($("textarea").val());
        console.log(songs);
        let toSend = {text: textarea, songs: songs};
        console.log(toSend);
        $.ajax({
            type: 'POST',
            data: toSend,
            url: "/post/add",
            success: success
        });

        let toast = $("#liveToast");
        toast.find(".close").click(function () {
            toast.toast('hide');
        })

        function success(data) {
            console.log(data);
            $('.close-modal').click();
            $(document).ready(function () {
                if (data.result !== undefined) {
                    toast.find(".me-auto").text(data.result);
                    toast.find(".toast-body").html(data.text.replaceAll('\n', "<br>"));
                    toast.toast('show');
                }
            });
        }
    });
});
