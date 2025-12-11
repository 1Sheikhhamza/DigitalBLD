@extends('layouts.app_details')

@section('seodetails')
	@php
     if($articles!='' && $articles->image!=""){
    	$pageimages = asset('uploads/article/'.$articles->image);
     }
     else{
     	$pageimages = asset('assets/front/images/logo.png');
     }
    if($articles!='' && $articles->image!=""){
      $metadetails =  Str::limit(strip_tags($articles->meta_details),200);
    }
    else{
      $metadetails =  '';
    }
    @endphp
                    
	<title>{{ $articles ? $articles->title : '' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="keywords" content="{{ $articles ? $articles->keywords : '' }}">  
    <meta name="description" content="{{ $metadetails }}"/>
    <meta name="distribution" content="global"/>
    <meta name="coverage" content="Worldwide"/>
    <meta name="revisit-after" content="1 day">
    <meta property="og:url"           content="{{ route('content',$menuslug ? $menuslug->uri : '') }}"/>
    <meta property="og:type"            content="Website"/> 
    <meta property="og:site_name"          content="Seven Billion Perfume"/>
    <meta property=fb:pages content="1550283541950127"/>
    <meta property="og:title"         content="{{ $articles ? $articles->title : '' }}"/>
    <meta property="og:description"   content="{{ $metadetails }}"/>
    <meta property="og:image"         content="{{ $pageimages }}"/>
    <meta name="robots" content="index, follow">
    <meta name="Googlebot" content="index, follow"/> 
    <meta property="og:locale" content="bn_BD"/>
    <meta property="og:locale:alternate" content="en_US"/>
    <meta property="og:locale:alternate" content="en_GB"/>
    <meta property="og:locale:alternate" content="fr_FR"/>
    <meta property="og:locale:alternate" content="en_IN"/> 
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="Seven Billion Perfume">
    <meta name="twitter:title" content="Seven Billion Perfume">
    <meta name="twitter:description" content="{{ $metadetails }}">
    <meta name="twitter:image:src" content="{{ $pageimages }}">  
    <link rel="alternate" href="http://www.sevenbillionperfume.com" hreflang="en-us" />
	<link rel="canonical" href="http://sevenbillionperfume.com/"/>
@endsection

@section('sidebar')
    @parent
@endsection

@section('content')                 
    
  <section class="banner_area">
    <div class="banner_inner d-flex align-items-center">       
    
        <div class="container">
            <div class="banner_content">
            <div class="page_link">
                    <a href="{{ route('home') }}">Home</a>
                    <!-- <a href="{{ route('content',$menuslug->uri) }}">{{ $menuslug ? $menuslug->title : ' No data found' }}</a> -->
                </div>
            </div>
        </div>
    </div>
  </section>

  @if($articles!="")
    <section class="contact_area p_120">
      <div class="container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d1834.6026198714721!2d72.4725423645397!3d23.126172171838387!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1s283%2C%20VADSAR%20ROAD%2C%20NEAR%20CANAL%2C%20%20SANTEJ%2C%20AHMEDABAD%2C%20GUJRAT-%20380058!5e0!3m2!1sen!2sbd!4v1711774890471!5m2!1sen!2sbd" style="border:0; width:100%; height:400px"  allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> 
        <div class="row">
              <div class="col-lg-12">
                  <div class="contact_info">
                    {!! $articles->content !!}                      
                  </div>
              </div>
              
          </div>
      </div>
  </section>
    @else
      <h4 style="margin:20%; text-align:center">No data found</h4>
    @endif  
@endsection
@section('footer')
    @parent
@endsection