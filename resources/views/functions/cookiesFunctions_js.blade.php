<script type="text/javascript">

    function createCookie(name,value,days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/; Domain=localhost";
    }

    function check_cookie_name(name)
    {
        var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        if (match) {
            return match[2];
        }
        else{
            console.log('--something went wrong---');
        }
    }

    function setField_with_check_cookie_name(name, field)
    {
        var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        if (match) {
            $(field).val(match[2]);
        }
        else{
            console.log('--something went wrong---');
        }
    }

</script>
