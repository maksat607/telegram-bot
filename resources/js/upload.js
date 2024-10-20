$(`body`).on('click', `.attach`, function (e) {
    $('#file').trigger('click');
})
$(`body`).on('change', `#file`, function (e) {
    e.preventDefault();
    var id = $('.chatButton.active').data('id');
    var formData = new FormData();
    let images = $('#file')[0];
    for (let i = 0; i < this.files.length; i++) {
        let file = this.files.item(i);
        formData.append('files[]', images.files[i]);
    }
    $('#loading').append(`<div id="spinner" class="hidden">
          <div class="spinner-inner"></div>
       </div>`);

    $.ajax({
        url: `api/${id}/upload`,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log(response);
            var objDiv = document.getElementById("scrollBar");
            objDiv.scrollTop = objDiv.scrollHeight;

            $(document).ready(function() {
                var objDiv = $("#scrollBar");
                var images = $("img");

                images.on("load", function() {
                    objDiv.scrollTop(objDiv[0].scrollHeight);
                });
            });
            $('#loading').empty();
        },
        error: function(xhr, status, error) {
            var errorMessage = xhr.status + ': ' + xhr.statusText
            console.log('Error - ' + errorMessage);
        }
    });
});


