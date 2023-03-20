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


            var active = false;
            if ($(`#${response.data.customer_id} .chatButton`).hasClass('active')){
                active = true;
                $('.convHistory.userBg').empty();
                $('.convHistory.userBg').append(response.data.messages);
            }

            $(`#${response.data.customer_id}`).empty();
            $(`#${response.data.customer_id}`).append(response.data.customer)
            if(active){
                $(`#${response.data.customer_id} .chatButton`).addClass('active');
            }
            // convHistory userBg
            var objDiv = document.getElementById("scrollBar");
            objDiv.scrollTop = objDiv.scrollHeight;


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



window.Echo.private('user-1')
    .listen('ApplicationChat',(response) => {
        getChat(response.id);
        var objDiv = document.getElementById("scrollBar");
        objDiv.scrollTop = objDiv.scrollHeight;
    })
;

