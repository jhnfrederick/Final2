<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<link rel="icon" href="/img/new/logofinal2.png" type="image/x-icon"/>

<title>Cram-Monkey</title>

@php
    use \App\General;
    use \App\Partial;
    use \App\Homepage;
    use \Carbon\Carbon;
    $currentRoute = \Route::current()->getName();
    $footer = Partial::where('name', 'footer')->first();    
    $footer2 = Partial::where('name', 'footer2')->first();
    $homepageText = Homepage::where('type', 1)->first();
    $header_logo1 = Partial::where('name', 'header_logo_1')->first();
    $header_logo2 = Partial::where('name', 'header_logo_2')->first();
@endphp
<style>
html,body{
    padding:0;
    margin:0;
    height: 100%;
    width: 100%;
}

body{
font-family: 'AvertaStd-Regular', Arial, sans-serif;
}

.leftloginimg {
width: 50%;
background-color: #F8D12B;

}

.image {
margin-left: 20%; 
margin-top: 5%;    
width: 60%;

}

.headerbg2{
    margin-left: 20%;
    margin-top: 2%;
    width: 60%;
}

.textbox {
    
    margin-left: 26%;
    margin-top: 5%;
    width: 40%;
    vertical-align: bottom;
  position: relative;
}
.btnlogin {
    width: 124;
height: 38px;
background: #F8D12B;
border-radius: 8px;
color: #FFFFFF;
border: none;
font-weight: bold;
font-size: 18px;
    margin-left: 45%;
}


input[type=text], input[type=password], input[type=email]{
    width: 140%;
   
 
}



</style>
<link href="{{ asset('css/wzzb.css') }}" rel="stylesheet">
<!-- <iframe src="https://www.tmzb.io/nami_matches" width="80%" height="50%"></iframe> -->
    <script src="{{ asset('js/app.js') }}" defer></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<div style="display:flex; width:100%;  height: 100%;">
<div class="leftloginimg">
<img src="/img/new/headerbg2.png" class="image"> 
<div class="headerbg2">
                <h3><span style="color:#ffffff"><span style="font-family:Verdana,Geneva,sans-serif">Having trouble with your schoolwork?<br><strong>We got you Covered</strong></span></span></h3><br>


<p><span style="font-size:20px"><span style="color:#ffffff"><span style="font-family:Verdana,Geneva,sans-serif">We do the thinking, reading, and problem-solving. You can cram for exams with cram-monkey and earn a degree in no time!</span></span></span></p>
            </div>
    </div>
    
    
    
    
    
<div style="width:50%">
    <div  class="textbox">
    <form class="" method="POST" action="{{ route('register') }}">
    @csrf
  <label for="username">User Name:</label><br>
  <input placeholder="Enter your Username" id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus><br><br>
  <label for="Email">Email:</label><br>
  <input placeholder="Enter your Email" id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" required autocomplete="email" autofocus><br><br>
  <label for="password">Password:</label><br>
  <input placeholder="Minimum of 8 Characters" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password"><br>
  <input type="checkbox" onclick="myFunction()">&emsp;Show Password<br><br>
  <label for="password">Confirm Password:</label><br>
  <input placeholder="Minimum of 8 Characters" id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password"><br>
  <input type="checkbox" onclick="myFunction2()">&emsp;Show Password<br><br>
 </div>
     @error('name')
    <div class="alert alert-danger" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <p> {{ $message }}</p>

            </div>   
    @enderror
            @error('password')
    <div class="alert alert-danger" role="alert">

                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

                <p> {{ $message }}</p>
                <br>
                

            </div>   
    @enderror 
  <button class="btnlogin" type="submit">Join</button>

</form> 



</div>
    </div>

<script>
function myFunction() {
  var x = document.getElementById("password");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
function myFunction2() {
    var y = document.getElementById("cpassword");
  if (y.type === "password") {
    y.type = "text";
  } else {
    y.type = "password";
}
}

</script>
            
           