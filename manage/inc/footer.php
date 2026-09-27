

<script>
document.addEventListener(
    'DOMContentLoaded',
    function(){

        var logoutButton =
            document.getElementById(
                'adminLogout'
            );


        if(!logoutButton){
            return;
        }


        logoutButton.onclick = function(){

            confirm(
                '로그아웃 하시겠습니까?',
                function(){

                    location.href =
                        '/manage/proc/logout';

                }
            );

        };

    }
);




</script>
</body>
</html>
