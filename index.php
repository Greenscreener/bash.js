<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>bash.js</title>
        <link rel="stylesheet" href="styles/style.css">
    </head>
    <body>
        <div class="wrapper" onclick="document.getElementsByClassName('inputbox')[0].focus(); ">
            <div class="bash" id="bash">
            </div>
        </div>
        <endora>
        <script type="text/javascript">
            var version = "Beta 0.1";
            var inputboxValue = "";
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
                var inputElements = document.getElementsByClassName('inputbox');
                for (var i = 0; i < inputElements.length; i++) {
                    //inputboxValue+=inputElements[i].value;
                    inputElements[i].parentNode.removeChild(inputElements[i]);
                }
                document.getElementById('bash').innerHTML+=" <input type='text' class='inputbox' onfocus='this.style.width = ((this.value.length + 1) * 8) + &quot;px&quot;;' onkeypress='this.style.width = ((this.value.length + 1) * 8) + &quot;px&quot;; inputboxValue = this.value;' value='" + inputboxValue + "'>";
                //document.getElementById('bash').innerHTML+=" <input type='text' class='inputbox' inputboxValue = this.value;' value='" + inputboxValue + "'>";
                inputElements[0].style.width = ((inputElements[0].value.length + 1) * 8) + "px";
                changeBottom();
            }
            changeBottom();
            echo("Bash.js " + version + " tty1 <br> <br> login:");

        </script>
    </body>
</html>
