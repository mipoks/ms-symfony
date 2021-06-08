$(document).ready(function () {
    $(".btn-song-share").button().click(function () {
        let item = $(this).parent().closest('.audioplayer').clone().get();
        console.log(item);
        $(".post-modal").append(item);
    });
    $(".close-modal").button().click(function () {
        $(this).parent().parent().find('.audioplayer').remove();
    });
});
