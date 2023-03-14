<script type="text/javascript">
    function clickEvent(event, url, title, text, icon, confirmButtonText, cancelButtonText) {
        event.preventDefault();
        swal.fire({
            title: title,
            text: text,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: confirmButtonText,
            cancelButtonText: cancelButtonText
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "GET",
                    url:url,
                    success: function (response) {
                        console.log('Response:', response);
                        window.location.href = currentLocation;
                    },
                    error: function (response) {
                        console.log('Error:', response);
                    }
                });
            }
        });
    }
</script>
