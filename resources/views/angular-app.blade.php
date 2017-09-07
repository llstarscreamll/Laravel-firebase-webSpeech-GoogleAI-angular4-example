<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <script data-ionic="inject">
    (function(w){var i=w.Ionic=w.Ionic||{};i.version='^3.6.0';i.angular='4.1.3';i.staticDir='build/';})(window);
  </script>
  <meta charset="UTF-8">
  <title>Davicompras App</title>

  <!-- Here we put the generated custom Firebase token -->
  <meta name="token" content="{{ session()->get('token') }}">

  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <meta name="msapplication-tap-highlight" content="no">

  <link rel="icon" type="image/x-icon" href="assets/icon/favicon.ico">
  <link rel="manifest" href="manifest.json">
  <meta name="theme-color" content="#387ef5">

  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="apple-touch-icon" href="assets/img/appicon.png">

  <script>
    /*
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('service-worker.js')
        .then(() => console.log('service worker installed'))
        .catch(err => console.log('Error', err));
    }
    */
  </script>

  <link href="build/main.css" rel="stylesheet">

</head>

<body>

  <!-- Ionic's root component and where the app will load -->
  <ion-app></ion-app>

  <!-- The polyfills js is generated during the build process -->
  <script src="build/polyfills.js"></script>

  <!-- The bundle js is generated during the build process -->
  <script src="build/vendor.js"></script>

  <!-- The bundle js is generated during the build process -->
  <script src="build/main.js"></script>

</body>

</html>