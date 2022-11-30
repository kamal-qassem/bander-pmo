@extends('layouts.auth')

@section('header_scripts')
<style>

/*--login start here--*/
 body{
   font-size: 100%;
   background: #0ed2f7; 
   font-family: 'Roboto', sans-serif;
}
a {
  text-decoration: none;
}
a:hover {
  transition: 0.5s all;
  -webkit-transition: 0.5s all;
  -moz-transition: 0.5s all;
  -o-transition: 0.5s all;
}
/*--elemt style strat here--*/
.elelment h2 {
    font-size: 2.5em;
    color: #fff;
    text-align: center;
    margin-top:2em;
    font-weight: 700;
}
.element-main {
    width:50%;
    background: #fff;
    margin:4em auto 0em;
    border-radius: 5px;
    padding:3em 2em;
}
.element-main h1 {
    text-align: center;
    font-size: 2.3em;
    color: #3c8dbc;
    font-weight: 700;
}
.element-main p {
    font-size: 1em;
    color: #696969;
    line-height: 1.5em;
    margin: 1.5em 0em;
    text-align:center;
}
.element-main input[type="email"] {
    font-size: 1em;
    color: #A29E9E;
    padding: 1em 0.5em;
    display: block;
    width: 100%;
    outline: none;
    margin-bottom: 1em;
    text-align:center;
    border: 1px solid #B9B9B9;
}
.element-main input[type="submit"] {
    font-size: 1em;
    color: #fff;
    background: #3c8dbc;
    width: 50%;
    padding: 0.8em 0em;
    outline: none;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    border-bottom: 3px solid #045B99;
    display: block;
    /*margin: 1.5em auto 0;*/
}
.element-main input[type="submit"]:hover{
    background:#1D1C1C;
    border-bottom: 3px solid #2F2F2F;  
    transition: 0.5s all;
  -webkit-transition: 0.5s all;
  -moz-transition: 0.5s all;
  -o-transition: 0.5s all;
}
/*---copyrights--*/
.copy-right {
    margin: 9em 0em 2em 0em;
}
.copy-right p {
    text-align: center;
    font-size:1em;
    color:#fff;
    line-height: 1.5em;

}
.copy-right p a{
  color:#fff;
}
.copy-right p a:hover{
     color:#000;
     transition: 0.5s all;
  -webkit-transition: 0.5s all;
  -moz-transition: 0.5s all;
  -o-transition: 0.5s all;
}
/*--element end here--*/
/*--media quiries start here--*/
@media(max-width:1440px){
    
}
@media(max-width:1366px){
    
}
@media(max-width:1280px){
.elelment h2 {
    margin-top: 1em;    
}
.copy-right {
    margin: 6em 0em 2em 0em;
}
.element-main {
    width: 30%;
}
}
@media(max-width:1024px){
.element-main {
    width: 37%; 
}
}
@media(max-width:768px){
.element-main {
    width: 49%;
}   
.elelment h2 {
    font-size: 2em;
}
.element-main {
    width: 60%;
}
.element-main h1 {
    font-size: 2em;
}
}
@media(max-width:640px){
    
}
@media(max-width:480px){
.element-main {
    width: 80%;
    padding: 3em 1.5em;
}   
.copy-right {
    margin: 5em 0em 2em 0em;
}
.copy-right p {
    font-size: 0.9em;
}
}
@media(max-width:320px){
.elelment h2 {
    font-size: 1.5em;
}
.element-main h1 {
    font-size: 1.5em;
}
.element-main {
    width: 80%;
    margin: 2em auto 0em;
    padding: 1.5em 1.5em;
}
.element-main p {
    font-size: 0.9em;   
}
.element-main input[type="submit"] {
    font-size:0.9em;
    width: 75%;
}
.element-main input[type="email"] {
    font-size: 0.9em;
    padding: 0.8em 0.5em;
}
.copy-right {
    margin: 3em 0em 2em 0em;
}
.copy-right p {
    font-size: 0.85em;
    padding:0 4px;
}
}
/*--media quiries end here--*/

</style>
@endsection
@section('content')
<div class="elelment">
    <h2></h2>
    <div class="element-main">
        <!-- <h1>Forgot Password</h1> -->


            <?php
            $login_logo = getSetting('login_logo','login-settings');
              $login_logo_enable = getSetting('login_logo_enable','login-settings');
              if($login_logo_enable === 'Yes'){
            ?>

             @if($login_logo)
            <p class="single-line"><img src="{{ IMAGE_PATH_SETTINGS.$login_logo }}"  height="76" width="200"></p>  
            @endif

              <?php
            } else {
             ?>

              <h2>{{ getSetting('site_title','site_settings') }}</h2>   

           <?php }  ?>

        @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were problems with input:
                            <br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                
        <p></p>
        <form
            role="form"
            method="POST"
            action="{{ url('password/email') }}"
           >

           <input type="hidden"
                name="_token"
                value="{{ csrf_token() }}">
            <div >   
            <input type="email" value="{{ old('email') }}" name="email" placeholder="Please enter your email address">
            <input type="submit" value="{{ trans('quickadmin.qa_reset_link') }}">
            <span style="float:right; margin-top: -34px;">@lang('quickadmin.qa_click-here-to-login', ['url' => route('login')]) </span>
        </div>
        </form>
    </div>
</div>
@endsection