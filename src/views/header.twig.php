<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Anabel - List composer packages status</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            font-size:11px;
        }
        .status {
            width:20px;
            height:20px;
            margin:0 auto;
        }
        .status-up-to-date {
            background-color:#008b00;
        }
        .status-update-possible {
            background-color:#ffa500;
        }
        .status-semver-safe-update {
            background-color:#f00;
        }
        .warning {
            background-color:#ffa500;
            color:#fff;
            font-weight:500;
        }
    </style>
  </head>
