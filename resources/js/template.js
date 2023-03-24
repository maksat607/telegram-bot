$(document).ready(function () {
    /* make side menu show up */
    $(".trigger").click(function () {
        $(".overlay, .menuWrap").fadeIn(180);
        $(".menu").animate({opacity: '1', left: '0px'}, 180);
    });

    /* make config menu show up */
    $(".settings").click(function () {
        $(".config").animate({opacity: '1', right: '0px'}, 180);
        /* hide others */
        $(".menuWrap").fadeOut(180);
        $(".menu").animate({opacity: '0', left: '-320px'}, 180);
    });

    // Show/Hide the other notification options
    $(".deskNotif").click(function () {
        $(".showSName, .showPreview, .playSounds").toggle();
    });

    /* close all overlay elements */
    $(".overlay").click(function () {
        $(".overlay, .menuWrap").fadeOut(180);
        $(".menu").animate({opacity: '0', left: '-320px'}, 180);
        $(".config").animate({opacity: '0', right: '-200vw'}, 180);
    });

    //This also hide everything, but when people press ESC
    $(document).keydown(function (e) {
        if (e.keyCode == 27) {
            $(".overlay, .menuWrap").fadeOut(180);
            $(".menu").animate({opacity: '0', left: '-320px'}, 180);
            $(".config").animate({opacity: '0', right: '-200vw'}, 180);
        }
    });

    //Enable/Disable night mode
    $(".DarkThemeTrigger").click(function () {
        $("body").toggleClass("DarkTheme");
    });

    /* small conversation menu */
    $(".otherOptions").click(function () {
        $(".moreMenu").slideToggle("fast");
    });

    /* clicking the search button from the conversation focus the search bar outside it, as on desktop */
    $(".search").click(function () {
        $(".searchChats").focus();
    });

    /* Show or Hide Emoji Panel */
    $(".emoji").click(function () {
        $(".emojiBar").fadeToggle(120);
    });

    /* if the user click the conversation or the type panel will also hide the emoji panel */
    $(".convHistory, .replyMessage").click(function () {
        $(".emojiBar").fadeOut(120);
    });
});
$(document).ready(function () {
    $('body').on('click', '.option.block', function () {
        var customer = $('.chatButton.active').data('id');
        axios.get(`${APP_URL}/user/${customer}/toggle`).then(response => {
            location.reload();
        });
    });
    $('body').on('click', '.option.delete', function () {
        var customer = $('.chatButton.active').data('id');
        axios.get(`${APP_URL}/user/${customer}/delete`).then(response => {
            location.reload();
        });

    });
    $('body').on('click', '.chatButton', function () {
        $('.chatButton').removeClass('active');
        $(this).addClass('active');
        var id = $(this).data('id');
        getChat(id);
    });
});

async function getChat(customer) {
    await axios.get(`${APP_URL}/customer/${customer}/chat`)
        .then(response => {

            if ($(`#${response.data.customer_id} .chatButton`).length) {
                var active = false;
                if ($(`#${response.data.customer_id} .chatButton`).hasClass('active')) {
                    active = true;
                    $('.convHistory.userBg').empty();
                    $('.convHistory.userBg').append(response.data.messages);
                }

                $(`#${response.data.customer_id}`).empty();
                $(`#${response.data.customer_id}`).append(response.data.customer)
                if (active) {
                    $(`#${response.data.customer_id} .chatButton`).addClass('active');
                }
            } else {
                $('.chats').prepend(
                    `<div id="${response.data.customer_id}">
                        ${response.data.customer}
                     </div>`
                );
            }

            function delay(time) {
                return new Promise(resolve => setTimeout(resolve, time));
            }


            var objDiv = document.getElementById("scrollBar");
            objDiv.scrollTop = objDiv.scrollHeight;


            $(document).ready(function () {
                var objDiv = $("#scrollBar");
                var images = $("img");

                images.on("load", function () {
                    objDiv.scrollTop(objDiv[0].scrollHeight);

                    // if ($('.searchChats').val().length > 0) {
                    //     var keyword = $('.searchChats').val();
                    //     // Find all the messages
                    //     var messages = $(".convHistory").find(".msg");
                    //
                    //     timer = setTimeout(function () {
                    //
                    //     // Loop through all the messages
                    //     messages.each(function () {
                    //         // Get the message text
                    //         var messageText = $(this).text();
                    //
                    //         // Check if the keyword is present in the message text
                    //
                    //         if (messageText.indexOf(keyword) >= 0) {
                    //
                    //             // Add a highlight class to the message
                    //             $(this).addClass("highlight");
                    //
                    //             // Scroll to the message
                    //             console.log($(this).offset().top)
                    //             // var objDiv = document.getElementById("scrollBar");
                    //             // objDiv.scrollTop = objDiv.scrollHeight;
                    //             // objDiv.scrollTop = $(this).offset().top;
                    //
                    //
                    //             var $scrollBar = $('#scrollBar');
                    //             $scrollBar.animate({
                    //                 scrollTop: $(this).offset().top - $scrollBar.offset().top + $scrollBar.scrollTop()
                    //             }, 500);
                    //         }
                    //     });
                    //     }, 10);
                    // }


                });
            });


        }).catch(error => {
            console.log(error)
        });
}

$(document).ready(function () {
    $('.replyMessage').on('keydown', function (event) {
        // Check if Enter key was pressed
        if (event.keyCode === 13) {
            // Get the value of the input field
            const message = $(this).val();
            // Log the message to the console
            console.log(message);
            // Clear the input field
            $(this).val('');
            var customer = $('.chatButton.active').data('id');

            axios.post(`${APP_URL}/customer/${customer}/chat`, {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'message': message,

            })
                .then(response => {

                    console.log(response.data)
                }).catch(error => {
                console.log(error)
            });
        }
    });
});

var timer;
$('.searchChats').on('input', function () {

    clearTimeout(timer); // clear the timer on every input event


    var query = $(this).val();

    let formValue = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        value: query,
    }
    $.ajax({
        url: `/?search=${query}`,
        type: 'get',
    })
        .done(function (data) {
            // console.log(data)
            $('#customers').empty();
            $('#customers').append(data);
            timer = setTimeout(function () {
                $('.chats div .chatButton:first').trigger('click');
            }, 300);

        })
        .fail(function (data) {

        });


});


window.Echo.private('user-1')
    .listen('ApplicationChat', (response) => {
        getChat(response.id);
        var objDiv = document.getElementById("scrollBar");
        objDiv.scrollTop = objDiv.scrollHeight;
    })
;

setInterval(function() {
    $(".audio.active").toggleClass("blink");
}, 500);
$(document).ready(function() {
    var recording = false;
    var audioChunks = [];
    var mediaRecorder;

    $('.audio').mousedown(function() {
        $(this).addClass("active");
        recording = true;
        audioChunks = [];

        navigator.mediaDevices.getUserMedia({audio: true})
            .then(function(stream) {
                mediaRecorder = new MediaRecorder(stream);

                mediaRecorder.addEventListener("dataavailable", function(event) {
                    audioChunks.push(event.data);
                });

                mediaRecorder.addEventListener("stop", function() {
                    var audioBlob = new Blob(audioChunks);
                    var formData = new FormData();
                    formData.append('audio', audioBlob);
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    formData.append('_token', csrfToken);
                    $.ajax({
                        url: '/upload-audio',

                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,

                    }).done(function (data) {
                        console.log(data)

                    })
                        .fail(function (data) {
                            console.log(data)
                        });
                });

                mediaRecorder.start();
            });
    });

    $('.audio').mouseup(function() {
        if (recording) {
            recording = false;
            mediaRecorder.stop();
            $(this).removeClass("active");
        }
    });
});

