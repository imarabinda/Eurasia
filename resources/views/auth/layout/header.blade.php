<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <title>Eurasia - @php 
        if(isset($title)){
         echo $title;
        } else{
            echo 'Decor';
        }@endphp </title>
        <meta content="Eurasia Dashboard" name="description" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        
        @php 
        $favicon_path = \App\Models\Setting::where('name','favicon')->value('value'); 
        @endphp
        <link rel="shortcut icon" href="@php echo Storage::url($favicon_path); @endphp">

        <link href="@php echo asset('assets/css/bootstrap.min.css'); @endphp" rel="stylesheet" type="text/css">
        <link href="@php echo asset('assets/plugins/animate/animate.css'); @endphp" rel="stylesheet" type="text/css">
        <link href="@php echo asset('assets/css/icons.css" rel="stylesheet'); @endphp" type="text/css">
        <link href="@php echo asset('assets/css/style.css" rel="stylesheet'); @endphp" type="text/css">


        <style>
          body{
            background-color: #2a3a4a!important;
          }
          .wrapper-page{
              max-width:350px
          }
        </style>
    </head>


    <body class="fixed-left">
        <!-- Begin page -->
        <!--<div class="accountbg"></div>-->
        <div id="stars"></div>
        <div id="stars2"></div>
        <div class="wrapper-page">