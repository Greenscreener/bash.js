<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>bash.js</title>
        <link rel="stylesheet" href="styles/style.css">
        <link href="https://fonts.googleapis.com/css?family=Ubuntu+Mono:400,700" rel="stylesheet">
    </head>
    <body>
        <div class="wrapper" onclick="document.getElementsByClassName('inputbox')[0].focus(); ">
            <div class="bash" id="bash"></div>
        </div>
        <endora>
        <script src="//cdn.rawgit.com/Caligatio/jsSHA/v2.3.1/src/sha256.js"></script><!-- sha256 hashing libaray -->
        <script src="//scripts.greenscreener.tk/cycle.js"></script><!-- JSON decycling library -->
        <script type="text/javascript" src="/js/classes.js"></script><!-- TextFile and Directory classes -->
        <script type="text/javascript" src="/js/identifiers.js"></script><!-- functions to find/identify something (a directory, file or user) -->
        <script type="text/javascript" src="/js/sendCommand.js"></script><!-- Main command function with the great switch -->
        <script type="text/javascript" src="/js/otherFunctions.js"></script><!-- The rest of the functions. -->
        <script type="text/javascript" src="/js/script.js"></script><!-- main JavaScript code -->
    </body>
</html>
