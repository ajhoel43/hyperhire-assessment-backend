<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Account Checkpoint Reached (50 Likes)</title>
</head>
<body>
  <h1>Hello, {{ $name }}</h1>
  <p>Your account has reach {{ $likeCount }} likes</p>

  <p>Thanks,<br> Best Regards, {{ config('app.name') }} Team</p>
</body>
</html>