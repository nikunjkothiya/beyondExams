<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Language;

class UtilController extends Controller
{
    protected $code = array();

    public function __construct(){
        $languages = Language::all();
        foreach($languages as $lang){
        	$this->code[] = $lang->code;
        }
    }

    public function locale($locale){
    	if(in_array($locale, $this->code)){
    		\App::setLocale($locale);
            session()->put('locale', $locale);
        	return redirect()->back();
    	}
    	else{
    		\App::setLocale('en');
            session()->put('locale', 'en');
        	return redirect()->back();	
    	}
    }
}
