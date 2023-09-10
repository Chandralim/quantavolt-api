<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Palm Oil X-Tion</title>
        <style>
            body {
                font-family: 'Nunito';
                margin:0px;
                padding:0px;
            }

            video {
              object-fit: cover;
              /* object-fit: contain; */
              /* width: 100vw;
              height: 100vh; */
              /* position: fixed;
              top: 0;
              left: 0; */
            }

            iframe {
              /* object-fit: cover; */
              /* object-fit: contain; */
              width: 100vw;
              height: 100vh;
              position: fixed;
              top: 0;
              left: 0;
            }
        </style>
    </head>
    <body onload="loadit()" id="body" style="width:100vw; height:100vh; margin:0px;">

      <div class="" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
        @if ($image !== '')
        <img src="{{$image}}" alt="" style="max-width:100%; max-height:100%;">
        @elseif ($video != '')
        <video>
          <source src="{{$video}}" type="video/webm">
          Your browser does not support the video tag.
        </video>
        @endif


        <!-- <video>
          <source src="/exposawit2021/mov_bbb.webm" type="video/webm">
          Your browser does not support the video tag.
        </video> -->


        <!-- <iframe src="/exposawit2021/mov_bbb.webm" allow="autoplay; fullscreen" height="100%" width="100%" > -->

      </div>

      <script type="text/javascript">

        // document.getElementById("body").addEventListener('click', function() {
        //   // document.querySelector("video").controls = false;
        //   // document.querySelector('video').play();
        // });

        // setTimeout(()=>{
        //   document.getElementById("body").click();
        //   // console.log("test");
        // },100);
        function loadit() {
          var videoWidth = document.querySelector("video").videoWidth;
          var videoHeight = document.querySelector("video").videoHeight;

          if (videoWidth > videoHeight) {
            document.querySelector("video").width = window.innerWidth;
          }else {
            document.querySelector("video").height = window.innerHeight;
          }
        }

      </script>
    </body>
</html>
