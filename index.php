<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>bash.js</title>
        <link rel="stylesheet" href="styles/style.css">
    </head>
    <body>
        <div class="wrapper">
            <div class="bash" id="bash">
            </div>
        </div>
        <endora>
        <script type="text/javascript">
            var version = "Beta 0.1";
            function changeBottom() {
                var bashTextHeight = document.getElementById('bash').offsetHeight;
                if ((window.innerHeight - bashTextHeight) > 0) {
                    document.getElementById('bash').style.bottom = (window.innerHeight - bashTextHeight) + "px";
                } else {
                    document.getElementById('bash').style.bottom = "0px";
                }
            }

            function echo(input) {
                document.getElementById('bash').innerHTML+=input;
                changeBottom();
            }
            changeBottom();
            echo("Bash.js " + version + " tty1 <br> <br> login:");

        </script>
    </body>
</html>