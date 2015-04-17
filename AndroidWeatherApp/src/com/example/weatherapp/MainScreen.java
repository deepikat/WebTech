package com.example.weatherapp;

import java.io.IOException;
import java.io.UnsupportedEncodingException;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.util.EntityUtils;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;


import android.os.AsyncTask;
import android.os.Bundle;
import android.annotation.SuppressLint;
//import android.app.ActionBar.LayoutParams;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.service.textservice.SpellCheckerService.Session;
import android.util.Log;
import android.view.Menu;
import android.view.View;
//import android.webkit.WebView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.TableRow;
import android.widget.Toast;
//import android.widget.LinearLayout;
//import android.widget.RelativeLayout;
import android.widget.TableLayout;
//import android.widget.TableRow;
import android.widget.TextView;

import com.facebook.FacebookException;
import com.facebook.FacebookOperationCanceledException;
import com.facebook.LoggingBehavior;
//import com.facebook.Session;
import com.facebook.SessionState;
import com.facebook.Settings;
import com.facebook.android.DialogError;
import com.facebook.android.Facebook;
import com.facebook.android.Facebook.DialogListener;
import com.facebook.android.FacebookError;
import com.facebook.widget.WebDialog;
import com.facebook.widget.WebDialog.OnCompleteListener;

import com.facebook.Session.StatusCallback;


public class MainScreen extends Activity 
{
	final Context context = this;
	String APP_ID=null;
	Facebook fb;
	
	JSONObject jobj=null;
    String condition=null ,fore_day=null, fore_high=null,fore_low=null,fore_text=null;
    String text_att=null, temp_att=null,img_att=null;
    String city_att=null,region_att=null,country_att=null;
    
	@Override
	protected void onCreate(Bundle savedInstanceState) 
    {
		
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main_screen);
        
        APP_ID=getString(R.string.APP_ID);
    	fb= new Facebook(APP_ID);
        
        
        
        Button searchButton = (Button) findViewById(R.id.bSearch);
	     
	    searchButton.setOnClickListener(new View.OnClickListener() 
	    {
			
			@SuppressLint("NewApi")
			@Override
			public void onClick(View v) 
			{
				// TODO Auto-generated method stub
				//just call the asynctask and pass on the URL and close onCreate
				
				
				 
				// find the radiobutton by returned id
				RadioGroup radioGroup = (RadioGroup) findViewById(R.id.radioGroup1);			
				int selectedId = radioGroup.getCheckedRadioButtonId();
				
			
				String url2 = "http://cs-server.usc.edu:25322/assignment8/myServletWeather?loc=90007&Unit=f";
				
				
				new loadData().execute(url2);
			
				EditText locationBox=(EditText) findViewById(R.id.editText1);
				String loc=locationBox.getText().toString();
				
				Log.d(null, loc);
			}
		});
        
    }
	
	@Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main_screen, menu);
        return true;
    }


	public class loadData extends AsyncTask<String, Void, String>
    {

    	protected String doInBackground(String... params)
    	{
    		String output = null;
    		HttpResponse httpResponse = null;
    		String url = params[0];
    		
    		try {
    			DefaultHttpClient httpClient = new DefaultHttpClient();
    			
    			HttpGet httpGet = new HttpGet(url);
    			
    			httpResponse = httpClient.execute(httpGet);
    			
    			int status = httpResponse.getStatusLine().getStatusCode();
    			
    			if(status == 200)
    			{
    				Log.d(null, "SUCCESS");
    			}
    			HttpEntity httpEntity = httpResponse.getEntity();
    			
    			output = EntityUtils.toString(httpEntity);
    			
    		}catch(UnsupportedEncodingException e){
    			e.printStackTrace();
    		}catch(ClientProtocolException e){
    			e.printStackTrace();
    		}catch(IOException e){
    			e.printStackTrace();
    		}
    		
    		return output;
    	}
    	
    	protected void onPostExecute(String result) 
    	{
    		final TableLayout table_fore = (TableLayout)findViewById(R.id.RHE);
    		table_fore.setBackgroundColor(0xff000000);
            final TextView tv1 = (TextView) findViewById(R.id.day);
    	    final TextView tv2=  (TextView) findViewById(R.id.weather);
    	    final TextView tv3 = (TextView) findViewById(R.id.high);
    	    final TextView tv4=  (TextView) findViewById(R.id.low);
    	   final TextView cityText=(TextView) findViewById(R.id.textView1);
    	   final TextView regionText=(TextView) findViewById(R.id.textView2);
    	    
    	    
	        JSONArray fore_arr;
	      try 
	      {
			jobj= new JSONObject(result);
			
			city_att = jobj.getJSONObject("weather").getJSONObject("location").getString("city");
			region_att = jobj.getJSONObject("weather").getJSONObject("location").getString("region");
			country_att = jobj.getJSONObject("weather").getJSONObject("location").getString("country");
			
			img_att = jobj.getJSONObject("weather").getString("img");
			
			text_att = jobj.getJSONObject("weather").getJSONObject("condition").getString("text");
			temp_att = jobj.getJSONObject("weather").getJSONObject("condition").getString("temp");
			fore_arr = jobj.getJSONObject("weather").getJSONArray("forecast");
			
			table_fore.setBackgroundColor(0x00000000);
			cityText.setText(city_att);
			cityText.setTextColor(0xFFFFFFFF);
			cityText.setTextSize(30);
			
			StringBuilder stringBuilder = new StringBuilder();

			 stringBuilder.append(region_att);
			 stringBuilder.append(",");
			 stringBuilder.append(country_att);
			 

			String finalString = stringBuilder.toString();
			
			regionText.setText(finalString);
			regionText.setTextColor(0xFFFFFFFF);
			regionText.setTextSize(20);
			

			tv1.setText("Day");
			tv1.setBackgroundResource(getResources().getColor(R.color.gray));
			tv2.setText("Weather");
			tv2.setBackgroundResource(getResources().getColor(R.color.gray));
			tv3.setText("High");
			tv3.setBackgroundResource(getResources().getColor(R.color.gray));
			tv4.setText("Low");
			tv4.setBackgroundResource(getResources().getColor(R.color.gray));
			for(int fore_iter= 0;fore_iter < fore_arr.length();fore_iter++)
			{

				JSONObject obj = fore_arr.getJSONObject(fore_iter);
				fore_day = obj.getString("day");
				fore_high = obj.getString("high");
				fore_low = obj.getString("low");
				fore_text = obj.getString("text");
				
				 TextView tV_txt1=null;
				 TextView tV_txt2=null;
				 TextView tV_txt3=null;
				 TextView tV_txt4=null;
				if(fore_iter==0)
				{
					tV_txt1 = (TextView) findViewById(R.id.day1);
					tV_txt2 =(TextView) findViewById(R.id.weather1);
				     tV_txt3 = (TextView) findViewById(R.id.high1);
				    tV_txt4 = (TextView) findViewById(R.id.low1);		
					
					
				}
				
				if(fore_iter==1)
				{

					tV_txt1 = (TextView) findViewById(R.id.day2);					
					 tV_txt2 =(TextView) findViewById(R.id.weather2);
				     tV_txt3 = (TextView) findViewById(R.id.high2);
				     tV_txt4 = (TextView) findViewById(R.id.low2);
				}
				
				if(fore_iter==2)
				{
					tV_txt1 = (TextView) findViewById(R.id.day3);
					 tV_txt2 =(TextView) findViewById(R.id.weather3);
				     tV_txt3 = (TextView) findViewById(R.id.high3);
				     tV_txt4 = (TextView) findViewById(R.id.low3);
				
				}
				
				if(fore_iter==3)
				{
					tV_txt1 = (TextView) findViewById(R.id.day4);
					 tV_txt2 =(TextView) findViewById(R.id.weather4);
				     tV_txt3 = (TextView) findViewById(R.id.high4);
				     tV_txt4 = (TextView) findViewById(R.id.low4);
				  
				}
				
				
				if(fore_iter==4)
				{
					tV_txt1 = (TextView) findViewById(R.id.day5);
					 tV_txt2 =(TextView) findViewById(R.id.weather5);
				     tV_txt3 = (TextView) findViewById(R.id.high5);
				     tV_txt4 = (TextView) findViewById(R.id.low5);
				  
				}
				
				
				
				tV_txt1.setText(fore_day);
				tV_txt2.setText(fore_text);
				tV_txt3.setText(fore_high);
				tV_txt4.setText(fore_low);
				
				if(fore_iter==1 || fore_iter==3)
				{
					 tV_txt1.setBackgroundColor(getResources().getColor(R.color.aqua));
				     tV_txt2.setBackgroundColor(getResources().getColor(R.color.aqua));
				     tV_txt3.setBackgroundColor(getResources().getColor(R.color.aqua));
				     tV_txt4.setBackgroundColor(getResources().getColor(R.color.aqua));
				}
				else
				{
				  tV_txt1.setBackgroundColor(0xFFFFFFFF);
				     tV_txt2.setBackgroundColor(0xFFFFFFFF);
				     tV_txt3.setBackgroundColor(0xFFFFFFFF);
				     tV_txt4.setBackgroundColor(0xFFFFFFFF);
				}
				     
				     tV_txt3.setTextColor(0xFFFF0000);
				     tV_txt4.setTextColor(0xff0000ff);
				
			}
			
			condition= jobj.getJSONObject("weather").getJSONObject("condition").getString("text");
			TextView shareCurr=(TextView) findViewById(R.id.textView3);
			shareCurr.setText("Share Current Weather");
			shareCurr.setTextColor(0xFFFFFFFF);
			  
			shareCurr.setOnClickListener(new View.OnClickListener() {   
             	  public void onClick(View v) {
             		  
             		  //added 12:07 to check the dialog
             		  
             		
             		  
             		 AlertDialog.Builder builder1 = new AlertDialog.Builder(context);
                     builder1.setMessage("Write your message here.");
                     builder1.setCancelable(true);
                     
                     builder1.setPositiveButton("Post Current Weather", new DialogInterface.OnClickListener() {
						
						@Override
						public void onClick(DialogInterface dialog, int which) {
							// TODO Auto-generated method stub
							getSession(1);
							dialog.cancel();
						}
					});
                
                     builder1.setNegativeButton("Cancel",
                             new DialogInterface.OnClickListener() {
                         public void onClick(DialogInterface dialog, int id) {
                             dialog.cancel();
                         }
                     });

                     AlertDialog alert11 = builder1.create();
                     alert11.show();

             		  //till here
             	  
             	  }
			});
			
			
			TextView shareFore=(TextView) findViewById(R.id.textView4);
			shareFore.setText("Share Current Weather");
			shareFore.setTextColor(0xFFFFFFFF);
			

		} 
	    catch (JSONException e) 
	    {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
   	}

    }
 
	//getSession function for the fb integration
	
	
	 @SuppressWarnings("deprecation")
	public void getSession(final int flag) 
	 {
		 
		 if(fb.isSessionValid()){
			 //logout
		 }
		 else
		 {
			 //login
			 
			 fb.authorize(MainScreen.this, new DialogListener()
			 {

				@Override
				public void onComplete(Bundle values) {
					// TODO Auto-generated method stub
					
					Toast.makeText(MainScreen.this, "Yay", Toast.LENGTH_SHORT).show();
					
					
				}

				@Override
				public void onFacebookError(FacebookError e) {
					// TODO Auto-generated method stub
					Toast.makeText(MainScreen.this, "onFaceBookError", Toast.LENGTH_SHORT).show();
					
				}

				@Override
				public void onError(DialogError e) {
					// TODO Auto-generated method stub
					Toast.makeText(MainScreen.this, "DialogError", Toast.LENGTH_SHORT).show();
					
				}

				@Override
				public void onCancel() {
					// TODO Auto-generated method stub
					Toast.makeText(MainScreen.this, "OnCancel", Toast.LENGTH_SHORT).show();
					
				}
				 
			 });
			 
		 }

	 }
	 
	 /*@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		// TODO Auto-generated method stub
		super.onActivityResult(requestCode, resultCode, data);
		fb.authorizeCallback(requestCode, resultCode, data);
	}*/
}
