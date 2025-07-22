import $ from 'jquery';
import * as Console from "console";
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


    $('body').on('click', '.option.about', function () {
        console.log('About option clicked');
        var customer = $('.chatButton.active').data('id');
        axios.get(`${APP_URL}/user/${customer}/info`).then(response => {
            // console.log('Customer info response:', response.data);
            showCustomerInfo(response.data);
        }).catch(error => {
            console.error('Error fetching customer info:', error);
        });
    });

    function showCustomerInfo(customer) {
        // Populate modal fields
        console.log('Customer data:', customer);
        document.getElementById('customerId').textContent = customer.id || '-';
        document.getElementById('customerTelegramId').textContent = customer.telegram_id || '-';
        document.getElementById('customerName').textContent = customer.fullname || 'Unknown Customer';
        document.getElementById('customerPhone').textContent = customer.phone || 'No phone number';
        document.getElementById('customerUsername').textContent = customer.username ? '@' + customer.username : 'No username';
        document.getElementById('customerCreated').textContent = formatDate(customer.created_at);
        document.getElementById('customerUpdated').textContent = formatDate(customer.updated_at);

        // Update status badge
        const statusBadge = document.getElementById('customerStatus');
        if (customer.active) {
            statusBadge.textContent = 'Active';
            statusBadge.className = 'inline-block px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800';
        } else {
            statusBadge.textContent = 'Inactive';
            statusBadge.className = 'inline-block px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800';
        }

        // Force show the modal with multiple methods
        modal.classList.remove('hidden');
        modal.style.display = 'block';
        modal.style.visibility = 'visible';
        modal.style.opacity = '1';
        modal.style.zIndex = '9999';

        console.log('Modal classes after show:', modal.className);
        console.log('Modal style after show:', modal.style.cssText);
        console.log('Modal should be visible now');
    }

    function closeModal() {
        document.getElementById('customerInfoModal').classList.add('hidden');
    }

    function formatDate(dateString) {
        if (!dateString) return 'Not available';
        const date = new Date(dateString);
        return date.toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

// Close modal when clicking outside of it
    document.getElementById('customerInfoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

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
        const token = localStorage.getItem('token');
        // console.log('Using token:', token);

        const response = await axios.get(`${APP_URL}/customer/${customer}/chat`, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });

        // console.log('Response data:', response.data);

        // Check if response matches expected structure
        const data = response.data;
        console.log('Data:', data);
        if (!data || !data.customer_id || !data.customer || !data.messages) {
            throw new Error('Invalid response structure');
        }

        // Update chat UI
        const chatButtonContainer = $(`#${data.customer_id}`);
        if (chatButtonContainer.length) {
            const isActive = $(`#${data.customer_id} .chatButton`).hasClass('active');

            if (isActive) {
                $('.convHistory.userBg').empty().append(data.messages);
            }

            chatButtonContainer.empty().append(data.customer);

            if (isActive) {
                $(`#${data.customer_id} .chatButton`).addClass('active');
                // Mark as read
                await axios.get(`${APP_URL}/customer/${customer}/mark`, {
                    headers: {
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

        // Handle scroll
        const scrollBar = document.getElementById("scrollBar");
        if (scrollBar) {
            scrollBar.scrollTop = scrollBar.scrollHeight;
        }

    } catch (error) {
        console.error('Chat error:', {
            message: error.message,
            response: error.response?.data,
            status: error.response?.status
        });

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


