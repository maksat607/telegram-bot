import $ from 'jquery';
window.$ = $;
$(document).ready(function () {
    $('.pick').click(function () {
        var emoji = $(this).text();
        var message = $('.replyMessage').val();
        message += emoji;
        $('.replyMessage').val(message);
    });
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
        console.log('id', id);
        getChat(id);
    });
});


async function getChat(customer, pusher = false) {
    try {
        console.log('Request URL:', `${APP_URL}/customer/${customer}/chat`);
        const token = localStorage.getItem('token');
        console.log('Using token:', token);

        if (!token) {
            window.location.href = '/login';
            return;
        }

        const response = await axios({
            method: 'GET',
            url: `${APP_URL}/customer/${customer}/chat`,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            validateStatus: function (status) {
                return status < 500; // Handle all status codes but server errors
            }
        });

        if (response.status === 401) {
            localStorage.removeItem('token');
            window.location.href = '/login';
            return;
        }

        if (!response.data || typeof response.data === 'string') {
            throw new Error('Invalid response format');
        }

        const data = response.data;
        console.log('Chat response:', data);

        // Update UI with chat data
        if ($(`#${data.customer_id} .chatButton`).length) {
            const active = $(`#${data.customer_id} .chatButton`).hasClass('active');
            if (active) {
                $('.convHistory.userBg').empty().append(data.messages);
            }

            $(`#${data.customer_id}`)
                .empty()
                .append(data.customer);

            if (active) {
                $(`#${data.customer_id} .chatButton`).addClass('active');
                // Mark as read with same auth headers
                await axios.get(`${APP_URL}/customer/${customer}/mark`, {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });
            }
        } else {
            $('.chats').prepend(`
                <div id="${data.customer_id}">
                    ${data.customer}
                </div>
            `);
        }

        // Handle scrolling
        const scrollBar = document.getElementById("scrollBar");
        if (scrollBar) {
            scrollBar.scrollTop = scrollBar.scrollHeight;
        }

        // Handle image loading
        $("img").on("load", function() {
            const scrollBar = $("#scrollBar");
            if (scrollBar.length) {
                scrollBar.scrollTop(scrollBar[0].scrollHeight);
            }
        });

    } catch (error) {
        console.error('Chat error:', error);
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
    }
}

$('.sound').on('click', function() {
    const audio = $('#sound')[0]; // Get the audio element
    audio.play();

})
function flash(id) {


    var $myElement = $(`#customer-${id} .chatButton`);

// start the flashing effect
    var flashingInterval = setInterval(function () {
        $myElement.toggleClass('flashing');
    }, 500); // flash every 500ms (0.5 seconds)

// stop the flashing effect after 3 seconds
    setTimeout(function () {
        clearInterval(flashingInterval);
        $myElement.removeClass('flashing');
    }, 3000); // stop after 3000ms (3 seconds)
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

            console.log(`${APP_URL}/customer/${customer}/chat`)
            axios.post(`${APP_URL}/customer/${customer}/chat`, {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'message': message,
            }).then(response => {

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
        console.log(response);
        getChat(response.id, true);
        if(response.from){
            $('.sound').trigger('click');
            console.log(response.id);
            setTimeout(function () {
                flash(response.id);
            }, 500);

        }
        var objDiv = document.getElementById("scrollBar");
        objDiv.scrollTop = objDiv.scrollHeight;
    })
;

setInterval(function () {
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
                // mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/ogg; codecs=opus' });

                mediaRecorder.addEventListener("dataavailable", function(event) {
                    audioChunks.push(event.data);
                });

                mediaRecorder.addEventListener("stop", function() {
                    var audioBlob = new Blob(audioChunks);
                    var formData = new FormData();
                    formData.append('audio', audioBlob);
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    formData.append('_token', csrfToken);
                    var customer = $('.chatButton.active').data('id');
                    $.ajax({
                        url: '/upload-audio/'+customer,

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

            // stopTimeout = setTimeout(function() { // Add a delay of 2 seconds
                mediaRecorder.stop();
                $(this).removeClass("active");
            // }.bind(this), 2000);
        }
    });
});


