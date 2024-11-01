
function validate(){
  var varValid=true;
  if (document.getElementById('iname')){
    if (document.getElementById('isms_name_required')){
      if (document.getElementById('isms_name_required').value=='1'){
        if(document.getElementById('iname').value==""){
          document.getElementById('iname').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('iname').style.borderColor = "#ddd";
                   
        }
      }
    }
  }

  if (document.getElementById('ifirstname')){
    if (document.getElementById('isms_first_name_required')){
      if (document.getElementById('isms_first_name_required').value=='1'){
        if(document.getElementById('ifirstname').value==""){
          document.getElementById('ifirstname').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('ifirstname').style.borderColor = "#ddd";
                   
        }
      }
    }
  }


  if (document.getElementById('ilastname')){
    if (document.getElementById('isms_last_name_required')){
      if (document.getElementById('isms_last_name_required').value=='1'){
        if(document.getElementById('ilastname').value==""){
          document.getElementById('ilastname').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('ilastname').style.borderColor = "#ddd";
                   
        }
      }
    }
  }
  
  if (document.getElementById('imobilephone')){
    if (document.getElementById('isms_mobile_phone_required')){
      if (document.getElementById('isms_mobile_phone_required').value=='1'){
        if(document.getElementById('imobilephone').value==""){
          document.getElementById('imobilephone').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('imobilephone').style.borderColor = "#ddd";
                   
        }
      }
    }
  }

  if (document.getElementById('iemail')){
    if (document.getElementById('isms_email_required')){
      if (document.getElementById('isms_email_required').value=='1'){
        if(document.getElementById('iemail').value==""){
          document.getElementById('iemail').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('iemail').style.borderColor = "#ddd";
                   
        }
      }
    }
  }


  if (document.getElementById('ireemail')){
    if (document.getElementById('isms_reemail_required')){
      if (document.getElementById('isms_reemail_required').value=='1'){
        if(document.getElementById('ireemail').value=="" || document.getElementById('ireemail').value!=document.getElementById('iemail').value){
          document.getElementById('ireemail').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('ireemail').style.borderColor = "#ddd";   
        }
      }
    }
  }
  
  
  if (document.getElementById('ireemail')){
    if(document.getElementById('ireemail').value!=document.getElementById('iemail').value){
      document.getElementById('ireemail').style.borderColor = "red";
      varValid=false;
    }else{
      document.getElementById('ireemail').style.borderColor = "#ddd";   
    }    
  }
  
  
  if (document.getElementById('iaddress')){
    if (document.getElementById('isms_address_required')){
      if (document.getElementById('isms_address_required').value=='1'){
        if(document.getElementById('iaddress').value==""){
          document.getElementById('iaddress').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('iaddress').style.borderColor = "#ddd";
            
        }
      }
    }
  }

  if (document.getElementById('icountry')){
    if (document.getElementById('isms_country_required')){
      if (document.getElementById('isms_country_required').value=='1'){
        if(document.getElementById('icountry').value==""){
          document.getElementById('icountry').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('icountry').style.borderColor = "#ddd";
                   
        }
      }
    }
  }

  if (document.getElementById('ipassport')){
    if (document.getElementById('isms_passport_no_required')){
      if (document.getElementById('isms_passport_no_required').value=='1'){
        if(document.getElementById('ipassport').value==""){
          document.getElementById('ipassport').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('ipassport').style.borderColor = "#ddd";
                   
        }
      }
    }
  } 


  if (document.getElementById('isocial_security_no')){
    if (document.getElementById('isms_social_security_no_required')){
      if (document.getElementById('isms_social_security_no_required').value=='1'){
        if(document.getElementById('isocial_security_no').value==""){
          document.getElementById('isocial_security_no').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('isocial_security_no').style.borderColor = "#ddd";
                   
        }
      }
    }
  }
  
  if (document.getElementById('idob')){
    if (document.getElementById('isms_dob_required')){
      if (document.getElementById('isms_dob_required').value=='1'){
        if(document.getElementById('idob').value==""){
          document.getElementById('idob').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('idob').style.borderColor = "#ddd";
                   
        }
      }
    }
  }
  

  if (document.getElementById('icompany')){
    if (document.getElementById('isms_company_required')){
      if (document.getElementById('isms_company_required').value=='1'){
        if(document.getElementById('icompany').value==""){
          document.getElementById('icompany').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('icompany').style.borderColor = "#ddd";
                   
        }
      }
    }
  } 

  if (document.getElementById('iproduct')){
    if (document.getElementById('isms_product_required')){
      if (document.getElementById('isms_product_required').value=='1'){
        if(document.getElementById('iproduct').value==""){
          document.getElementById('iproduct').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('iproduct').style.borderColor = "#ddd";
                   
        }
      }
    }
  }
  
  
    if (document.getElementById('iwebsite')){
    if (document.getElementById('isms_website_required')){
      if (document.getElementById('isms_website_required').value=='1'){
        if(document.getElementById('iwebsite').value==""){
          document.getElementById('iwebsite').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('iwebsite').style.borderColor = "#ddd";
                   
        }
      }
    }
  } 


  if (document.getElementById('cisms_custom_name1')){
    if (document.getElementById('isms_custom1_required')){
      if (document.getElementById('isms_custom1_required').value=='1'){
        if(document.getElementById('cisms_custom_name1').value==""){
          document.getElementById('cisms_custom_name1').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('cisms_custom_name1').style.borderColor = "#ddd";
                   
        }
      }
    }
  } 


  if (document.getElementById('cisms_custom_name2')){
    if (document.getElementById('isms_custom2_required')){
      if (document.getElementById('isms_custom2_required').value=='1'){
        if(document.getElementById('cisms_custom_name2').value==""){
          document.getElementById('cisms_custom_name2').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('cisms_custom_name2').style.borderColor = "#ddd";
                   
        }
      }
    }
  } 

  if (document.getElementById('cisms_custom_name3')){
    if (document.getElementById('isms_custom3_required')){
      if (document.getElementById('isms_custom3_required').value=='1'){
        if(document.getElementById('cisms_custom_name3').value==""){
          document.getElementById('cisms_custom_name3').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('cisms_custom_name3').style.borderColor = "#ddd";
                   
        }
      }
    }
  }
  
  
  if (document.getElementById('cisms_custom_name4')){
    if (document.getElementById('isms_custom4_required')){
      if (document.getElementById('isms_custom4_required').value=='1'){
        if(document.getElementById('cisms_custom_name4').value==""){
          document.getElementById('cisms_custom_name4').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('cisms_custom_name4').style.borderColor = "#ddd";
                   
        }
      }
    }
  }

 if (document.getElementById('cisms_custom_name5')){
    if (document.getElementById('isms_custom5_required')){
      if (document.getElementById('isms_custom5_required').value=='1'){
        if(document.getElementById('cisms_custom_name5').value==""){
          document.getElementById('cisms_custom_name5').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('cisms_custom_name5').style.borderColor = "#ddd";
                   
        }
      }
    }
  }
  
  
 if (document.getElementById('cisms_custom_select_name1')){
    if (document.getElementById('isms_custom_select1_required')){
      if (document.getElementById('isms_custom_select1_required').value=='1'){
        if(document.getElementById('cisms_custom_select_name1').value==""){
          document.getElementById('cisms_custom_select_name1').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('cisms_custom_select_name1').style.borderColor = "#ddd";
                   
        }
      }
    }
  }


 if (document.getElementById('cisms_custom_select_name2')){
    if (document.getElementById('isms_custom_select2_required')){
      if (document.getElementById('isms_custom_select2_required').value=='1'){
        if(document.getElementById('cisms_custom_select_name2').value==""){
          document.getElementById('cisms_custom_select_name2').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('cisms_custom_select_name2').style.borderColor = "#ddd";
                   
        }
      }
    }
  }

 if (document.getElementById('cisms_custom_select_name3')){
    if (document.getElementById('isms_custom_select3_required')){
      if (document.getElementById('isms_custom_select3_required').value=='1'){
        if(document.getElementById('cisms_custom_select_name3').value==""){
          document.getElementById('cisms_custom_select_name3').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('cisms_custom_select_name3').style.borderColor = "#ddd";
                   
        }
      }
    }
  }


 if (document.getElementById('isubject')){
    if (document.getElementById('isms_subject_required')){
      if (document.getElementById('isms_subject_required').value=='1'){
        if(document.getElementById('isubject').value==""){
          document.getElementById('isubject').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('isubject').style.borderColor = "#ddd";
                   
        }
      }
    }
  }


 if (document.getElementById('imessage')){
    if (document.getElementById('imessage_required')){
      if (document.getElementById('imessage_required').value=='1'){
        if(document.getElementById('imessage').value==""){
          document.getElementById('imessage').style.borderColor = "red";
          varValid=false;
        }else{
          document.getElementById('imessage').style.borderColor = "#ddd";
                   
        }
      }
    } 
  }
  
  if (document.getElementById('captchatxt')){
    if(document.getElementById('captchatxt').value==""){
      document.getElementById('captchatxt').style.borderColor = "red";
      varValid=false;
    }else{
      document.getElementById('captchatxt').style.borderColor = "#ddd";
               
    }
  } 
  
  //alert(varValid); 
  return varValid; 
}
